<?php
/**
 * save_exam.php
 * Called via POST from mcq_test.php (fetch API) when a test finishes.
 * Saves the exam attempt to exam_attempts table so it can be shown
 * on the Progress page calendar.
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

$user_class      = trim($input['user_class'] ?? '');
$subject         = trim($input['subject'] ?? '');
$chapters        = trim($input['chapters'] ?? '');
$total_questions = (int) ($input['total_questions'] ?? 0);
$correct         = (int) ($input['correct'] ?? 0);
$wrong           = (int) ($input['wrong'] ?? 0);
$score_pct       = (int) ($input['score_pct'] ?? 0);

if (!$subject || $total_questions <= 0) {
    echo json_encode(['success' => false, 'message' => 'Missing data']);
    exit;
}

require_once 'db.php';

$user_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare(
    "INSERT INTO exam_attempts (user_id, user_class, subject, chapters, total_questions, correct, wrong, score_pct)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param(
    'isssiiii',
    $user_id, $user_class, $subject, $chapters,
    $total_questions, $correct, $wrong, $score_pct
);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
