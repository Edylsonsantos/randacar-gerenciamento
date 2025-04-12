<?php
session_start(); // Inicia a sessão

// Conexão com o banco de dados
$conn = new mysqli('localhost', 'root', "", 'sistema_monitoramento');

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Inicializa variáveis
$historico = [];
$nomeViatura = '';
$viaturas = [];

// Verifica se o ID do usuário está definido na sessão
if (isset($_SESSION['usuarioLogado'])) {
    $usuarioID = $_SESSION['usuarioLogado'];
    // Obtém as placas de viaturas apenas do usuário logado
    $sql = "SELECT placa, nomeViatura FROM viaturas WHERE usuarioID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuarioID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $viaturas[] = $row; // Armazena todas as viaturas do usuário
        }
    }

    // Verifica se uma placa foi selecionada
    if (isset($_POST['placa'])) {
        $placa = $_POST['placa'];

        // Consulta para obter o histórico da viatura
        $sql = "SELECT h.data, h.hora, h.localizacao, v.nomeViatura AS nome_viatura 
                FROM historicos h 
                JOIN viaturas v ON h.placa = v.placa 
                WHERE h.placa = ? AND v.usuarioID = ?";
        
        $stmt = $conn->prepare($sql);
        
        // Verifica se a preparação da consulta falhou
        if ($stmt === false) {
            die("Erro ao preparar a consulta: " . $conn->error);
        }

        $stmt->bind_param("si", $placa, $usuarioID); // Use $usuarioID em vez de $id_usuario
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $historico[] = $row; // Armazena o histórico
        }

        // Obtém o nome da viatura (se existir)
        if (!empty($historico)) {
            $nomeViatura = $historico[0]['nome_viatura'];
        }
    }
} else {
    // Caso o ID do usuário não esteja definido, redirecionar ou exibir mensagem
    echo "Por favor, faça login para acessar esta página.";
    exit; // Para evitar que o restante do código seja executado
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
            font-size:18px;
            color: #333;
            margin-bottom: 10px;
            border-bottom: 3px solid #3357FF;
            text-transform: uppercase;
            width: 100%;
            padding-bottom: 10px;
        }

        select {
            width: 100%;
            max-width: 500px;
            padding: 10px;
            outline: none;
            margin-bottom: 15px;
            border: 2px solid #ddd;
            border-radius: 0;
            font-size: 16px;
            transition: border-color 0.3s;
            background-color: white;
            font-family: "roboto", sans-serif;
        }

        select:focus{
            border-bottom: 3px solid #3357FF;
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
    <h1><?php echo ($nomeViatura) ? "HISTÓRICO DA VIATURA: $nomeViatura" : "Selecione uma viatura"; ?></h1>

    <form method="POST" action="">
        <select name="placa" onchange="this.form.submit()">
            <option value="">Selecione uma placa</option>
            <?php foreach ($viaturas as $viatura): ?>
                <option value="<?php echo htmlspecialchars($viatura['placa']); ?>">
                    <?php echo htmlspecialchars($viatura['placa']) . ' - ' . htmlspecialchars($viatura['nomeViatura']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

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
</body>
</html>
