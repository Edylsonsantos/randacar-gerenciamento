<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'sistema_monitoramento');

// Verifica a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$viaturas = []; // Inicializa o array para armazenar viaturas
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['placa'])) {
    $placa = $_POST['placa'];

    // Consulta as viaturas com base na placa ou nome da empresa
    $sql = "SELECT * FROM viaturas WHERE placa = ? OR empresa LIKE ?";
    $stmt = $conn->prepare($sql);
    $placaLike = "%$placa%";
    $stmt->bind_param("ss", $placa, $placaLike);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $viaturas[] = $row; // Adiciona cada viatura encontrada ao array
    }
    $stmt->close();
}

$conn->close(); // Fecha a conexão com o banco de dados
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar Viatura</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Roboto', sans-serif; background-color: #f0f0f0; color: #333; justify-content: center; align-items: center; min-height: 100vh; width: 100%; padding: 20px; padding-top: 15%;    max-width: 1000px;
            margin:  20px auto; }
        h1 { margin-bottom: 20px; color: #007bff; font-size: 18px; text-transform: uppercase; text-align: center; }
        form { background-color: white; padding: 20px; border-radius: 0; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); width: 100%; max-width: 600px; margin: auto; }
        label { font-weight: 700; margin-bottom: 5px; display: block; color: #555; text-transform: uppercase; font-size: 14px; }
        input[type="text"] { width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 0; font-size: 16px; transition: border-color 0.3s; }
        input[type="text"]:focus { border-bottom-color: #007bff; outline: none; }
        button { width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 0; font-size: 18px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
        #resultado { width: 100%; max-width: 600px; margin-top: 20px; background-color: white; padding: 20px; border-radius: 0; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);     max-width: 1000px;
            margin:  20px auto;}
        h2, strong { color: #007bff; margin-bottom: 10px; font-size: 16px; text-transform: uppercase; }
        strong { color: #000; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #ddd; }
        li:last-child { border-bottom: none; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; }
        .modal-content { background-color: white; padding: 20px; border-radius: 5px; width: 90%; max-width: 500px; text-align: center; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); }
        .modal-content h3 { margin-bottom: 20px; color: #e74c3c; font-size: 16px; font-weight: 500; }
        .close-modal { background-color: #007bff; color: white; padding: 8px 20px; border: none; border-radius: 0; cursor: pointer; font-size: 14px; text-transform: uppercase; font-weight: 500; }
        .close-modal:hover { background-color: #0056b3; }
    </style>
</head>
<body>
    <div>
        <h1><i class='bx bx-search-alt'></i> Pesquisar Viatura</h1>
        <form method="POST" id="pesquisaForm">
            <div>
                <label for="placa">Placa da Viatura ou Nome da Empresa:</label>
                <input type="text" name="placa" id="placa" placeholder="Ex: ABC123 ou Nome Empresa" required>
            </div>
            <button type="submit"><i class='bx bx-search'></i></button>
        </form>

        <div id="resultado">
            <?php if (!empty($viaturas)): ?>
                <h2>Resultados:</h2>
                <ul>
                    <?php foreach ($viaturas as $v): ?>
                        <li>
                            <i class='bx bxs-map location-icon'></i>
                            <div class="details">
                                <p class="date">Empresa: <?php echo htmlspecialchars($v['empresa']); ?></p>
                                <p class="date">Placa: <?php echo htmlspecialchars($v['placa']); ?></p>
                                <p class="coords">Nome do proprietário: <?php echo htmlspecialchars($v['proprietario']); ?>, modelo: <?php echo htmlspecialchars($v['nomeViatura']); ?></p>
                            </div>
                        </li>
                        <button onclick="redirectToHistory('<?php echo htmlspecialchars($v['placa']); ?>', '<?php echo htmlspecialchars($v['empresa']); ?>')">
                            <i class='bx bx-history'></i>
                        </button>
                    <?php endforeach; ?>
                </ul>
            <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                <div id="modal" class="modal" style="display: flex;">
                    <div class="modal-content">
                        <h3>Nenhuma viatura encontrada!</h3>
                        <button class="close-modal">Fechar</button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function redirectToHistory(placa, empresa) {
            // Cria a URL para a página de histórico com os parâmetros
            window.location.href = `historico_viatura.php?placa=${encodeURIComponent(placa)}&empresa=${encodeURIComponent(empresa)}`;
        }

        document.querySelector('.close-modal').addEventListener('click', function () {
            document.getElementById('modal').style.display = 'none';
        });

        window.addEventListener('click', function (e) {
            if (e.target === document.getElementById('modal')) {
                document.getElementById('modal').style.display = 'none';
            }
        });
    </script>

</body>
</html>
