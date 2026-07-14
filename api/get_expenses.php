<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../database/db.php';

// Optional date filter (YYYY-MM-DD)
$date = $_GET['date'] ?? null;

if ($date) {
    $stmt = mysqli_prepare($conn, "SELECT id, expense_date, category, description, amount, payment_method, created_at FROM expenses WHERE expense_date = ? ORDER BY created_at DESC, id DESC");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Unable to prepare expenses query.']);
        exit;
    }
    mysqli_stmt_bind_param($stmt, 's', $date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    $query = "SELECT id, expense_date, category, description, amount, payment_method, created_at FROM expenses ORDER BY created_at DESC, id DESC";
    $result = mysqli_query($conn, $query);
}

if (!$result) {
    echo json_encode(['success' => false, 'message' => 'Unable to load expenses.']);
    exit;
}

$expenses = [];

while ($row = mysqli_fetch_assoc($result)) {
    $expenses[] = [
        'id' => (int) $row['id'],
        'date' => date('d M Y', strtotime($row['expense_date'])),
        'date_key' => date('Y-m-d', strtotime($row['expense_date'])),
        'category' => $row['category'],
        'description' => $row['description'],
        'payment_method' => $row['payment_method'],
        'amount' => number_format((float) $row['amount'], 2, '.', ''),
        'created_at' => $row['created_at']
    ];
}

echo json_encode(['success' => true, 'expenses' => $expenses]);
