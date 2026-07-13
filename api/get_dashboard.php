<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../database/db.php';

$incomeBaseline = 5000;

$totalSql = "SELECT COALESCE(SUM(amount), 0) AS total FROM expenses";
$todaySql = "SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE expense_date = CURDATE()";
$monthSql = "SELECT COALESCE(SUM(amount), 0) AS total FROM expenses WHERE MONTH(expense_date) = MONTH(CURDATE()) AND YEAR(expense_date) = YEAR(CURDATE())";

$totalResult = mysqli_query($conn, $totalSql);
$todayResult = mysqli_query($conn, $todaySql);
$monthResult = mysqli_query($conn, $monthSql);

if (!$totalResult || !$todayResult || !$monthResult) {
    echo json_encode(['success' => false, 'message' => 'Unable to load dashboard data.']);
    exit;
}

$totalRow = mysqli_fetch_assoc($totalResult);
$todayRow = mysqli_fetch_assoc($todayResult);
$monthRow = mysqli_fetch_assoc($monthResult);

$totalExpenses = (float) $totalRow['total'];
$todayExpenses = (float) $todayRow['total'];
$monthlyExpenses = (float) $monthRow['total'];
$remainingBalance = $incomeBaseline - $totalExpenses;

echo json_encode([
    'success' => true,
    'summary' => [
        'total_expenses' => number_format($totalExpenses, 2, '.', ''),
        'remaining_balance' => number_format($remainingBalance, 2, '.', ''),
        'today_expenses' => number_format($todayExpenses, 2, '.', ''),
        'monthly_expenses' => number_format($monthlyExpenses, 2, '.', '')
    ]
]);
