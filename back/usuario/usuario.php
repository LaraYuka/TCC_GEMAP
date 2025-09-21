<?php
// clients.php - CRUD de clients
session_start();
header('Content-Type: application/json');
// CORS para desenvolvimento; idealmente servir front e back no mesmo host em produção.
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

require_once '../conexao/conexao.php';

// Helper para checar se usuário é admin (para POST/PUT/DELETE)
function require_admin()
{
    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Acesso negado. Somente administrador.']);
        exit;
    }
}

// GET -> lista ou um cliente
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $pdo->prepare('SELECT id, name, email, user_type, created_at FROM usuario WHERE id = ?');
        $stmt->execute([$id]);
        $client = $stmt->fetch();
        if ($client) echo json_encode($client);
        else {
            http_response_code(404);
            echo json_encode(['error' => 'Cliente não encontrado']);
        }
    } else {
        $stmt = $pdo->query('SELECT id, name, email, user_type, created_at FROM usuario ORDER BY id DESC');
        $clients = $stmt->fetchAll();
        echo json_encode($clients);
    }
    exit;
}

// POST -> criar cliente (admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_admin();
    $input = json_decode(file_get_contents('php://input'), true);
    $name = $input['name'] ?? '';
    $email = $input['email'] ?? '';
    $password = $input['password'] ?? '';
    $user_type = $input['user_type'] ?? 'client';

    if (!$name || !$email || !$password) {
        http_response_code(400);
        echo json_encode(['error' => 'name, email e senha são obrigatórios']);
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    try {
        $stmt = $pdo->prepare('INSERT INTO clients (name, email, password, user_type) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $hashed, $user_type]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// PUT -> atualizar (admin)
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    require_admin();
    parse_str(file_get_contents("php://input"), $putVars); // fallback, but JSON decode below preferred
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input) $input = $putVars;
    $id = intval($_GET['id'] ?? ($input['id'] ?? 0));
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID é obrigatório']);
        exit;
    }

    $name = $input['name'] ?? null;
    $email = $input['email'] ?? null;
    $password = $input['password'] ?? null;
    $user_type = $input['user_type'] ?? null;

    $fields = [];
    $params = [];
    if ($name !== null) {
        $fields[] = 'name = ?';
        $params[] = $name;
    }
    if ($email !== null) {
        $fields[] = 'email = ?';
        $params[] = $email;
    }
    if ($user_type !== null) {
        $fields[] = 'user_type = ?';
        $params[] = $user_type;
    }
    if ($password !== null && $password !== '') {
        $fields[] = 'password = ?';
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }

    if (empty($fields)) {
        http_response_code(400);
        echo json_encode(['error' => 'Nada para atualizar']);
        exit;
    }

    $params[] = $id;
    $sql = 'UPDATE clients SET ' . implode(', ', $fields) . ' WHERE id = ?';
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

// DELETE -> excluir (admin)
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    require_admin();
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID é obrigatório']);
        exit;
    }
    try {
        $stmt = $pdo->prepare('DELETE FROM clients WHERE id = ?');
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Método não suportado']);
