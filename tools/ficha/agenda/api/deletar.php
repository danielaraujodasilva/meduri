<?php
require __DIR__ . '/../../config/conexao.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = (int) ($data['id'] ?? 0);

$stmt = $conn->prepare('DELETE FROM tatuagens WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->close();
