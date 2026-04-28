<?php
require __DIR__ . '/../config/conexao.php';

$clientes = $conn->query('SELECT * FROM clientes ORDER BY nome ASC');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clientes e Tatuagens</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="../index.php">Ficha Meduri</a>
      <div class="navbar-nav ms-auto gap-2">
        <a class="nav-link" href="../index.php">Cadastrar cliente</a>
        <a class="nav-link" href="cadastrar_tatuagem.php">Cadastrar tatuagem</a>
        <a class="nav-link" href="../agenda/">Agenda</a>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <h1 class="h3 mb-4">Clientes e tatuagens</h1>

    <table class="table table-striped align-middle bg-white">
      <thead>
        <tr>
          <th>Nome completo</th>
          <th>Telefone</th>
          <th>Acoes</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($cliente = $clientes->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($cliente['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td>
              <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $cliente['telefone']); ?>" target="_blank">
                <?php echo htmlspecialchars($cliente['telefone'], ENT_QUOTES, 'UTF-8'); ?>
              </a>
            </td>
            <td>
              <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse" data-bs-target="#tatuagens-<?php echo (int) $cliente['id']; ?>">Ver tatuagens</button>
                <a class="btn btn-sm btn-outline-secondary" href="../detalhes_cliente.php?id=<?php echo (int) $cliente['id']; ?>">Detalhes</a>
              </div>
            </td>
          </tr>
          <tr class="collapse" id="tatuagens-<?php echo (int) $cliente['id']; ?>">
            <td colspan="3">
              <table class="table table-bordered mt-3 mb-0">
                <thead>
                  <tr>
                    <th>Descricao</th>
                    <th>Valor</th>
                    <th>Data</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $stmtTat = $conn->prepare('SELECT descricao, valor, data_tatuagem, status FROM tatuagens WHERE cliente_id = ? ORDER BY data_tatuagem DESC, hora_inicio DESC');
                  $stmtTat->bind_param('i', $cliente['id']);
                  $stmtTat->execute();
                  $tatuagens = $stmtTat->get_result();
                  ?>
                  <?php if ($tatuagens->num_rows > 0): ?>
                    <?php while ($tatuagem = $tatuagens->fetch_assoc()): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($tatuagem['descricao'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>R$ <?php echo number_format((float) $tatuagem['valor'], 2, ',', '.'); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($tatuagem['data_tatuagem'])); ?></td>
                        <td><?php echo htmlspecialchars($tatuagem['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                      </tr>
                    <?php endwhile; ?>
                  <?php else: ?>
                    <tr><td colspan="4">Nenhuma tatuagem encontrada para este cliente.</td></tr>
                  <?php endif; ?>
                  <?php $stmtTat->close(); ?>
                </tbody>
              </table>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
