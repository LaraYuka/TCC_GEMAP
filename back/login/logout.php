<?php
session_start();
$_SESSION = [];
session_destroy();


$dados = array(
    'tipo' => 'success',
    'mensagem' => 'Sua sessão foi encerrada.'
);

echo json_encode($dados);
