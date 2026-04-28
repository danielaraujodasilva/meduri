<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Agenda de Tatuagens</title>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #111827; color: #fff; }
#calendar { background: #1f2937; padding: 16px; border-radius: 16px; }
</style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
      <h1 class="h3 mb-1">Agenda de tatuagens</h1>
      <p class="text-secondary mb-0">Clique e arraste para criar ou ajustar horarios.</p>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-light" href="../index.php">Nova ficha</a>
      <a class="btn btn-outline-info" href="../public/clientes.php">Clientes</a>
    </div>
  </div>

  <div id="calendar"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
    locale: 'pt-br',
    initialView: 'timeGridWeek',
    timeZone: 'local',
    selectable: true,
    editable: true,
    height: 'auto',
    events: 'api/listar.php',
    select: function (info) {
      const titulo = prompt('Descricao da tattoo:');
      if (!titulo) {
        return;
      }

      fetch('api/salvar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          inicio: info.startStr,
          fim: info.endStr,
          descricao: titulo
        })
      }).then(() => calendar.refetchEvents());
    },
    eventDrop: atualizar,
    eventResize: atualizar,
    eventClick: function (info) {
      if (!confirm('Excluir esse agendamento?')) {
        return;
      }

      fetch('api/deletar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: info.event.id })
      }).then(() => info.event.remove());
    }
  });

  function atualizar(info) {
    fetch('api/atualizar.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        id: info.event.id,
        inicio: info.event.startStr,
        fim: info.event.endStr
      })
    });
  }

  calendar.render();
});
</script>
</body>
</html>
