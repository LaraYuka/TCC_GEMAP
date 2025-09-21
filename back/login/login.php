<?php
// Inclui o arquivo de cabeçalhos para permitir a conexão com o Angular
include '../conexao/cabecalho.php';

// Inclui o arquivo de conexão com o banco de dados
include '../conexao/conexao.php';

// Verifica se a requisição é do tipo POST e se o corpo não está vazio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty(file_get_contents("php://input"))) {

    // Inclui a conexão com o banco de dados apenas quando for usar
    include '../conexao/conexao.php';

    // Recebe os dados JSON
    $data = json_decode(file_get_contents("php://input"));

    $email = isset($data->email) ? $data->email : '';
    $password = isset($data->password) ? $data->password : '';


    // Agora o seu código de consulta ao banco de dados pode ser executado
    $sql = "SELECT * FROM usuario WHERE email = ? AND senha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Lógica para verificar o resultado da consulta
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        echo json_encode(["status" => "success", "message" => "Login bem-sucedido!", "user" => $usuario]);
    } else {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Credenciais inválidas."]);
    }

    $conn->close();
} else {
    // Se não for uma requisição POST, retorne uma mensagem de erro
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Método não permitido ou dados ausentes."]);
}
