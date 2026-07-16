<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../database/db.php';
require_once __DIR__ . '/../auth/auth.php';

// Protect API: must be logged in
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST requests are allowed.']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!is_array($data)) {
    $data = $_POST;
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}
$userId = (int) $userId;

$date = trim($data['date'] ?? '');
$category = trim($data['category'] ?? '');
$description = trim($data['description'] ?? '');
$amount = trim($data['amount'] ?? '');
$paymentMethod = trim($data['payment_method'] ?? '');

$errors = [];

if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    $errors[] = 'Date is required and must be in YYYY-MM-DD format.';
}

if ($category === '') {
    $errors[] = 'Category is required.';
}

if ($description === '') {
    $errors[] = 'Description cannot be empty.';
}

if ($paymentMethod === '') {
    $errors[] = 'Payment method is required.';
}

if ($amount === '' || !is_numeric($amount) || (float) $amount <= 0) {
    $errors[] = 'Amount must be greater than 0.';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

$stmt = mysqli_prepare(
    $conn,
    'INSERT INTO expenses (user_id, expense_date, category, description, amount, payment_method)
     VALUES (?, ?, ?, ?, ?, ?)'
);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Unable to prepare statement: ' . mysqli_error($conn)]);
    exit;
}

$amountValue = number_format((float) $amount, 2, '.', '');
mysqli_stmt_bind_param($stmt, 'isssds', $userId, $date, $category, $description, $amountValue, $paymentMethod);

if (!mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save expense: ' . mysqli_error($conn)]);
    mysqli_stmt_close($stmt);
    exit;
}

$expenseId = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

echo json_encode([
    'success' => true,
    'message' => 'Expense saved successfully.',
    'expense_id' => (int) $expenseId
]);

