<?php
if (!isset($_GET['id'])) {
    die('Cliente nao especificado.');
}

require __DIR__ . '/config/conexao.php';

$id = (int) $_GET['id'];
$stmt = $conn->prepare('SELECT * FROM clientes WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cliente) {
    die('Cliente nao encontrado.');
}

$stmtTat = $conn->prepare('SELECT descricao, valor, data_tatuagem, hora_inicio, hora_fim, status FROM tatuagens WHERE cliente_id = ? ORDER BY data_tatuagem DESC, hora_inicio DESC');
$stmtTat->bind_param('i', $id);
$stmtTat->execute();
$tatuagens = $stmtTat->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Detalhes de <?php echo htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8'); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-4">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Detalhes do cliente</h1>
      <a class="btn btn-outline-secondary" href="public/clientes.php">Voltar</a>
    </div>

    <div class="card mb-4">
      <div class="card-body">
        <div class="row g-3">
          <div class="col-md-6"><strong>Nome:</strong> <?php echo htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8'); ?></div>
          <div class="col-md-6"><strong>Telefone:</strong> <?php echo htmlspecialchars($cliente['telefone'], ENT_QUOTES, 'UTF-8'); ?></div>
          <div class="col-md-6"><strong>E-mail:</strong> <?php echo htmlspecialchars($cliente['email'], ENT_QUOTES, 'UTF-8'); ?></div>
          <div class="col-md-6"><strong>Data de nascimento:</strong> <?php echo htmlspecialchars((string) $cliente['data_nascimento'], ENT_QUOTES, 'UTF-8'); ?></div>
          <div class="col-md-6"><strong>Genero:</strong> <?php echo htmlspecialchars((string) $cliente['genero'], ENT_QUOTES, 'UTF-8'); ?></div>
          <div class="col-md-6"><strong>Profissao:</strong> <?php echo htmlspecialchars((string) $cliente['profissao'], ENT_QUOTES, 'UTF-8'); ?></div>
          <div class="col-md-12"><strong>Endereco:</strong> <?php echo htmlspecialchars((string) $cliente['endereco'], ENT_QUOTES, 'UTF-8'); ?></div>
          <div class="col-md-6"><strong>Instagram:</strong> <?php echo htmlspecialchars((string) $cliente['instagram_cliente'], ENT_QUOTES, 'UTF-8'); ?></div>
          <div class="col-md-6"><strong>Estilo favorito:</strong> <?php echo htmlspecialchars((string) $cliente['estilo_tatuagem'], ENT_QUOTES, 'UTF-8'); ?></div>
          <div class="col-md-6"><strong>Uso de imagem:</strong> <?php echo (int) $cliente['uso_imagem'] === 1 ? 'Sim' : 'Nao'; ?></div>
          <div class="col-md-6"><strong>Marcacao:</strong> <?php echo (int) $cliente['marcacao'] === 1 ? 'Sim' : 'Nao'; ?></div>
          <div class="col-md-12"><strong>Hobbies:</strong><br><?php echo nl2br(htmlspecialchars((string) $cliente['hobbies'], ENT_QUOTES, 'UTF-8')); ?></div>
          <div class="col-md-12"><strong>Doencas preexistentes:</strong><br><?php echo nl2br(htmlspecialchars((string) $cliente['tem_doencas'], ENT_QUOTES, 'UTF-8')); ?></div>
          <div class="col-md-12"><strong>Uso de medicamentos:</strong><br><?php echo nl2br(htmlspecialchars((string) $cliente['uso_medicamentos'], ENT_QUOTES, 'UTF-8')); ?></div>
          <div class="col-md-12"><strong>Alergias:</strong><br><?php echo nl2br(htmlspecialchars((string) $cliente['alergias'], ENT_QUOTES, 'UTF-8')); ?></div>
          <div class="col-md-12"><strong>Historico de tatuagens:</strong><br><?php echo nl2br(htmlspecialchars((string) $cliente['historico_tatuagens'], ENT_QUOTES, 'UTF-8')); ?></div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h2 class="h5 mb-3">Tatuagens registradas</h2>
        <?php if ($tatuagens->num_rows > 0): ?>
          <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
              <thead>
                <tr>
                  <th>Descricao</th>
                  <th>Valor</th>
                  <th>Data</th>
                  <th>Horario</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php while ($tattoo = $tatuagens->fetch_assoc()): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($tattoo['descricao'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>R$ <?php echo number_format((float) $tattoo['valor'], 2, ',', '.'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($tattoo['data_tatuagem'])); ?></td>
                    <td><?php echo htmlspecialchars(substr((string) $tattoo['hora_inicio'], 0, 5) . ' - ' . substr((string) $tattoo['hora_fim'], 0, 5), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($tattoo['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="mb-0">Nenhuma tatuagem registrada para este cliente.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
