<?php
session_start(); // Iniciar a sessão para armazenar dados do usuário logado
// Configurações de conexão com o banco de dados
$host = 'localhost';
$dbname = 'sistema_monitoramento';
$username = 'root'; // ou seu nome de usuário do MySQL
$password = ''; // sua senha do MySQL

// Conexão com o banco de dados usando PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Verificar se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receber os dados do formulário
    $numero = $_POST['numero'];
    $senha = $_POST['senha'];

    // Preparar a consulta para verificar o usuário
    $query = $conn->prepare("SELECT * FROM usuarios WHERE numero = :numero");
    $query->bindParam(':numero', $numero);

    // Executar a consulta
    $query->execute();

    // Verificar se o usuário foi encontrado
    if ($query->rowCount() > 0) {
        $usuario = $query->fetch(PDO::FETCH_ASSOC);
    
        // Verificar se a senha fornecida corresponde à senha criptografada no banco de dados
        if (password_verify($senha, $usuario['senha'])) {
            // Armazenar o ID do usuário na sessão, não o usuário completo
            $_SESSION['usuarioLogado'] = $usuario['id']; // Altere aqui para armazenar apenas o ID
            $message = ['status' => 'success', 'message' => 'Login bem-sucedido!'];
        } else {
            $message = ['status' => 'error', 'message' => 'Senha incorreta.'];
        }
    } else {
        $message = ['status' => 'error', 'message' => 'Usuário não encontrado.'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="c_u.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <!-- Modal -->
    <div id="myModal" class="modal" style="display: <?php echo isset($message) ? 'flex' : 'none'; ?>;">
        <div class="modal-content">
            <i id="bx-car" class='bx bx-check-circle' style="display: <?php echo isset($message) && $message['status'] === 'success' ? 'block' : 'none'; ?>;"></i>
            <i id="bx-x" class='bx bx-x' style="display: <?php echo isset($message) && $message['status'] === 'error' ? 'block' : 'none'; ?>;"></i>
            <p id="message"><?php echo isset($message) ? $message['message'] : ''; ?></p>
        </div>
    </div>

    <i class='bx bx-car'></i>
    <h1 class="underline-animation">Login</h1>
    <form id="loginForm" method="POST" action="login.php">
        <div>
            <label for="numero">Número de Telefone:</label>
            <div style="display: flex;">
                <p class="prefixo">+258</p>
                <input type="number" id="numero" name="numero" required inputmode="numeric" pattern="^\d{9}$" title="O número deve ter 9 dígitos.">
            </div>
        </div>
        <div>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        <div class="btn">
            <button type="submit">Entrar</button>
            <a href="cadastro_usuario.php">Fazer registo</a>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const h1 = document.querySelector('.underline-animation');
            h1.classList.add('start'); // Adiciona a classe para iniciar a animação
        });

        // Fecha o modal automaticamente após 3 segundos
        setTimeout(() => {
            const modal = document.getElementById('myModal');
            if (modal.style.display === 'flex') {
                modal.style.display = 'none';
            }
        }, 3000);
    </script>

    <?php
    // Redirecionar se o login for bem-sucedido
    if (isset($message) && $message['status'] === 'success') {
        echo "<script>setTimeout(() => { window.location.href = 'menu.php'; }, 3000);</script>";
    }
    ?>
</body>
</html>
