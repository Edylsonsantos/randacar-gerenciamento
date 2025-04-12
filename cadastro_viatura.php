<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistema_monitoramento";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Conexão falhou: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obter os dados do formulário
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar se todos os campos estão preenchidos
    if (isset($data['placa'], $data['nomeViatura'], $data['proprietario'], $data['empresa'], $data['usuarioID'])) {
        $placa = $data['placa'];
        $nomeViatura = $data['nomeViatura'];
        $proprietario = $data['proprietario'];
        $empresa = $data['empresa'];
        $usuarioID = $data['usuarioID'];

        // Verificar se a placa já existe
        $checkSql = "SELECT COUNT(*) FROM viaturas WHERE placa = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $placa);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {
            echo json_encode(['success' => false, 'message' => 'A placa já está cadastrada.']);
        } else {
            // Preparar a consulta SQL para inserir os dados
            $sql = "INSERT INTO viaturas (placa, nomeViatura, proprietario, empresa, usuarioID) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("ssssi", $placa, $nomeViatura, $proprietario, $empresa, $usuarioID);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'A placa já está cadastrada.' . $stmt->error]);
                }

                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta: ' . $conn->error]);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
    }
    $conn->close();
    exit; // Finaliza a execução do PHP após o envio
}
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Viatura</title>
    <link rel="stylesheet" href="c_u.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCXziIbU_msVXwoHlXC8yOeHrdwsZTmD2E"></script>
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body{
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: auto!important;
            justify-items: center;
            max-width: 1000px;
            margin:  20px auto;
        }
        h1{
            display: flex;
            flex-direction: row-reverse;
            text-align: left!important;
            font-size: 16px!important;
            align-items: center;
            border-bottom: 3px solid #3f51b5;
        }
    </style>
</head>
<body>
    <!-- Modal -->
    <div id="myModal" class="modal" style="display: none;">
        <div class="modal-content">
            <i id="bx-car" class='bx bx-check-circle' style="display: none;"></i>
            <i id="bx-x" class='bx bx-x' style="display: none;"></i>
            <p id="message"></p>
        </div>
    </div>

    <h1 class="underline-animation">Cadastrar Viatura</h1>
    <form id="cadastroViaturaForm">
        <div>
            <label for="placa">Placa:</label>
            <input type="text" id="placa" required>
        </div>
        <div>
            <label for="nomeViatura">Nome da Viatura:</label>
            <input type="text" id="nomeViatura" required>
        </div>
        <div>
            <label for="proprietario">Nome do Proprietário:</label>
            <input type="text" id="proprietario" required>
        </div>
        <div>
            <label for="empresa">Nome da Empresa:</label>
            <input type="text" id="empresa" required>
        </div>
        <!-- Campo oculto para o ID do usuário logado -->
        <input type="hidden" id="usuarioID" value="<?php echo htmlspecialchars($_SESSION['usuarioLogado']); ?>">
        <button type="submit">Cadastrar Viatura</button>
    </form>

    <script>
        function openModal(icon, text) {
            const modal = document.getElementById('myModal');
            modal.style.display = 'flex';

            const successIcon = document.getElementById('bx-car');
            const errorIcon = document.getElementById('bx-x');
            const textMessage = document.getElementById('message');

            if (icon === "success") {
                successIcon.style.display = "block";
                errorIcon.style.display = "none";
                textMessage.textContent = text;
            } else {
                successIcon.style.display = "none";
                errorIcon.style.display = "block";
                textMessage.textContent = text;
            }

            setTimeout(() => {
                modal.style.display = 'none';
            }, 3000);
        }

        document.getElementById('cadastroViaturaForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const viatura = {
                placa: document.getElementById('placa').value,
                nomeViatura: document.getElementById('nomeViatura').value,
                proprietario: document.getElementById('proprietario').value,
                empresa: document.getElementById('empresa').value,
                usuarioID: document.getElementById('usuarioID').value // ID do usuário logado
            };

            // Envio dos dados para o servidor
            fetch('', { // O mesmo arquivo lida com a requisição
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(viatura),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    openModal("success", "Viatura cadastrada com sucesso!");
                    setTimeout(() => {
                        window.location.href = 'adicionar_paragem.php';
                    }, 3000);
                } else {
                    openModal("error", "A placa já está cadastrada, tente novamente!");
                }
            })
            .catch((error) => {
                openModal("error", "Erro ao conectar ao servidor.");
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
