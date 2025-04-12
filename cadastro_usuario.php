<?php
// Configurações de conexão com o banco de dados
$host = 'localhost';
$dbname = 'sistema_monitoramento';
$username = 'root'; // ou seu nome de usuário do MySQL
$password = ''; // sua senha do MySQL

// Conexão com o banco de dados
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Captura os dados do formulário
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $numero = $_POST['numero'];
    $senha = password_hash($_POST['senha'], PASSWORD_BCRYPT); // Criptografa a senha
    $tipoUsuario = $_POST['tipoUsuario'];
    $dataCriacao = date('Y-m-d');

    // Insere os dados no banco de dados
    try {
        $sql = "INSERT INTO usuarios (nome, email, numero, senha, tipoUsuario, dataCriacao) VALUES (:nome, :email, :numero, :senha, :tipoUsuario, :dataCriacao)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':numero', $numero);
        $stmt->bindParam(':senha', $senha);
        $stmt->bindParam(':tipoUsuario', $tipoUsuario);
        $stmt->bindParam(':dataCriacao', $dataCriacao);
        $stmt->execute();

        // Redireciona para a página de sucesso com a exibição do modal
        echo "<script>
                window.onload = function() {
                    openModal('success', 'Usuário cadastrado com sucesso, aguarde...');
                };
              </script>";
    } catch (PDOException $e) {
        echo "<script>
                openModal('error', 'Ocorreu um erro ao registrar, tente de novo.');
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="c_u.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <i id="bx-car" class='bx bx-check-circle'></i>
            <i id="bx-x" class='bx bx-x' style="display: none;"></i> <!-- Escondido por padrão -->
            <p id="message"></p>
        </div>
    </div>

    <h1 class="underline-animation">Faça seu cadastro para monitoramento</h1>

    <form id="cadastroForm" action="cadastro_usuario.php" method="POST">
        <div>
            <label for="nome">Nome completo:</label>
            <input type="text" id="nome" name="nome" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="numero">Número de Telefone:</label>
            <div style="display: flex;">
                <input type="number" value="+258" disabled style="margin-right: 10px; width: 80px;">
                <input type="number" id="numero" name="numero" required inputmode="numeric" pattern="^\d{9}$" title="O número deve ter 9 dígitos.">
            </div>
        </div>
        <div>
            <label for="senha">Digite uma Senha:</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        <div>
            <label for="tipoUsuario">Tipo de usuário:</label>
            <select id="tipoUsuario" name="tipoUsuario" required>
                <option value="">Selecione</option>
                <option value="motorista">Motorista</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit">Cadastrar</button>
    </form>

    <script>
        function openModal(icon, text) {
            const modal = document.getElementById('myModal');
            modal.style.display = 'flex'; 

            const sucess = document.getElementById('bx-car');
            const x = document.getElementById('bx-x');
            const textMessage = document.getElementById('message');

            if (icon === "success") {
                sucess.style.display = "block"; // Mostra o ícone de sucesso
                x.style.display = "none"; // Esconde o ícone de erro
                textMessage.textContent = text; // Exibe o texto de sucesso
            } else {
                sucess.style.display = "none"; // Esconde o ícone de sucesso
                x.style.display = "block"; // Mostra o ícone de erro
                textMessage.textContent = "Ocorreu um erro ao registrar, tente de novo."; // Exibe a mensagem de erro
            }

            // Fecha o modal após 8 segundos e redireciona
            setTimeout(() => {
                modal.style.display = 'none';
                window.location.href = 'login.php'; // Redireciona para a página de login
            }, 8000);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const h1 = document.querySelector('.underline-animation');
            h1.classList.add('start'); // Adiciona a classe para iniciar a animação
        });
    </script>
</body>
</html>
