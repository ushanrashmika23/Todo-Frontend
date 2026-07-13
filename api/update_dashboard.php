<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/db.php';

$period = $_GET['period'] ?? 'weekly';
$range = $_GET['range'] ?? 'current';

function getPeriodTotal($conn, $period, $table, $amountColumn, $dateColumn)
{
    if ($period === 'weekly') {
        $sql = "SELECT COALESCE(SUM($amountColumn), 0) AS total FROM $table WHERE YEARWEEK($dateColumn, 1) = YEARWEEK(CURDATE(), 1)";
    } else {
        $sql = "SELECT COALESCE(SUM($amountColumn), 0) AS total FROM $table WHERE MONTH($dateColumn) = MONTH(CURDATE()) AND YEAR($dateColumn) = YEAR(CURDATE())";
    }

    $result = mysqli_query($conn, $sql);
    if (!$result) {
        return null;
    }

    $row = mysqli_fetch_assoc($result);
    return (float) $row['total'];
}

$expensesTotal = getPeriodTotal($conn, $period, 'expenses', 'amount', 'expense_date');
$incomeTotal = getPeriodTotal($conn, $period, 'incomes', 'amount', 'income_date');
$remainingBalance = $incomeTotal - $expensesTotal;

echo json_encode([
    'success' => true,
    'period' => $period,
    'expenses' => number_format($expensesTotal, 2, '.', ''),
    'remaining_balance' => number_format($remainingBalance, 2, '.', ''),
    'income' => number_format($incomeTotal, 2, '.', '')
]);
