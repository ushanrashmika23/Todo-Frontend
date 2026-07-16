<?php
// Central auth helpers (sessions + login protection)

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function currentUser(): ?array
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    return [
        'id' => (int) $_SESSION['user_id'],
        'name' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
    ];
}

function requireLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        // Redirect for protected pages
        header('Location: /Todo-Frontend/index.php');
        exit;
    }
}

