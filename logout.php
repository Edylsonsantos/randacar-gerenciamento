<?php
session_start(); // Inicia a sessão

// Destroi todas as informações da sessão
session_destroy(); 

// Opcionalmente, remova os dados do localStorage no lado do cliente, se necessário
echo json_encode(['status' => 'success']); // Retorna um status de sucesso
?>
