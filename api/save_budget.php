<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/db.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['type']) || !isset($input['amount'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit;
}

$type = $input['type'] === 'monthly' ? 'monthly' : 'weekly';
$amount = number_format((float)$input['amount'], 2, '.', '');

// Use INSERT ... ON DUPLICATE KEY UPDATE approach
$stmt = mysqli_prepare($conn, 'INSERT INTO budgets (`type`, amount) VALUES (?, ?) ON DUPLICATE KEY UPDATE amount = VALUES(amount), updated_at = CURRENT_TIMESTAMP');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Unable to prepare statement.']);
    exit;
}

mysqli_stmt_bind_param($stmt, 'sd', $type, $amount);
$ok = mysqli_stmt_execute($stmt);
if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'Unable to save budget: ' . mysqli_error($conn)]);
    exit;
}

mysqli_stmt_close($stmt);

echo json_encode(['success' => true, 'message' => 'Budget saved successfully.', 'type' => $type, 'amount' => $amount]);
