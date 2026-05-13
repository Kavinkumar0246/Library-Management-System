<?php
session_start();
if(isset($_SESSION['user'])){
    if($_SESSION['user']['role'] == 'admin') header("Location: admin/dashboard.php");
    else if($_SESSION['user']['role'] == 'librarian') header("Location: librarian/dashboard.php");
    else header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library System</title>
    <link rel="stylesheet" href="includes/style.css">
    <style>
        .hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 80px);
            text-align: center;
        }
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: -webkit-linear-gradient(#60a5fa, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .hero p {
            font-size: 1.25rem;
            color: #94a3b8;
            max-width: 600px;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .hero-buttons {
            display: flex;
            gap: 1rem;
        }
        .hero-buttons .btn {
            padding: 1rem 2rem;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

<header>
    <h1>📚 Library System</h1>
    <div class="nav-links">
        <a href="login.php" class="btn" style="padding: 0.5rem 1.5rem; background: transparent; border: 1px solid var(--primary-color);">Login</a>
        <a href="register.php" class="btn" style="padding: 0.5rem 1.5rem;">Register</a>
    </div>
</header>

<div class="container hero">
    <h1>Welcome to the Premium Library Network</h1>
    <p>Discover a huge collection of books, journals, newspapers and digital resources. Manage your readings, track your streaks and more seamlessly.</p>
    
    <div class="hero-buttons">
        <a href="login.php" class="btn">Access Catalog</a>
        <a href="register.php" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2);">Become a Member</a>
        <a href="staff_login.php" class="btn" style="background: transparent; border: 1px solid var(--danger); color: #f87171;">Staff Portal</a>
    </div>
</div>

<?php include("includes/footer.php"); ?>