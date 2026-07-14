<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/db.php';

$date = $_GET['date'] ?? date('Y-m-d');

// Prepared statement to get total and transaction count directly from the database
$summaryStmt = mysqli_prepare($conn, 'SELECT COALESCE(SUM(amount), 0) AS total, COUNT(*) AS transactions FROM expenses WHERE expense_date = ?');
if (!$summaryStmt) {
    echo json_encode(['success' => false, 'message' => 'Unable to prepare summary query.']);
    exit;
}
mysqli_stmt_bind_param($summaryStmt, 's', $date);
mysqli_stmt_execute($summaryStmt);
$summaryResult = mysqli_stmt_get_result($summaryStmt);
$summaryRow = mysqli_fetch_assoc($summaryResult);
$total = (float) $summaryRow['total'];
$transactions = (int) $summaryRow['transactions'];
mysqli_stmt_close($summaryStmt);

// Prepared statement to fetch expense rows for the date
$stmt = mysqli_prepare($conn, 'SELECT id, category, description, payment_method, amount, expense_date FROM expenses WHERE expense_date = ? ORDER BY created_at DESC, id DESC');
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Unable to prepare daily summary query.']);
    exit;
}
mysqli_stmt_bind_param($stmt, 's', $date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$expenses = [];
while ($row = mysqli_fetch_assoc($result)) {
    $amount = (float) $row['amount'];
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
    'transactions' => $transactions,
    'expenses' => $expenses
]);
