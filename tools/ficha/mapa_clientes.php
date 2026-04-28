<?php
require __DIR__ . '/config/conexao.php';

$result = $conn->query('SELECT id, nome, telefone, endereco FROM clientes WHERE endereco IS NOT NULL AND endereco <> "" ORDER BY nome ASC');
$clientes = [];
while ($row = $result->fetch_assoc()) {
    $clientes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mapa de Clientes</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background: #f8f9fa; }
    header { padding: 16px 20px; background: #111827; color: #fff; }
    #map { height: calc(100vh - 72px); }
  </style>
</head>
<body>
  <header>
    <strong>Mapa de clientes tatuados</strong>
  </header>
  <div id="map"></div>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    const map = L.map('map').setView([-23.55052, -46.633308], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap'
    }).addTo(map);

    const clientes = <?php echo json_encode($clientes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;

    clientes.forEach((cliente) => {
      fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(cliente.endereco)}`)
        .then((response) => response.json())
        .then((data) => {
          if (!data.length) {
            return;
          }

          const telefone = String(cliente.telefone || '').replace(/\D/g, '');
          const linkWhatsapp = telefone ? `https://wa.me/55${telefone}` : '#';
          const popupContent = `
            <strong>${cliente.nome}</strong><br>
            ${telefone ? `<a href="${linkWhatsapp}" target="_blank">WhatsApp</a><br>` : ''}
            <a href="detalhes_cliente.php?id=${cliente.id}" target="_blank">Ver detalhes</a>`;

          L.marker([data[0].lat, data[0].lon]).addTo(map).bindPopup(popupContent);
        });
    });
  </script>
</body>
</html>
