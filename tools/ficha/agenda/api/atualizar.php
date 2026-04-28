<?php
require __DIR__ . '/../../config/conexao.php';

$data = json_decode(file_get_contents('php://input'), true);
$inicio = new DateTime($data['inicio']);
$fim = new DateTime($data['fim']);
$id = (int) ($data['id'] ?? 0);

$stmt = $conn->prepare('UPDATE tatuagens SET data_tatuagem = ?, hora_inicio = ?, hora_fim = ? WHERE id = ?');
$dataTat = $inicio->format('Y-m-d');
$horaInicio = $inicio->format('H:i:s');
$horaFim = $fim->format('H:i:s');
$stmt->bind_param('sssi', $dataTat, $horaInicio, $horaFim, $id);
$stmt->execute();
$stmt->close();
