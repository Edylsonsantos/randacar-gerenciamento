<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Histórico de Paragem</title>
    <link rel="stylesheet" href="c_u.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCXziIbU_msVXwoHlXC8yOeHrdwsZTmD2E&callback=initMap" async defer></script>
    <style>
        body {
            height: auto!important;
        }
        #map {
            height: 300px; /* Altura do mapa */
            width: 100%; /* Largura do mapa */
            margin-bottom: 20px; /* Espaço abaixo do mapa */
        }
        h1, .underline-animation {
            text-align: left!important;
            font-size: 16px!important;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1 class="underline-animation">Adicionar Histórico de Paragem</h1>

    <form id="adicionarHistoricoForm" method="POST">
        <div>
            <label for="placa">Placa da Viatura:</label>
            <select id="placa" name="placa" required>
                <option value="">Selecione a Viatura</option>
                <?php
                session_start();
                
$conn = new mysqli('localhost', 'root', '', 'sistema_monitoramento');

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Defina o ID do usuário atual (ajuste conforme necessário)
$usuarioID = $_SESSION['usuarioLogado']; // Troque pelo ID do usuário logado
echo "ID do Usuário: " . $usuarioID; // Debug: Mostra o ID do usuário

// Consulta as viaturas disponíveis do usuário
$sql = "SELECT placa FROM viaturas WHERE usuarioID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarioID);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se há resultados
if ($result->num_rows > 0) {
    // Exibe as viaturas no select
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['placa']}'>{$row['placa']}</option>";
    }
} else {
    echo "Nenhuma viatura encontrada para o usuário com ID: " . $usuario_id; // Mensagem se não encontrar viaturas
}

$stmt->close();
$conn->close();

                ?>
            </select>
        </div>
        <div>
            <label for="data">Data:</label>
            <input type="date" id="data" name="data" required>
        </div>
        <div>
            <label for="hora">Hora:</label>
            <input type="time" id="hora" name="hora" required>
        </div>
        <div>
            <label for="localizacao">Localização:</label>
            <input type="text" id="localizacao" name="localizacao" required readonly>
        </div>
        <div>
            <button type="button" id="escolherLocalizacao" style="width: 100%;">Selecionar Localização no Mapa</button>
        </div>
        <div id="map"></div>
        <button type="submit">Adicionar Histórico</button>
    </form>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <i id="bx-car" class='bx bx-check-circle' style="display: none;"></i>
            <i id="bx-x" class='bx bx-x' style="display: none;"></i>
            <p id="message"></p>
        </div>
    </div>

    <script>
        let map;
        let marker;

        function initMap() {
            const mapOptions = {
                zoom: 10,
                center: { lat: -17.8800, lng: 36.8440 }, // Centraliza em Quelimane
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            map.addListener('click', function(event) {
                placeMarker(event.latLng);
            });
        }

        function placeMarker(location) {
            if (marker) {
                marker.setMap(null);
            }
            marker = new google.maps.Marker({
                position: location,
                map: map
            });
            document.getElementById('localizacao').value = `${location.lat()}, ${location.lng()}`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const h1 = document.querySelector('.underline-animation');
            h1.classList.add('start'); // Adiciona a classe para iniciar a animação
        });

        function openModal(icon, text) {
            const modal = document.getElementById('myModal');
            modal.style.display = 'flex';

            const sucess = document.getElementById('bx-car');
            const x = document.getElementById('bx-x');
            const textMessage = document.getElementById('message');

            if (icon === "success") {
                sucess.style.display = "block";
                x.style.display = "none";
                textMessage.textContent = text;
            } else {
                sucess.style.display = "none";
                x.style.display = "block";
                textMessage.textContent = text;
            }

            setTimeout(() => {
                modal.style.display = 'none';
            }, 3000);
        }

        document.getElementById('adicionarHistoricoForm').addEventListener('submit', function (e) {
            e.preventDefault();

            this.submit();
        });
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $conn = new mysqli('localhost', 'igtkamgn_root', 'monitoramento', 'igtkamgn_monitoramento'); // Atualize com os dados corretos

        if ($conn->connect_error) {
            die("Erro de conexão: " . $conn->connect_error);
        }

        $placa = $_POST['placa'];
        $data = $_POST['data'];
        $hora = $_POST['hora'];
        $localizacao = $_POST['localizacao'];

        $sql = "INSERT INTO historicos (placa, data, hora, localizacao) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $placa, $data, $hora, $localizacao);

        if ($stmt->execute()) {
            echo "<script>openModal('success', 'Histórico adicionado com sucesso!');</script>";
        } else {
            echo "<script>openModal('error', 'Erro ao adicionar histórico: " . $conn->error . "');</script>";
        }

        $stmt->close();
        $conn->close();
    }
    ?>
</body>
</html>
