<?php
session_start();
include("config/db.php");
$success = "";
$error = "";

if($_POST){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if($stmt->get_result()->num_rows > 0){
        $error = "Email already registered";
    } else {
        $stmt = $conn->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,'student')");
        $stmt->bind_param("sss", $name, $email, $pass);
        if($stmt->execute()){
            $success = "Registration successful! You can now login.";
        } else {
            $error = "An error occurred.";
        }
    }
}
include("includes/header.php");
?>

<div style="max-width: 400px; margin: 4rem auto;">
    <form method="POST" class="card">
        <h2 style="text-align: center; margin-bottom: 2rem;">Create Account</h2>

        <?php if($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn" style="width: 100%;">Register</button>

        <div style="text-align: center; margin-top: 1rem;">
            Already have an account? <a href="login.php" style="color: var(--primary-color);">Login</a>
        </div>
    </form>
</div>

<?php include("includes/footer.php"); ?>