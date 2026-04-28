<?php
require __DIR__ . '/../config/conexao.php';

$clienteSelecionadoId = isset($_GET['cliente_id']) ? (int) $_GET['cliente_id'] : 0;
$clienteSelecionadoNome = '';

if ($clienteSelecionadoId > 0) {
    $stmtCliente = $conn->prepare('SELECT nome FROM clientes WHERE id = ? LIMIT 1');
    $stmtCliente->bind_param('i', $clienteSelecionadoId);
    $stmtCliente->execute();
    $clienteData = $stmtCliente->get_result()->fetch_assoc();
    $stmtCliente->close();
    if ($clienteData) {
        $clienteSelecionadoNome = $clienteData['nome'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cliente_id'])) {
    header('Content-Type: application/json; charset=utf-8');

    $clienteId = (int) $_POST['cliente_id'];
    $descricao = trim((string) ($_POST['descricao'] ?? ''));
    $valor = (float) ($_POST['valor'] ?? 0);
    $data = trim((string) ($_POST['data_tatuagem'] ?? ''));
    $horaInicio = trim((string) ($_POST['hora_inicio'] ?? ''));
    $horaFim = trim((string) ($_POST['hora_fim'] ?? ''));

    if ($clienteId <= 0 || $descricao === '' || $data === '') {
        echo json_encode(['status' => 'error', 'message' => 'Preencha cliente, descricao e data.']);
        exit;
    }

    $stmt = $conn->prepare('INSERT INTO tatuagens (cliente_id, descricao, valor, data_tatuagem, hora_inicio, hora_fim) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->bind_param('isdsss', $clienteId, $descricao, $valor, $data, $horaInicio, $horaFim);
    $stmt->execute();
    $stmt->close();

    echo json_encode(['status' => 'success', 'message' => 'Agendamento salvo com sucesso.']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Cadastrar Tatuagem</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body { background: #121212; color: #eee; }
.container { max-width: 760px; margin-top: 48px; }
.autocomplete-suggestions { position: absolute; background: #222; border: 1px solid #444; width: 100%; max-height: 200px; overflow-y: auto; z-index: 999; }
.autocomplete-suggestion { padding: 10px; cursor: pointer; }
.autocomplete-suggestion:hover { background: #333; }
</style>
</head>
<body>
<div class="container">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
      <h1 class="h3 mb-1">Cadastrar tatuagem</h1>
      <p class="text-secondary mb-0">Associe o agendamento a um cliente ja cadastrado.</p>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-light" href="../index.php">Nova ficha</a>
      <a class="btn btn-outline-info" href="clientes.php">Clientes</a>
    </div>
  </div>

  <div id="alerta"></div>

  <form id="formTatuagem" class="bg-secondary bg-opacity-25 border border-secondary rounded-4 p-4">
    <div class="mb-3 position-relative">
      <label class="form-label">Cliente</label>
      <input type="text" id="clienteInput" class="form-control" autocomplete="off" required value="<?php echo htmlspecialchars($clienteSelecionadoNome, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="hidden" name="cliente_id" id="clienteId" value="<?php echo $clienteSelecionadoId > 0 ? $clienteSelecionadoId : ''; ?>">
      <div id="clienteSuggestions" class="autocomplete-suggestions" style="display:none;"></div>
    </div>

    <div class="mb-3">
      <label class="form-label">Descricao</label>
      <input type="text" name="descricao" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Valor (R$)</label>
      <input type="number" step="0.01" name="valor" class="form-control" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Data</label>
      <input type="date" name="data_tatuagem" class="form-control" required>
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Hora inicio</label>
        <input type="time" name="hora_inicio" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Hora fim</label>
        <input type="time" name="hora_fim" class="form-control" required>
      </div>
    </div>

    <button class="btn btn-success w-100 mt-4">Salvar agendamento</button>
  </form>
</div>

<script>
$(function () {
  $('#clienteInput').on('input', function () {
    const valor = $(this).val();

    if (valor.length < 2) {
      $('#clienteSuggestions').hide();
      return;
    }

    $.get('buscar_clientes.php', { busca: valor }, function (data) {
      $('#clienteSuggestions').html(data).show();
    });
  });

  $(document).on('click', '.autocomplete-suggestion', function () {
    const id = $(this).data('id');
    if (!id) {
      return;
    }
    $('#clienteInput').val($(this).text());
    $('#clienteId').val(id);
    $('#clienteSuggestions').hide();
  });

  $(document).click(function (event) {
    if (!$(event.target).closest('#clienteInput, #clienteSuggestions').length) {
      $('#clienteSuggestions').hide();
    }
  });

  $('#formTatuagem').submit(function (event) {
    event.preventDefault();

    if (!$('#clienteId').val()) {
      $('#alerta').html('<div class="alert alert-warning">Escolha um cliente valido antes de salvar.</div>');
      return;
    }

    $.ajax({
      url: 'cadastrar_tatuagem.php',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function (response) {
        const classe = response.status === 'success' ? 'success' : 'danger';
        $('#alerta').html('<div class="alert alert-' + classe + '">' + response.message + '</div>');
        if (response.status === 'success') {
          $('#formTatuagem')[0].reset();
          $('#clienteId').val('');
        }
      },
      error: function () {
        $('#alerta').html('<div class="alert alert-danger">Nao foi possivel salvar o agendamento.</div>');
      }
    });
  });
});
</script>
</body>
</html>
