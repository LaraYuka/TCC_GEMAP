<?php
$hostname = "localhost";
$bd = "tcc";
$usuario = "root";
$senha = "macro218";

$mysqli = new mysqli($hostname, $usuario, $senha, $bd);

if ($mysqli->connect_errno) {
    echo "falha ao conectar: (" . $mysqli->connect_error . ")" . $mysqli->connect_errno;
} else
    echo "Conectado ao Banco de Dados";
