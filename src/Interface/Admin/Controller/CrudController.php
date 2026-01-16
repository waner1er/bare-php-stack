<?php

declare(strict_types=1);

namespace App\Interface\Admin\Controller;

use App\Interface\Common\Attribute\Route;
use App\Interface\Common\BaseController;
use App\Infrastructure\Middleware\AdminMiddleware;
use App\Infrastructure\Middleware\CsrfMiddleware;
use App\Infrastructure\Session\Session;
use App\Infrastructure\Database\Database;

class CrudController extends BaseController
{
    private static array $resources = [];
    private static bool $autoDiscovered = false;

    /**
     * Auto-découverte des CrudResources
     */
    private static function autoDiscover(): void
    {
        if (self::$autoDiscovered) {
            return;
        }

        $crudDir = INTERFACE_PATH . '/Admin/Crud';

        if (!is_dir($crudDir)) {
            self::$autoDiscovered = true;
            return;
        }

        $files = glob($crudDir . '/*Resource.php');

        foreach ($files as $file) {
            $className = basename($file, '.php');
            $fullClassName = "App\\Interface\\Admin\\Crud\\{$className}";

            if (class_exists($fullClassName)) {
                // Extraire le nom de la resource (ex: PostResource -> posts)
                $resourceName = strtolower(str_replace('Resource', '', $className)) . 's';
                self::$resources[$resourceName] = $fullClassName;
            }
        }

        self::$autoDiscovered = true;
    }

    /**
     * Enregistre un CrudResource manuellement
     */
    public static function register(string $name, string $resourceClass): void
    {
        self::$resources[$name] = $resourceClass;
    }

    /**
     * Retourne tous les CRUD enregistrés
     */
    public static function getResources(): array
    {
        self::autoDiscover();
        return self::$resources;
    }

    #[Route('/admin/crud/{resource}', 'GET')]
    public function index(string $resource): void
    {
        AdminMiddleware::handle();

        self::autoDiscover();

        if (!isset(self::$resources[$resource])) {
            http_response_code(404);
            echo "Resource CRUD non trouvée : {$resource}";
            exit;
        }

        $resourceClass = self::$resources[$resource];
        $crudResource = new $resourceClass();

        // Récupérer les entités
        $entityClass = $crudResource->getEntityClass();
        $entities = $entityClass::all();

        $action = $_GET['action'] ?? 'list';
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;

        if ($action === 'edit' && $id) {
            $entity = $entityClass::find($id);
            $formHtml = $crudResource->renderForm($entity);
            $this->renderCrud($crudResource, $formHtml, $resource, 'edit', $id);
        } elseif ($action === 'create') {
            $formHtml = $crudResource->renderForm();
            $this->renderCrud($crudResource, $formHtml, $resource, 'create');
        } elseif ($action === 'delete' && $id) {
            $entity = $entityClass::find($id);
            if ($entity && $entity->delete()) {
                Session::flash('success', 'Suppression effectuée avec succès');
            } else {
                Session::flash('error', 'Erreur lors de la suppression');
            }
            header("Location: /admin/crud/{$resource}");
            exit;
        } else {
            $tableHtml = $crudResource->renderTable($entities);
            $this->renderCrud($crudResource, $tableHtml, $resource, 'list');
        }
    }

    #[Route('/admin/crud/{resource}/store', 'POST')]
    public function store(string $resource): void
    {
        AdminMiddleware::handle();
        CsrfMiddleware::handle();

        // Auto-découverte des resources
        self::autoDiscover();

        if (!isset(self::$resources[$resource])) {
            http_response_code(404);
            exit;
        }

        $resourceClass = self::$resources[$resource];
        $crudResource = new $resourceClass();
        $entityClass = $crudResource->getEntityClass();
        $tableName = $this->getTableName($entityClass);

        $id = $_POST['id'] ?? null;
        $data = $_POST;
        unset($data['_token']);

        // Debug avec Tracy
        \Tracy\Debugger::barDump($data, 'POST Data');
        \Tracy\Debugger::barDump($id, 'ID');

        if ($id) {
            // Update - charger l'entité existante
            $entity = $entityClass::find((int) $id);
            if (!$entity) {
                Session::flash('error', 'Entité introuvable');
                header("Location: /admin/crud/{$resource}");
                exit;
            }
            unset($data['id']);
        } else {
            // Create - nouvelle entité
            $entity = new $entityClass();
        }

        // Récupérer tous les inputs pour détecter les booléens et valider
        $inputs = $crudResource->inputs();
        $booleanFields = [];
        $numberFields = [];
        $errors = [];

        foreach ($inputs as $input) {
            $fieldName = $input->getName();
            $fieldValue = $data[$fieldName] ?? null;

            // Vérifier si le champ est requis
            $reflection = new \ReflectionClass($input);
            $requiredProperty = $reflection->getProperty('required');
            $requiredProperty->setAccessible(true);
            $isRequired = $requiredProperty->getValue($input);

            if ($isRequired && (empty($fieldValue) && $fieldValue !== '0')) {
                $errors[$fieldName] = "Le champ '{$input->getLabel()}' est requis";
            }

            // Détecter les NumberInput
            if ($input instanceof \App\Application\Service\Crud\Input\NumberInput) {
                $numberFields[] = $fieldName;
            }

            // Détecter les SelectInput avec options 0/1 (booléens)
            if ($input instanceof \App\Application\Service\Crud\Input\SelectInput) {
                $optionsProperty = $reflection->getProperty('options');
                $optionsProperty->setAccessible(true);
                $options = $optionsProperty->getValue($input);

                // Si c'est un select avec [0 => 'Non', 1 => 'Oui'], c'est un booléen
                if (isset($options[0]) && isset($options[1]) && count($options) === 2) {
                    $booleanFields[] = $fieldName;
                }
            }
        }

        // Si des erreurs de validation, rediriger avec les erreurs
        if (!empty($errors)) {
            Session::flash('error', 'Erreurs de validation :');
            Session::flash('validation_errors', $errors);
            Session::flash('old_input', $data);

            if ($id) {
                header("Location: /admin/crud/{$resource}?action=edit&id={$id}");
            } else {
                header("Location: /admin/crud/{$resource}?action=create");
            }
            exit;
        }

        // Mise à jour des propriétés via les setters
        foreach ($data as $key => $value) {
            // Convertir snake_case en camelCase pour le nom du setter
            $camelKey = str_replace('_', '', ucwords($key, '_'));
            $setter = 'set' . $camelKey;
            \Tracy\Debugger::barDump(['key' => $key, 'value' => $value, 'setter' => $setter], 'Field Processing');
            if (method_exists($entity, $setter)) {
                // Convertir en booléen si nécessaire
                if (in_array($key, $booleanFields)) {
                    $value = (bool) (int) $value;
                }
                // Convertir les champs numériques en int
                if (in_array($key, $numberFields) && $value !== '' && $value !== null) {
                    $value = (int) $value;
                }
                // Convertir les champs _id en int ou null
                if (str_ends_with($key, '_id') && ($value === '' || $value === '0')) {
                    $value = null;
                } elseif (str_ends_with($key, '_id') && $value !== null) {
                    $value = (int) $value;
                }
                \Tracy\Debugger::barDump(['setter' => $setter, 'converted_value' => $value], 'Calling Setter');
                $entity->$setter($value);
            } else {
                \Tracy\Debugger::barDump($setter, 'Method does not exist');
            }
        }

        // Mettre les champs booléens absents à false (checkbox non cochés)
        foreach ($booleanFields as $boolField) {
            if (!isset($data[$boolField])) {
                $setter = 'set' . ucfirst($boolField);
                if (method_exists($entity, $setter)) {
                    $entity->$setter(false);
                }
            }
        }

        // Sauvegarder via la méthode save() de l'entité
        if ($entity->save()) {
            Session::flash('success', 'Enregistrement effectué avec succès');
        } else {
            Session::flash('error', 'Erreur lors de l\'enregistrement');
        }

        header("Location: /admin/crud/{$resource}");
        exit;
    }

    private function renderCrud($crudResource, string $content, string $resource, string $mode = 'list', ?int $id = null): void
    {
        $this->render('crud.index', [
            'title' => $crudResource->getTitle(),
            'singularTitle' => $crudResource->getSingularTitle(),
            'content' => $content,
            'resource' => $resource,
            'mode' => $mode,
            'id' => $id,
        ]);
    }

    private function getTableName(string $entityClass): string
    {
        $className = basename(str_replace('\\', '/', $entityClass));
        return strtolower($className) . 's';
    }
}
