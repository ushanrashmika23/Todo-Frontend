<?php
$host = 'localhost';
$user = 'root';
$password = '1234';
$dbname = 'db_todo';

$conn = mysqli_connect($host, $user, $password);

if (!$conn) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . mysqli_connect_error()
    ]));
}

if (!mysqli_select_db($conn, $dbname)) {
    if (!mysqli_query($conn, "CREATE DATABASE IF NOT EXISTS `$dbname`")) {
        die(json_encode([
            'success' => false,
            'message' => 'Unable to create database: ' . mysqli_error($conn)
        ]));
    }

    if (!mysqli_select_db($conn, $dbname)) {
        die(json_encode([
            'success' => false,
            'message' => 'Unable to select database: ' . mysqli_error($conn)
        ]));
    }
}

mysqli_set_charset($conn, 'utf8mb4');

function ensure_expenses_table($conn)
{
    $sql = "
        CREATE TABLE IF NOT EXISTS expenses (
            id INT AUTO_INCREMENT PRIMARY KEY,
            expense_date DATE NOT NULL,
            category VARCHAR(100) NOT NULL,
            description TEXT NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            payment_method VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";

    if (!mysqli_query($conn, $sql)) {
        throw new Exception('Unable to create expenses table: ' . mysqli_error($conn));
    }
}

function ensure_incomes_table($conn)
{
    $sql = "
        CREATE TABLE IF NOT EXISTS incomes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            income_date DATE NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            description VARCHAR(200) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";

    if (!mysqli_query($conn, $sql)) {
        throw new Exception('Unable to create incomes table: ' . mysqli_error($conn));
    }
}

function ensure_budgets_table($conn)
{
    $sql = "
        CREATE TABLE IF NOT EXISTS budgets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            `type` VARCHAR(10) NOT NULL UNIQUE,
            amount DECIMAL(12,2) NOT NULL DEFAULT 0,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

    if (!mysqli_query($conn, $sql)) {
        throw new Exception('Unable to create budgets table: ' . mysqli_error($conn));
    }
}

try {
    ensure_expenses_table($conn);
    ensure_incomes_table($conn);
    ensure_budgets_table($conn);

    $incomeCheck = mysqli_query($conn, 'SELECT COUNT(*) AS total FROM incomes');
    if ($incomeCheck) {
        $incomeRow = mysqli_fetch_assoc($incomeCheck);
        if ((int) $incomeRow['total'] === 0) {
            $defaultDate = date('Y-m-d');
            $defaultAmount = '5000.00';
            mysqli_query($conn, "INSERT INTO incomes (income_date, amount, description) VALUES ('$defaultDate', '$defaultAmount', 'Default monthly income')");
        }
    }
} catch (Exception $e) {
    die(json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]));
}
