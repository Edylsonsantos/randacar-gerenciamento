<?php
header('Content-Type: application/json');

// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "sistema_monitoramento";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erro de conexão com o banco de dados.']);
    exit();
}

// Receber dados do POST
$data = json_decode(file_get_contents('php://input'), true);
$placa = $conn->real_escape_string($data['placa']);
$nomeViatura = $conn->real_escape_string($data['nomeViatura']);
$proprietario = $conn->real_escape_string($data['proprietario']);
$empresa = $conn->real_escape_string($data['empresa']);

// Inserir no banco de dados
$sql = "INSERT INTO viaturas (placa, nomeViatura, proprietario, empresa) VALUES ('$placa', '$nomeViatura', '$proprietario', '$empresa')";
if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar viatura.']);
}

// Fechar conexão
$conn->close();
?>
