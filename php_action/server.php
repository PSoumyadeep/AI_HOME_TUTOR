<?php
// ─── php_action/server.php ───────────────────────────────────────────────────
session_start();
require_once __DIR__ . '/db.php';

$action = $_POST['action'] ?? '';

// ═══════════════════════════════════════════════════════════════════════════
// 1. AJAX: Check if an email is already registered
// ═══════════════════════════════════════════════════════════════════════════
if ($action === 'check_email') {
    header('Content-Type: application/json');
    $email = trim($_POST['email'] ?? '');
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['exists' => false, 'error' => 'Invalid email']);
        exit;
    }
    $stmt = $conn->prepare("SELECT id FROM users WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    echo json_encode(['exists' => $stmt->num_rows > 0]);
    $stmt->close();
    $conn->close();
    exit;
}

// ═══════════════════════════════════════════════════════════════════════════
// 2. REGISTER
// ═══════════════════════════════════════════════════════════════════════════
if ($action === 'register') {
    $class    = trim($_POST['class']           ?? '');
   
    $name     = trim($_POST['name']            ?? '');
    $phone    = trim($_POST['phone']           ?? '');
    $email    = trim($_POST['email']           ?? '');
    $password = $_POST['password']             ?? '';
    $confirm  = $_POST['confirm_password']     ?? '';

    if (empty($class) || empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill in all required fields.';
        header('Location: ../register.php'); exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Please enter a valid email address.';
        header('Location: ../register.php'); exit;
    }
    if (strlen($password) < 8) {
        $_SESSION['error'] = 'Password must be at least 8 characters.';
        header('Location: ../register.php'); exit;
    }
    if ($password !== $confirm) {
        $_SESSION['error'] = 'Passwords do not match.';
        header('Location: ../register.php'); exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql  = "INSERT INTO users (user_class, user_name, user_phone, user_email, user_password)
             VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        $_SESSION['error'] = 'Server error. Please try again later.';
        header('Location: ../register.php'); exit;
    }
    $stmt->bind_param("sssss", $class, $name, $phone, $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['user_id']    = $conn->insert_id;
        $_SESSION['user_name']  = $name;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_class'] = $class;
       
        $_SESSION['user_phone'] = $phone;
        $stmt->close(); $conn->close();
        header('Location: ../dashboard.php'); exit;
    } else {
        $_SESSION['error'] = ($conn->errno === 1062)
            ? 'This email address is already registered.'
            : 'Registration failed: ' . $stmt->error;
        $stmt->close(); $conn->close();
        header('Location: ../register.php'); exit;
    }
}

// ═══════════════════════════════════════════════════════════════════════════
// 3. LOGIN
// ═══════════════════════════════════════════════════════════════════════════
if ($action === 'login') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Please enter your email and password.';
        header('Location: ../index.php'); exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['login_error'] = 'Please enter a valid email address.';
        header('Location: ../index.php'); exit;
    }

    $stmt = $conn->prepare("SELECT id, user_name, user_email, user_password, user_class,  user_phone FROM users WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['login_error'] = 'Invalid email or password.';
        $stmt->close(); $conn->close();
        header('Location: ../index.php'); exit;
    }

    $user = $result->fetch_assoc();
    if (!password_verify($password, $user['user_password'])) {
        $_SESSION['login_error'] = 'Invalid email or password.';
        $stmt->close(); $conn->close();
        header('Location: ../index.php'); exit;
    }

    session_regenerate_id(true);
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['user_name'];
    $_SESSION['user_email'] = $user['user_email'];
    $_SESSION['user_class'] = $user['user_class'];
    
    $_SESSION['user_phone'] = $user['user_phone'];

    $stmt->close(); $conn->close();
    header('Location: ../dashboard.php'); exit;
}

// Fallback
header('Location: ../index.php'); exit;
?>