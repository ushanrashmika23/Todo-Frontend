<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../database/db.php';

$query = "
    SELECT id, expense_date, category, description, amount, payment_method, created_at
    FROM expenses
    ORDER BY created_at DESC, id DESC
";

$result = mysqli_query($conn, $query);

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
