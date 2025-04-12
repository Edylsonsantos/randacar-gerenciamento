<?php
session_start(); // Iniciar a sessão para verificar a autenticação

// Verificar se o usuário está logado
if (isset($_SESSION['usuarioLogado'])) {
    // Usuário está autenticado
    $redirectPage = 'menu.php'; // Redirecionar para o menu
} else {
    // Usuário não está autenticado
    $redirectPage = 'login.php'; // Redirecionar para a página de login
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .splash-container {
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
            font-size: 16px;
            text-transform: uppercase;
        }

        .bx-car {
            color: #333;
            font-size: 25px
        }

        .progress-container {
            position: relative;
            width: 20px;
            height: 20px;
            margin: 0 auto;
        }

        .progress {
            width: 100%;
            height: 100%;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #007bff;
            border-left: 5px solid #007bff;
            border-right: 5px solid #007bff;
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<div class="splash-container">
    <i class='bx bx-car'></i>
    <h1>DIAGNÓSTICOS RASTREIO DE VIATURAS</h1>
    
    <div class="progress-container">
        <div class="progress"></div>
    </div>
</div>

<script>
    // Redireciona após 8 segundos
    setTimeout(function () {
        window.location.href = '<?php echo $redirectPage; ?>'; // Redirecionar conforme a autenticação
    }, 8000); // 8 segundos de splash antes de redirecionar
</script>

</body>
</html>
