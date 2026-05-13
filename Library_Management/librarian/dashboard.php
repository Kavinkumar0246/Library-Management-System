<?php
session_start();
include("../config/db.php");

/* SECURITY CHECK */
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!="librarian"){
    header("Location: ../login.php");
    exit();
}

$issues_count = $conn->query("SELECT COUNT(*) as c FROM book_issues WHERE return_date IS NULL")->fetch_assoc()['c'];

include("../includes/header.php");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Librarian Panel</h2>
</div>

<div class="dashboard-grid">
    <div class="stat-card">
        <h3>Books Currently Issued</h3>
        <div class="value" style="color: var(--primary-color);"><?php echo $issues_count; ?></div>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 1.5rem;">Recent Checkouts</h3>
    <table class="table-premium">
        <thead>
            <tr>
                <th>Student</th>
                <th>Book Title</th>
                <th>Issue Date</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $recent_issues = $conn->query("SELECT bi.*, b.title, u.name 
                      FROM book_issues bi 
                      JOIN books b ON bi.book_id = b.id 
                      JOIN users u ON bi.user_id = u.id
                      ORDER BY bi.id DESC LIMIT 5");
            if($recent_issues->num_rows > 0):
                while($row=$recent_issues->fetch_assoc()): 
            ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($row['issue_date'])); ?></td>
                </tr>
            <?php 
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="3" style="text-align: center; color: #94a3b8; padding: 2rem;">No recent checkouts.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>