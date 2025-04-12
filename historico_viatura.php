<?php
// Conexão com o banco de dados
$conn = new mysqli('localhost', 'root', "", 'sistema_monitoramento');

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Verifica se a placa e a empresa foram passadas na URL
$placa = isset($_GET['placa']) ? $_GET['placa'] : '';
$empresa = isset($_GET['empresa']) ? $_GET['empresa'] : '';

// Inicializa variáveis
$historico = [];
$nomeViatura = '';

// Obtém o histórico da viatura se a placa ou a empresa estiver definida
if ($placa || $empresa) {
    // Consulta para obter o histórico e o nome da viatura
    $sql = "SELECT h.data, h.localizacao, v.nomeViatura AS nome_viatura 
            FROM historicos h 
            JOIN viaturas v ON h.placa = v.placa 
            WHERE h.placa = ? OR v.empresa = ?";
    
    $stmt = $conn->prepare($sql);
    
    // Verifica se a preparação da consulta falhou
    if ($stmt === false) {
        die("Erro ao preparar a consulta: " . $conn->error);
    }

    $stmt->bind_param("ss", $placa, $empresa);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $historico[] = $row; // Armazena cada paragem
    }

    // Obtém o nome da viatura (se existir)
    if (!empty($historico)) {
        $nomeViatura = $historico[0]['nome_viatura'];
    }
}

// Fecha a conexão com o banco de dados
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Paragens</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCXziIbU_msVXwoHlXC8yOeHrdwsZTmD2E"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
            max-width: 1000px;
            margin:  20px auto;
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 10px;
        }

        #map {
            height: 300px;
            width: 100%;
            margin-bottom: 20px;
            border-radius: 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #dadosViatura {
            text-align: center;
            margin-bottom: 20px;
        }

        ul {
            list-style-type: none;
            padding: 0;
            width: 100%;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        li {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        li:last-child {
            border-bottom: none;
        }

        li .details {
            flex: 1;
            margin-left: 10px;
        }

        .location-icon {
            font-size: 24px;
            color: #2196f3;
        }

        .date {
            font-size: 14px;
            color: #555;
        }

        .coords {
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <h1 class="underline-animation" id="dadosViatura"><?php echo ($placa || $empresa) ? "HISTÓRICO DA VIATURA: $nomeViatura" : "Viatura não encontrada!"; ?></h1>
    
    <div id="map"></div>
    <ul id="listaParagens">
        <?php if (count($historico) > 0): ?>
            <?php foreach ($historico as $paragem): ?>
                <li>
                    <i class='bx bxs-map location-icon'></i>
                    <div class="details">
                        <p class="date">Data: <?php echo htmlspecialchars($paragem['data']); ?></p>
                        <p class="coords">Localização: <?php echo htmlspecialchars($paragem['localizacao']); ?></p>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Não há paragens registradas para esta viatura.</li>
        <?php endif; ?>
    </ul>

    <script>
        function initMap() {
            const quelimane = { lat: -17.878388, lng: 36.888273 };
            const map = new google.maps.Map(document.getElementById('map'), {
                zoom: 13,
                center: quelimane
            });

            const historico = <?php echo json_encode($historico); ?>;

            historico.forEach(paragem => {
                const coords = paragem.localizacao.split(', '); // Divide a localização em latitude e longitude
                const position = { lat: parseFloat(coords[0]), lng: parseFloat(coords[1]) };
                const placa = paragem.placa || 'Placa não disponível'; // Adiciona uma verificação para a placa

                const marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: `Paragem em ${paragem.data}`
                });

                // Adiciona uma label para a placa
                const infowindow = new google.maps.InfoWindow({
                    content: `<div><strong>${placa}</strong></div>`
                });

                marker.addListener('click', function() {
                    infowindow.open(map, marker);
                });
            });
        }

        window.onload = initMap; // Chama a função initMap quando a página carrega
    </script>
</body>
</html>
