<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library System</title>
    <!-- CSS is loaded from the root or sub-directory -->
    <link rel="stylesheet" href="/Library_Management/includes/style.css">
</head>
<body>

<header>
    <h1>📚 Library System</h1>
    <div class="nav-links">
        <?php if(isset($_SESSION['user'])): ?>
            <?php if($_SESSION['user']['role'] === 'student'): ?>
                <a href="/Library_Management/dashboard.php">Dashboard</a>
                <a href="/Library_Management/search.php">Catalog</a>
                <a href="/Library_Management/eresources.php">E-Resources</a>
                <a href="/Library_Management/mybooks.php">My Books</a>
            <?php elseif($_SESSION['user']['role'] === 'admin'): ?>
                <a href="/Library_Management/admin/dashboard.php">Admin Panel</a>
                <a href="/Library_Management/admin/manage_students.php">Students</a>
                <a href="/Library_Management/admin/stock_management.php">Stock</a>
                <a href="/Library_Management/admin/notify_dues.php">Notify</a>
            <?php endif; ?>
            <a href="/Library_Management/logout.php" class="btn" style="padding: 0.4rem 1rem;">Logout (<?= htmlspecialchars($_SESSION['user']['name']) ?>)</a>
        <?php else: ?>
            <a href="/Library_Management/login.php">Login</a>
            <a href="/Library_Management/register.php" class="btn" style="padding: 0.4rem 1rem;">Register</a>
        <?php endif; ?>
    </div>
</header>
<div class="container">
