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
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o usuário está logado
if (!isset($_SESSION['usuarioLogado'])) {
    // Redirecionar para a página de login se não estiver autenticado
    header('Location: login.php');
    exit();
}

// Obter o ID do usuário logado da sessão
$usuarioID = $_SESSION['usuarioLogado'];

// Consultar os dados do usuário no banco de dados
$sql = "SELECT nome, numero, tipoUsuario, dataCriacao FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Erro ao preparar a consulta: " . $conn->error);
}

$stmt->bind_param("i", $usuarioID);
$stmt->execute();
$result = $stmt->get_result();

// Verificando se o usuário foi encontrado
if ($result->num_rows > 0) {
    $usuarioLogado = $result->fetch_assoc();
    // Atribui os dados do usuário a variáveis
    $usuarioNome = $usuarioLogado['nome'];
    $usuarioNumero = $usuarioLogado['numero'];
    $usuarioData = $usuarioLogado['dataCriacao'];
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
    <title>Dados do Usuário</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            display: flex;
            justify-content: center;
            height: 100vh;
            margin: 20px;
            background-color: #f0f0f0;
            flex-direction: column;
            overflow-x: hidden; /* Para alinhar os elementos verticalmente */
            max-width: 1000px;
            margin:  20px auto;
        }

        h1 {
            text-align: center;
            color: #3f51b5;
            margin-bottom: 20px;
            font-size: 18px;
            text-transform: uppercase;
            border: none;
            padding: 0;
            margin-left: 35px;
        }

        #dadosUsuario {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 0;
            text-align: left;
            border: 1px solid #007bff
        }

        p {
            font-size: 17px;
            color: #555;
            margin: 10px 0;
            font-family: "roboto", sans-serif;
        }

        strong {
            text-transform: uppercase;
            font-size: 15px!important;
        }

        /* Estilo para o botão de logout */
        button {
            padding: 10px 20px;
            background-color: #007bff;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border-radius: 0;
            margin-top: 20px;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div>
        <h1>Dados do Usuário</h1>
        <div id="dadosUsuario">
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($usuarioNome); ?></p>
            <p><strong>Número:</strong> <?php echo htmlspecialchars($usuarioNumero); ?></p>
            <p><strong>Data de criação:</strong> <?php echo htmlspecialchars($usuarioData); ?></p>
            <p><strong>Tipo de Usuário:</strong> <?php echo htmlspecialchars($tipoUsuario === 'motorista' ? 'Motorista' : 'Usuário Comum'); ?></p>
        </div>

        <!-- Botão de Logout -->
        <button id="logoutBtn">Logout</button>
    </div>

    <script>
        // Função de logout
        function logout() {
            // Remover todos os dados da sessão e redirecionar
            fetch('logout.php', {
                method: 'POST'
            })
            .then(response => {
                if (response.ok) {
                    window.location.href = 'login.php'; // Redireciona para a página de login
                }
            })
            .catch(error => console.error('Erro ao fazer logout:', error));
        }

        // Adiciona o evento de clique ao botão de logout
        document.getElementById('logoutBtn').addEventListener('click', logout);
    </script>
</body>
</html>
