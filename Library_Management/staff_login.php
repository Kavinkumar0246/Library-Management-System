<?php
session_start();
include("config/db.php");
$error = "";

if($_POST){
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND role IN ('admin', 'librarian')");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if($row = $res->fetch_assoc()){
        if(password_verify($pass, $row['password'])){
            $_SESSION['user'] = $row;
            // Update last login
            $conn->query("UPDATE users SET last_login_date = CURDATE() WHERE id=".$row['id']);
            
            if($row['role'] == "admin") header("Location: admin/dashboard.php");
            else header("Location: librarian/dashboard.php");
            exit();
        } else {
            $error = "Invalid Credentials";
        }
    } else {
        $error = "Access Denied. You are not authorized as staff.";
    }
}
include("includes/header.php");
?>

<div style="max-width: 400px; margin: 4rem auto;">
    <form method="POST" autocomplete="off" class="card" style="border-top: 4px solid var(--danger);">
        <div style="text-align: center; margin-bottom: 2rem;">
            <span class="badge" style="background: rgba(239, 68, 68, 0.2); color: #f87171; margin-bottom: 0.5rem; display: inline-block;">Secure Portal</span>
            <h2>Staff Login</h2>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label>Staff Email</label>
            <input type="email" name="email" class="form-control" required autocomplete="off" placeholder="admin@gmail.com">
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn" style="width: 100%; background: var(--danger);">Authorized Login Access</button>

        <div style="text-align: center; margin-top: 1.5rem; border-top: 1px solid var(--border-color); padding-top: 1rem;">
            Student? <a href="login.php" style="color: var(--primary-color);">Go to Student Portal</a>
        </div>
    </form>
</div>

<?php include("includes/footer.php"); ?>
