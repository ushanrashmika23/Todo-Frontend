<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/db.php';

$type = $_GET['type'] ?? null;
if ($type && !in_array($type, ['weekly','monthly'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid type']);
    exit;
}

if ($type) {
    $stmt = mysqli_prepare($conn, 'SELECT `type`, amount, updated_at FROM budgets WHERE `type` = ? LIMIT 1');
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Unable to prepare statement.']);
        exit;
    }
    mysqli_stmt_bind_param($stmt, 's', $type);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$row) {
        echo json_encode(['success' => true, 'type' => $type, 'amount' => '0.00']);
        exit;
    }

    echo json_encode(['success' => true, 'type' => $row['type'], 'amount' => number_format((float)$row['amount'], 2, '.', ''), 'updated_at' => $row['updated_at']]);
    exit;
}

// return all budgets
$result = mysqli_query($conn, 'SELECT `type`, amount, updated_at FROM budgets');
$budgets = [];
while ($row = mysqli_fetch_assoc($result)) {
    $budgets[$row['type']] = number_format((float)$row['amount'], 2, '.', '');
}

echo json_encode(['success' => true, 'budgets' => $budgets]);
