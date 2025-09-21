<?php
require_once '../conexao/conexao.php';
$email = 'admin@gmail.com';
$password = password_hash('123', PASSWORD_DEFAULT);
$name = 'Administrador';
$user_type = 'admin';

$stmt = $pdo->prepare('INSERT INTO users (name, email, password, user_type) VALUES (?, ?, ?, ?)');
try {
    $stmt->execute([$name, $email, $password, $user_type]);
    echo "Admin criado com sucesso\n";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
