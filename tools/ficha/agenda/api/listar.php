<?php
require __DIR__ . '/../../config/conexao.php';
header('Content-Type: application/json; charset=utf-8');

$sql = '
    SELECT
        id,
        descricao AS title,
        CONCAT(data_tatuagem, "T", hora_inicio) AS start,
        CONCAT(data_tatuagem, "T", hora_fim) AS end,
        status
    FROM tatuagens
';

$result = $conn->query($sql);
$cores = [
    'agendado' => '#3788d8',
    'confirmado' => '#28a745',
    'cancelado' => '#dc3545',
    'concluido' => '#6c757d'
];
$eventos = [];

while ($row = $result->fetch_assoc()) {
    $row['color'] = $cores[$row['status']] ?? '#3788d8';
    $eventos[] = $row;
}

echo json_encode($eventos);
