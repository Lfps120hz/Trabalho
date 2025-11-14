<?php
require __DIR__ . '/db.php';

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

$id = isset($input['id']) ? (int)$input['id'] : 0;
if ($id <= 0) {
    jsonResponse(false, 'ID inválido', null, 400);
}

// campos permitidos
$fields = ['nome', 'descricao', 'preco', 'disponivel'];
$updates = [];
$params = [':id' => $id];

foreach ($fields as $f) {
    if (isset($input[$f])) {
        $value = $input[$f];
        
        // Validações
        if ($f === 'nome') {
            $value = trim($value);
            if (empty($value) || strlen($value) > 255) {
                jsonResponse(false, 'Nome inválido (máx 255 caracteres)', null, 400);
            }
        } elseif ($f === 'preco') {
            $value = floatval($value);
            if ($value < 0) {
                jsonResponse(false, 'Preço não pode ser negativo', null, 400);
            }
        } elseif ($f === 'disponivel') {
            $value = (bool)$value ? 1 : 0;
        }
        
        $updates[] = "$f = :$f";
        $params[":$f"] = $value;
    }
}

if (empty($updates)) {
    jsonResponse(false, 'Nenhum campo para atualizar', null, 400);
}

$sql = 'UPDATE pratos SET ' . implode(', ', $updates) . ' WHERE id = :id';

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    if ($stmt->rowCount() === 0) {
        jsonResponse(false, 'Prato não encontrado', null, 404);
    }
    jsonResponse(true, 'Prato atualizado');
} catch (Exception $e) {
    jsonResponse(false, 'Erro ao atualizar prato', null, 500);
}