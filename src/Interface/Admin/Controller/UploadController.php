<?php

declare(strict_types=1);

namespace App\Interface\Admin\Controller;

use App\Interface\Common\Attribute\Route;
use Tracy\Debugger;

class UploadController
{
    #[Route(path: '/admin/upload-image', method: 'POST')]
    public function uploadImage(): void
    {

        $uploadDir = STORAGE_PATH . '/uploads/';
        $uploadUrl = '/storage/uploads/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!isset($_FILES['file'])) {
            Debugger::log('Erreur : Aucun fichier reçu.', Debugger::ERROR);
            http_response_code(400);
            echo json_encode(['error' => 'Aucun fichier reçu.']);
            return;
        }

        $file = $_FILES['file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Debugger::log('Erreur lors de l\'upload : ' . $file['error'], Debugger::ERROR);
            http_response_code(400);
            echo json_encode(['error' => 'Erreur lors de l\'upload.']);
            return;
        }

        $filename = uniqid() . '-' . basename($file['name']);
        $filePath = $uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            Debugger::log('Erreur : Impossible de déplacer le fichier vers ' . $filePath, Debugger::ERROR);
            http_response_code(500);
            echo json_encode(['error' => 'Impossible de sauvegarder le fichier.']);
            return;
        }


        header('Content-Type: application/json');
        echo json_encode(['location' => $uploadUrl . $filename]);
    }
}
