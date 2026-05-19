<?php

declare(strict_types=1);

namespace App\Interface\Admin\Controller;

use App\Interface\Common\Attribute\Route;
use App\Interface\Common\BaseController;
use App\Infrastructure\Middleware\AdminMiddleware;
use App\Infrastructure\Middleware\CsrfMiddleware;
use App\Infrastructure\Session\Session;

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
                $resourceName = strtolower(str_replace('Resource', '', $className)) . 's';
                self::$resources[$resourceName] = $fullClassName;
            }
        }

        self::$autoDiscovered = true;
    }

    public static function register(string $name, string $resourceClass): void
    {
        self::$resources[$name] = $resourceClass;
    }

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
        $repository = $crudResource->repository();

        $action = $_GET['action'] ?? 'list';
        $id = isset($_GET['id']) ? (int) $_GET['id'] : null;

        if ($action === 'edit' && $id) {
            $entity = $repository->find($id);
            $formHtml = $crudResource->renderForm($entity);
            $this->renderCrud($crudResource, $formHtml, $resource, 'edit', $id);
        } elseif ($action === 'create') {
            $formHtml = $crudResource->renderForm();
            $this->renderCrud($crudResource, $formHtml, $resource, 'create');
        } elseif ($action === 'delete' && $id) {
            $entity = $repository->find($id);
            if ($entity && $repository->delete($entity)) {
                Session::flash('success', 'Suppression effectuée avec succès');
            } else {
                Session::flash('error', 'Erreur lors de la suppression');
            }
            header("Location: /admin/crud/{$resource}");
            exit;
        } else {
            $entities = $repository->findAll();
            $tableHtml = $crudResource->renderTable($entities);
            $this->renderCrud($crudResource, $tableHtml, $resource, 'list');
        }
    }

    #[Route('/admin/crud/{resource}/store', 'POST')]
    public function store(string $resource): void
    {
        AdminMiddleware::handle();
        CsrfMiddleware::handle();

        self::autoDiscover();

        if (!isset(self::$resources[$resource])) {
            http_response_code(404);
            exit;
        }

        $resourceClass = self::$resources[$resource];
        $crudResource = new $resourceClass();
        $repository = $crudResource->repository();
        $entityClass = $crudResource->getEntityClass();

        $id = $_POST['id'] ?? null;
        $data = $_POST;
        unset($data['_token']);

        if ($id) {
            $entity = $repository->find((int) $id);
            if (!$entity) {
                Session::flash('error', 'Entité introuvable');
                header("Location: /admin/crud/{$resource}");
                exit;
            }
            unset($data['id']);
        } else {
            $entity = new $entityClass();
        }

        $inputs = $crudResource->inputs();
        $booleanFields = [];
        $numberFields = [];
        $errors = [];

        foreach ($inputs as $input) {
            $fieldName = $input->getName();
            $fieldValue = $data[$fieldName] ?? null;

            $reflection = new \ReflectionClass($input);
            $requiredProperty = $reflection->getProperty('required');
            $requiredProperty->setAccessible(true);
            $isRequired = $requiredProperty->getValue($input);

            if ($isRequired && (empty($fieldValue) && $fieldValue !== '0')) {
                $errors[$fieldName] = "Le champ '{$input->getLabel()}' est requis";
            }

            if ($input instanceof \App\Application\Service\Crud\Input\NumberInput) {
                $numberFields[] = $fieldName;
            }

            if ($input instanceof \App\Application\Service\Crud\Input\SelectInput) {
                $optionsProperty = $reflection->getProperty('options');
                $optionsProperty->setAccessible(true);
                $options = $optionsProperty->getValue($input);

                // Select [0 => 'Non', 1 => 'Oui'] traité comme un booléen
                if (isset($options[0]) && isset($options[1]) && count($options) === 2) {
                    $booleanFields[] = $fieldName;
                }
            }
        }

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

        foreach ($data as $key => $value) {
            $camelKey = str_replace('_', '', ucwords($key, '_'));
            $setter = 'set' . $camelKey;
            if (method_exists($entity, $setter)) {
                if (in_array($key, $booleanFields)) {
                    $value = (bool) (int) $value;
                }
                if (in_array($key, $numberFields) && $value !== '' && $value !== null) {
                    $value = (int) $value;
                }
                if (str_ends_with($key, '_id') && ($value === '' || $value === '0')) {
                    $value = null;
                } elseif (str_ends_with($key, '_id') && $value !== null) {
                    $value = (int) $value;
                }
                $entity->$setter($value);
            }
        }

        // Checkbox non cochée = absent du POST, on force false
        foreach ($booleanFields as $boolField) {
            if (!isset($data[$boolField])) {
                $setter = 'set' . ucfirst($boolField);
                if (method_exists($entity, $setter)) {
                    $entity->$setter(false);
                }
            }
        }

        if ($repository->save($entity)) {
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

}
