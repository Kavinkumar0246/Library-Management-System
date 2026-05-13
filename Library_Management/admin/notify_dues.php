<?php
session_start();
include("../config/db.php");

/* SECURITY CHECK */
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!="admin"){
    header("Location: ../login.php");
    exit();
}

$success = "";
if(isset($_POST['notify_id'])){
    // In a real application, send actual email here.
    // For now, we simulate success.
    $success = "Email notification sent to student successfully!";
}

include("../includes/header.php");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Overdue Book Returns</h2>
    <a href="dashboard.php" class="btn">Back to Dashboard</a>
</div>

<?php if($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<div class="card" style="padding: 1.5rem;">
    <p style="margin-bottom: 1.5rem; color: #94a3b8;">The following students have not returned their books within the 7-day borrowing period.</p>
    
    <table class="table-premium">
        <thead>
            <tr>
                <th>Student</th>
                <th>Book Details</th>
                <th>Issue Date</th>
                <th>Overdue By</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Mock logic: consider anything older than 7 days as overdue
            $query = "SELECT bi.*, b.title, u.name, u.email 
                      FROM book_issues bi 
                      JOIN books b ON bi.book_id = b.id 
                      JOIN users u ON bi.user_id = u.id
                      WHERE bi.return_date IS NULL AND bi.issue_date < DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            $res = $conn->query($query);
            
            if($res->num_rows > 0):
                while($row=$res->fetch_assoc()): 
                    $overdue_days = floor((time() - strtotime($row['issue_date'] . ' + 7 days')) / (60 * 60 * 24));
            ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
                        <span style="font-size:0.8rem; color:#94a3b8;"><?php echo htmlspecialchars($row['email']); ?></span>
                    </td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($row['issue_date'])); ?></td>
                    <td><span class="badge warning"><?php echo max(1, $overdue_days); ?> days</span></td>
                    <td>
                        <form method="POST" style="margin:0; padding:0; background:none; border:none; box-shadow:none;">
                            <input type="hidden" name="notify_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn" style="background: var(--danger); padding: 0.4rem 0.8rem; font-size: 0.8rem;">Send Email</button>
                        </form>
                    </td>
                </tr>
            <?php 
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: #94a3b8;">No overdue issues right now. Great!</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>
