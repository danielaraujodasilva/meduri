<?php
require __DIR__ . '/config/conexao.php';

function posted(string $key, string $default = ''): string {
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
}

function checked(string $key): int {
    return isset($_POST[$key]) ? 1 : 0;
}

$feedback = null;
$feedbackType = 'success';
$clienteId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = posted('nome');
    $email = posted('email');
    $telefone = posted('telefone');
    $dataNascimento = posted('data_nascimento');
    $genero = posted('genero');
    $profissao = posted('profissao');
    $endereco = posted('endereco');
    $hobbies = posted('hobbies');
    $estiloTatuagem = posted('estilo_tatuagem');
    $instagram = posted('instagram_cliente');
    $usoImagem = checked('uso_imagem');
    $marcacao = checked('marcacao');
    $temDoencas = posted('tem_doencas');
    $usoMedicamentos = posted('uso_medicamentos');
    $alergias = posted('alergias');
    $historico = posted('historico_tatuagens');

    if ($nome === '' || $email === '' || $telefone === '') {
        $feedback = 'Preencha pelo menos nome, e-mail e telefone.';
        $feedbackType = 'danger';
    } else {
        $stmt = $conn->prepare('SELECT id FROM clientes WHERE nome = ? AND telefone = ? LIMIT 1');
        $stmt->bind_param('ss', $nome, $telefone);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($existing) {
            $clienteId = (int) $existing['id'];
            $feedback = 'Este cliente ja existe. Voce pode seguir para o cadastro de tatuagem.';
            $feedbackType = 'info';
        } else {
            $stmt = $conn->prepare(
                'INSERT INTO clientes (
                    nome, email, telefone, data_nascimento, genero, profissao, endereco, hobbies,
                    estilo_tatuagem, uso_imagem, autorizou_uso_imagem, marcacao, instagram_cliente,
                    tem_doencas, uso_medicamentos, alergias, historico_tatuagens
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->bind_param(
                'sssssssssiiisssss',
                $nome,
                $email,
                $telefone,
                $dataNascimento,
                $genero,
                $profissao,
                $endereco,
                $hobbies,
                $estiloTatuagem,
                $usoImagem,
                $usoImagem,
                $marcacao,
                $instagram,
                $temDoencas,
                $usoMedicamentos,
                $alergias,
                $historico
            );
            $stmt->execute();
            $clienteId = $stmt->insert_id;
            $stmt->close();

            $feedback = 'Cliente cadastrado com sucesso.';
            $feedbackType = 'success';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ficha de Cliente - Meduri</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light py-5">
  <div class="container" style="max-width: 900px;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
      <div>
        <h1 class="h3 mb-1">Ficha de cliente</h1>
        <p class="text-secondary mb-0">Cadastro base e anamnese para atendimento.</p>
      </div>
      <div class="d-flex gap-2">
        <a class="btn btn-outline-light" href="public/clientes.php">Clientes</a>
        <a class="btn btn-outline-info" href="public/cadastrar_tatuagem.php">Cadastrar tatuagem</a>
        <a class="btn btn-outline-warning" href="agenda/">Agenda</a>
      </div>
    </div>

    <?php if ($feedback): ?>
      <div class="alert alert-<?php echo $feedbackType; ?>">
        <?php echo htmlspecialchars($feedback, ENT_QUOTES, 'UTF-8'); ?>
        <?php if ($clienteId): ?>
          <div class="mt-2">
            <a class="btn btn-sm btn-dark" href="public/cadastrar_tatuagem.php?cliente_id=<?php echo $clienteId; ?>">Adicionar tatuagem para este cliente</a>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <form method="post" class="bg-secondary bg-opacity-25 border border-secondary rounded-4 p-4">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nome</label>
          <input type="text" name="nome" class="form-control" required value="<?php echo htmlspecialchars(posted('nome'), ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">E-mail</label>
          <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars(posted('email'), ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Telefone</label>
          <input type="text" name="telefone" class="form-control" required value="<?php echo htmlspecialchars(posted('telefone'), ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Data de nascimento</label>
          <input type="date" name="data_nascimento" class="form-control" value="<?php echo htmlspecialchars(posted('data_nascimento'), ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Genero</label>
          <select name="genero" class="form-select">
            <option value="">Selecione</option>
            <?php foreach (['Masculino', 'Feminino', 'Outro'] as $opcao): ?>
              <option value="<?php echo $opcao; ?>" <?php echo posted('genero') === $opcao ? 'selected' : ''; ?>><?php echo $opcao; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Profissao</label>
          <input type="text" name="profissao" class="form-control" value="<?php echo htmlspecialchars(posted('profissao'), ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Instagram</label>
          <input type="text" name="instagram_cliente" class="form-control" value="<?php echo htmlspecialchars(posted('instagram_cliente'), ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-12">
          <label class="form-label">Endereco</label>
          <input type="text" name="endereco" class="form-control" value="<?php echo htmlspecialchars(posted('endereco'), ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Hobbies</label>
          <textarea name="hobbies" class="form-control" rows="3"><?php echo htmlspecialchars(posted('hobbies'), ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Estilo de tatuagem favorito</label>
          <textarea name="estilo_tatuagem" class="form-control" rows="3"><?php echo htmlspecialchars(posted('estilo_tatuagem'), ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
      </div>

      <hr class="my-4">
      <h2 class="h5 mb-3">Anamnese</h2>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Possui alguma doenca preexistente?</label>
          <textarea name="tem_doencas" class="form-control" rows="3"><?php echo htmlspecialchars(posted('tem_doencas'), ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Usa algum medicamento atualmente?</label>
          <textarea name="uso_medicamentos" class="form-control" rows="3"><?php echo htmlspecialchars(posted('uso_medicamentos'), ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Tem alergias?</label>
          <textarea name="alergias" class="form-control" rows="3"><?php echo htmlspecialchars(posted('alergias'), ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Historico de outras tatuagens</label>
          <textarea name="historico_tatuagens" class="form-control" rows="3"><?php echo htmlspecialchars(posted('historico_tatuagens'), ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
      </div>

      <div class="form-check mt-4">
        <input class="form-check-input" type="checkbox" name="uso_imagem" id="uso_imagem" <?php echo checked('uso_imagem') ? 'checked' : ''; ?>>
        <label class="form-check-label" for="uso_imagem">Autorizo o uso de fotos e videos.</label>
      </div>
      <div class="form-check mt-2 mb-4">
        <input class="form-check-input" type="checkbox" name="marcacao" id="marcacao" <?php echo checked('marcacao') ? 'checked' : ''; ?>>
        <label class="form-check-label" for="marcacao">Gostaria de ser marcado nas redes sociais.</label>
      </div>

      <button type="submit" class="btn btn-success w-100">Salvar cadastro</button>
    </form>
  </div>
</body>
</html>
