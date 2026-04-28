<?php
require __DIR__ . '/../../config/conexao.php';

$data = json_decode(file_get_contents('php://input'), true);
$inicio = new DateTime($data['inicio']);
$fim = new DateTime($data['fim']);
$descricao = trim((string) ($data['descricao'] ?? 'Agendamento'));
$dataTat = $inicio->format('Y-m-d');
$horaInicio = $inicio->format('H:i:s');
$horaFim = $fim->format('H:i:s');

$stmt = $conn->prepare('INSERT INTO tatuagens (cliente_id, descricao, valor, data_tatuagem, hora_inicio, hora_fim, status) VALUES (NULL, ?, 0.00, ?, ?, ?, "agendado")');
$stmt->bind_param('ssss', $descricao, $dataTat, $horaInicio, $horaFim);
$stmt->execute();
$stmt->close();
