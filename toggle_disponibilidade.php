<?php
require __DIR__ . '/db.php';

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST ?? $_GET;

$id = isset($input['id']) ? (int)$input['id'] : 0;
if ($id <= 0) {
    jsonResponse(false, 'ID inválido', null, 400);
}

try {
    // Otimização: usar UPDATE com NOT lógico em vez de 2 queries
    $stmt = $pdo->prepare('UPDATE pratos SET disponivel = (CASE WHEN disponivel = 1 THEN 0 ELSE 1 END) WHERE id = :id RETURNING disponivel');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    
    if (!$row) {
        jsonResponse(false, 'Prato não encontrado', null, 404);
    }
    
    $novoStatus = (bool)$row['disponivel'];
    jsonResponse(true, 'Disponibilidade atualizada', ['disponivel' => $novoStatus]);
} catch (Exception $e) {
    jsonResponse(false, 'Erro ao alternar disponibilidade', null, 500);
}