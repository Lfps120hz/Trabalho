<?php
require __DIR__ . '/db.php';

try {
    $stmt = $pdo->query('SELECT id, nome, descricao, preco, disponivel FROM pratos ORDER BY id ASC');
    $pratos = $stmt->fetchAll();
    jsonResponse(true, 'Pratos listados', $pratos);
} catch (Exception $e) {
    jsonResponse(false, 'Erro ao buscar pratos', null, 500);
}