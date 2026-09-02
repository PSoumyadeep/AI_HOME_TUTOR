<?php
/**
 * save_topic.php
 * Called via POST from learn.php (fetch API) after AI response is received.
 * Saves topic_name + ai_response to topics_learned table.
 */
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$topic_name  = trim($input['topic_name']  ?? '');
$ai_response = trim($input['ai_response'] ?? '');

if (!$topic_name || !$ai_response) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

// ── DB connection ─────────────────────────────────────────────────────────────
require_once 'db.php'; // adjust path if your DB connection file differs
// $conn should be a mysqli connection after this include.
// If you use PDO or a different file, update accordingly.

$user_id = (int) $_SESSION['user_id'];

// Prevent exact duplicate (same user + same topic in last 24 h)
$check = $conn->prepare(
    "SELECT id FROM topics_learned
     WHERE user_id = ? AND topic_name = ?
     AND learned_at >= NOW() - INTERVAL 24 HOUR
     LIMIT 1"
);
$check->bind_param('is', $user_id, $topic_name);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Already saved recently — update the response instead
    $upd = $conn->prepare(
        "UPDATE topics_learned SET ai_response = ?, learned_at = NOW()
         WHERE user_id = ? AND topic_name = ?
         AND learned_at >= NOW() - INTERVAL 24 HOUR"
    );
    $upd->bind_param('sis', $ai_response, $user_id, $topic_name);
    $upd->execute();
    echo json_encode(['success' => true, 'action' => 'updated']);
    exit;
}

$check->close();

// Insert new record
$stmt = $conn->prepare(
    "INSERT INTO topics_learned (user_id, topic_name, ai_response)
     VALUES (?, ?, ?)"
);
$stmt->bind_param('iss', $user_id, $topic_name, $ai_response);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'action' => 'inserted', 'id' => $stmt->insert_id]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();