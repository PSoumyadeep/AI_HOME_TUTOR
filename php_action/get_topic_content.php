<?php
/**
 * get_topic_content.php
 * Returns JSON with ai_response for the progress page modal viewer.
 */
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$topic_id = (int) ($_GET['id'] ?? 0);
if (!$topic_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

require_once 'db.php';

$user_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT topic_name, ai_response, learned_at
     FROM topics_learned
     WHERE id = ? AND user_id = ?
     LIMIT 1"
);
$stmt->bind_param('ii', $topic_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Not found']);
    exit;
}

$row = $result->fetch_assoc();
echo json_encode([
    'success'     => true,
    'topic_name'  => $row['topic_name'],
    'ai_response' => $row['ai_response'],
    'learned_at'  => $row['learned_at'],
]);

$stmt->close();
$conn->close();