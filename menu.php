<?php
session_start(); // Inicia a sessão

// Conexão com o banco de dados
$servername = "localhost"; // Substitua pelo seu servidor
$username = "root"; // Substitua pelo seu usuário do banco de dados
$password = ""; // Substitua pela sua senha do banco de dados
$dbname = "sistema_monitoramento"; // Substitua pelo nome do seu banco de dados

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Verificar se o usuário está logado
if (!isset($_SESSION['usuarioLogado'])) {
    // Redirecionar para a página de login se não estiver autenticado
    header('Location: login.php');
    exit();
}

// Obter o ID do usuário logado da sessão
$usuarioID = $_SESSION['usuarioLogado'];

// Consultar o tipo de usuário no banco de dados
$sql = "SELECT tipoUsuario FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuarioID);
$stmt->execute();
$result = $stmt->get_result();

// Verificando se o usuário foi encontrado
if ($result->num_rows > 0) {
    $usuarioLogado = $result->fetch_assoc();
    $tipoUsuario = $usuarioLogado['tipoUsuario']; // Ex: 'motorista' ou 'comum'
} else {
    // Redirecionar se o usuário não for encontrado
    header('Location: login.php');
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página do Usuário</title>
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
            max-width: 1000px;
            margin:  20px auto;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 800px;
        }

        .card {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card i {
            font-size: 40px;
            margin-bottom: 10px;
            color: #2196f3;
        }

        .card-title {
            font-size: 16px;
            color: #333;
        }

        a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>
    <h1>Menu de Opções</h1>
    <div class="card-container">
        <?php if ($tipoUsuario === 'motorista'): ?>
            <div class="card">
                <a href="cadastro_viatura.php">
                    <i class='bx bx-car'></i>
                    <p class="card-title">Cadastrar Viatura</p>
                </a>
            </div>
            <div class="card">
                <a href="adicionar_paragem.php">
                    <i class='bx bx-map'></i>
                    <p class="card-title">Adicionar Paragem</p>
                </a>
            </div>
            <div class="card">
                <a href="pesquisar_viatura.php">
                    <i class='bx bx-search'></i>
                    <p class="card-title">Pesquisar Viatura</p>
                </a>
            </div>
            <div class="card">
                <a href="ver_dados_usuario.php">
                    <i class='bx bx-user'></i>
                    <p class="card-title">Ver Dados do Usuário</p>
                </a>
            </div>
            <div class="card">
                <a href="historico.php">
                    <i class='bx bx-history'></i>
                    <p class="card-title">Históricos</p>
                </a>
            </div>
        <?php else: ?>
            <div class="card">
                <a href="pesquisar_viatura.php">
                    <i class='bx bx-search'></i>
                    <p class="card-title">Pesquisar Viatura</p>
                </a>
            </div>
            <div class="card">
                <a href="ver_dados_usuario.php">
                    <i class='bx bx-user'></i>
                    <p class="card-title">Ver Dados do Usuário</p>
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
