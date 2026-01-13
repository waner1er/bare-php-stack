<?php
// Vérifiez si un fichier a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $uploadDir = __DIR__ . '/uploads/';
    $uploadUrl = '/uploads/';
    $file = $_FILES['file'];

    // Vérifiez les erreurs
    if ($file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['error' => 'Erreur lors de l\'upload.']);
        exit;
    }

    // Vérifiez si le répertoire uploads existe, sinon créez-le
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Vérifiez si le répertoire uploads est accessible en écriture
    if (!is_writable($uploadDir)) {
        echo json_encode(['error' => 'Le dossier uploads n\'est pas accessible en écriture.']);
        exit;
    }

    // Test temporaire pour vérifier les permissions
    file_put_contents($uploadDir . 'test.txt', 'Test de permissions');

    // Générer un nom de fichier unique
    $filename = uniqid() . '-' . basename($file['name']);
    $filePath = $uploadDir . $filename;

    // Déplacez le fichier uploadé
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        http_response_code(500);
        echo json_encode(['error' => 'Impossible de sauvegarder le fichier.']);
        exit;
    }

    // Retournez l'URL de l'image
    echo json_encode(['location' => $uploadUrl . $filename]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Aucun fichier reçu.']);
