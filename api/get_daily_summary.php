<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/db.php';

$date = $_GET['date'] ?? date('Y-m-d');

$stmt = mysqli_prepare($conn, 'SELECT id, category, description, payment_method, amount, expense_date FROM expenses WHERE expense_date = ? ORDER BY created_at DESC, id DESC');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Unable to prepare daily summary query.']);
    exit;
}

mysqli_stmt_bind_param($stmt, 's', $date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$expenses = [];
$total = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $amount = (float) $row['amount'];
    $total += $amount;
    $expenses[] = [
        'id' => (int) $row['id'],
        'category' => $row['category'],
        'description' => $row['description'],
        'payment_method' => $row['payment_method'],
        'amount' => number_format($amount, 2, '.', ''),
        'date' => date('d M Y', strtotime($row['expense_date']))
    ];
}

mysqli_stmt_close($stmt);

echo json_encode([
    'success' => true,
    'date' => date('F j, Y', strtotime($date)),
    'total' => number_format($total, 2, '.', ''),
    'transactions' => count($expenses),
    'expenses' => $expenses
]);
