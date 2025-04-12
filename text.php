<?php
session_start(); // Inicia a sessão

// Conexão com o banco de dados
$servername = "localhost"; // Substitua pelo seu servidor
$username = "igtkamgn_root"; // Substitua pelo seu usuário do banco de dados
$password = "monitoramento"; // Substitua pela sua senha do banco de dados
$dbname = "igtkamgn_monitoramento"; // Substitua pelo nome do seu banco de dados

// Criando a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}