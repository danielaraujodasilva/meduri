<?php
require __DIR__ . '/../../config/conexao.php';

$data = json_decode(file_get_contents('php://input'), true);
$inicio = new DateTime($data['inicio']);
$fim = new DateTime($data['fim']);

$stmt = $conn->prepare('INSERT INTO tatuagens (descricao, data_tatuagem, hora_inicio, hora_fim, status, cliente_id, valor) VALUES (?, ?, ?, ?, "agendado", 1, 0.00)');
$descricao = trim((string) ($data['descricao'] ?? 'Agendamento'));
$dataTat = $inicio->format('Y-m-d');
$horaInicio = $inicio->format('H:i:s');
$horaFim = $fim->format('H:i:s');
$stmt->bind_param('ssss', $descricao, $dataTat, $horaInicio, $horaFim);
$stmt->execute();
$stmt->close();
