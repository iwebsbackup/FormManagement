
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header('Location: dashboard.php?error=invalid');
    exit;
}

$stmt = $pdo->prepare('DELETE FROM submissions WHERE id = ?');
$stmt->execute([$id]);

header('Location: dashboard.php?deleted=1');
exit;
