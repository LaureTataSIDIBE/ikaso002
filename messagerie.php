<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
include 'dbconnect.php'; // Inclure le fichier de connexion

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user = $data['user'];
    $message = $data['message'];

    $stmt = $conn->prepare("INSERT INTO messages (user, message) VALUES (:user, :message)");
    $stmt->execute(['user' => $user, 'message' => $message]);

    echo json_encode(['success' => true]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $conn->query("SELECT * FROM messages ORDER BY id ASC");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($messages);
    exit();
}
?>