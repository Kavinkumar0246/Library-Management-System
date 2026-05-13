<?php
session_start();
include("config/db.php");
$error = "";

if($_POST){
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if($row = $res->fetch_assoc()){
        if(password_verify($pass, $row['password'])){
            $_SESSION['user'] = $row;
            // Update last login
            $conn->query("UPDATE users SET last_login_date = CURDATE() WHERE id=".$row['id']);
            
            if($row['role'] == "admin") header("Location: admin/dashboard.php");
            else if($row['role'] == "librarian") header("Location: librarian/dashboard.php");
            else header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid Credentials";
        }
    } else {
        $error = "User not found";
    }
}
include("includes/header.php");
?>

<div style="max-width: 400px; margin: 4rem auto;">
    <form method="POST" autocomplete="off" class="card">
        <h2 style="text-align: center; margin-bottom: 2rem;">Welcome Back</h2>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required autocomplete="off">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn" style="width: 100%;">Login</button>

        <div style="text-align: center; margin-top: 1rem;">
            Don't have an account? <a href="register.php" style="color: var(--primary-color);">Register</a>
        </div>
    </form>
</div>

<?php include("includes/footer.php"); ?>