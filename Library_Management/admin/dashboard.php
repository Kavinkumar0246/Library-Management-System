<?php
session_start();
include("../config/db.php");

/* SECURITY CHECK */
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!="admin"){
    header("Location: ../login.php");
    exit();
}

if($_POST){
    $t = $_POST['title'];
    $a = $_POST['author'];
    $q = (int)$_POST['qty'];
    $dept = $_POST['department'];
    $block = $_POST['block'];
    $row_num = (int)$_POST['row_num'];
    $col_num = (int)$_POST['column_num'];

    $stmt = $conn->prepare("INSERT INTO books(title,author,quantity,available_quantity, department, block, row_num, column_num) VALUES(?,?,?,?,?,?,?,?)");
    $stmt->bind_param("ssiisssi", $t, $a, $q, $q, $dept, $block, $row_num, $col_num);
    $stmt->execute();
}

$books_count = $conn->query("SELECT COUNT(*) as c FROM books")->fetch_assoc()['c'];
$users_count = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='student'")->fetch_assoc()['c'];
$issues_count = $conn->query("SELECT COUNT(*) as c FROM book_issues WHERE return_date IS NULL")->fetch_assoc()['c'];

include("../includes/header.php");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Admin Dashboard</h2>
    <a href="notify_dues.php" class="btn" style="background: var(--danger);">🔔 Notify Overdue</a>
</div>

<div class="dashboard-grid">
    <div class="stat-card">
        <h3>Total Books</h3>
        <div class="value" style="color: var(--primary-color);"><?php echo $books_count; ?></div>
    </div>
    <div class="stat-card">
        <h3>Registered Students</h3>
        <div class="value" style="color: #c084fc;"><?php echo $users_count; ?></div>
    </div>
    <div class="stat-card">
        <h3>Active Issues</h3>
        <div class="value" style="color: #10b981;"><?php echo $issues_count; ?></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
    
    <div class="card" style="margin-bottom: 0;">
        <h3 style="margin-bottom: 1.5rem;">Add New Book</h3>
        <form method="POST" style="padding: 0; border: none; box-shadow: none; background: transparent;">
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" class="form-control" required>
            </div>
            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>Quantity</label>
                    <input type="number" name="qty" class="form-control" required min="1">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Block</label>
                    <input type="text" name="block" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label>Department</label>
                <input type="text" name="department" class="form-control" required>
            </div>
            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>Row</label>
                    <input type="number" name="row_num" class="form-control" required min="1">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Column</label>
                    <input type="number" name="column_num" class="form-control" required min="1">
                </div>
            </div>
            <button type="submit" class="btn" style="width: 100%;">Save Book</button>
        </form>
    </div>

    <div class="card" style="margin-bottom: 0;">
        <h3 style="margin-bottom: 1.5rem;">Latest Catalog Additions</h3>
        <table class="table-premium">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Dept. Location</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $recent_books = $conn->query("SELECT * FROM books ORDER BY id DESC LIMIT 5");
                while($row=$recent_books->fetch_assoc()): 
                ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['title']); ?></strong><br><span style="font-size:0.8rem; color:#94a3b8;"><?php echo htmlspecialchars($row['author']); ?></span></td>
                        <td><?php echo htmlspecialchars($row['department']); ?> (<?php echo htmlspecialchars($row['block']); ?>-<?php echo htmlspecialchars($row['row_num']); ?>-<?php echo htmlspecialchars($row['column_num']); ?>)</td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<?php include("../includes/footer.php"); ?>