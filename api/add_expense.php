<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../database/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Only POST requests are allowed.']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!is_array($data)) {
    $data = $_POST;
}

$date = trim($data['date'] ?? '');
$category = trim($data['category'] ?? '');
$description = trim($data['description'] ?? '');
$amount = trim($data['amount'] ?? '');
$paymentMethod = trim($data['payment_method'] ?? '');

$errors = [];

if ($date === '') {
    $errors[] = 'Date is required.';
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

$stmt = mysqli_prepare($conn, 'INSERT INTO expenses (expense_date, category, description, amount, payment_method) VALUES (?, ?, ?, ?, ?)');

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Unable to prepare statement: ' . mysqli_error($conn)]);
    exit;
}

$amountValue = number_format((float) $amount, 2, '.', '');
mysqli_stmt_bind_param($stmt, 'sssss', $date, $category, $description, $amountValue, $paymentMethod);

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
    'expense_id' => $expenseId
]);
