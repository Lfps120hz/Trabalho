<?php
require __DIR__ . '/db.php';

// suporta JSON no body ou form-data
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$nome = trim($input['nome'] ?? '');
$descricao = trim($input['descricao'] ?? '');
$preco = isset($input['preco']) ? floatval($input['preco']) : null;
$disponivel = isset($input['disponivel']) ? (bool)$input['disponivel'] : true;

// Validações
if (empty($nome) || strlen($nome) > 255) {
    jsonResponse(false, 'Nome inválido (máx 255 caracteres)', null, 400);
}
if ($preco === null || $preco < 0) {
    jsonResponse(false, 'Preço inválido', null, 400);
}

try {
    $stmt = $pdo->prepare('INSERT INTO pratos (nome, descricao, preco, disponivel) VALUES (:nome, :descricao, :preco, :disponivel)');
    $stmt->execute([
        ':nome' => $nome,
        ':descricao' => $descricao,
        ':preco' => $preco,
        ':disponivel' => $disponivel ? 1 : 0,
    ]);
    jsonResponse(true, 'Prato criado', ['id' => (int)$pdo->lastInsertId()], 201);
} catch (Exception $e) {
    jsonResponse(false, 'Erro ao criar prato', null, 500);
}