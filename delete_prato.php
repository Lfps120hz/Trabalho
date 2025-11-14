<?php
require __DIR__ . '/db.php';

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST ?? $_GET;

$id = isset($input['id']) ? (int)$input['id'] : 0;
if ($id <= 0) {
    jsonResponse(false, 'ID inválido', null, 400);
}

try {
    $stmt = $pdo->prepare('DELETE FROM pratos WHERE id = :id');
    $stmt->execute([':id' => $id]);
    if ($stmt->rowCount() === 0) {
        jsonResponse(false, 'Prato não encontrado', null, 404);
    }
    jsonResponse(true, 'Prato removido');
} catch (Exception $e) {
    jsonResponse(false, 'Erro ao remover prato', null, 500);
}