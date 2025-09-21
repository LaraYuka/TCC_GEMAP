<?php
// headers.php

// Permite requisições de qualquer origem
header("Access-Control-Allow-Origin: *");

// Métodos HTTP que a API aceita
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

// Cabeçalhos que o cliente pode enviar
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Lida com requisições "preflight" do navegador
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configura o tipo de conteúdo da resposta para JSON
header('Content-Type: application/json');
