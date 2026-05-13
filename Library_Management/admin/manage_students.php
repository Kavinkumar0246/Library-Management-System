<?php
session_start();
include("../config/db.php");

/* SECURITY CHECK */
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!="admin"){
    header("Location: ../login.php");
    exit();
}

include("../includes/header.php");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Student Directory</h2>
    <a href="dashboard.php" class="btn">Back to Dashboard</a>
</div>

<div class="card" style="padding: 1.5rem;">
    <p style="margin-bottom: 1.5rem; color: #94a3b8;">Below is the complete data table of all registered students, their engagement streaks, and active borrowing counts.</p>
    
    <table class="table-premium">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Email Address</th>
                <th>Reading Streak</th>
                <th>Active Issued Books</th>
                <th>Registered/Last Login</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $query = "
                SELECT u.id, u.name, u.email, u.reading_streak, u.last_login_date,
                (SELECT COUNT(*) FROM book_issues bi WHERE bi.user_id = u.id AND bi.return_date IS NULL) as active_issues
                FROM users u 
                WHERE u.role = 'student'
                ORDER BY u.name ASC
            ";
            $res = $conn->query($query);
            
            if($res->num_rows > 0):
                while($row=$res->fetch_assoc()): 
            ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><span class="badge success">🔥 <?php echo $row['reading_streak']; ?></span></td>
                    <td>
                        <?php if($row['active_issues'] > 0): ?>
                            <span class="badge warning"><?php echo $row['active_issues']; ?> books</span>
                        <?php else: ?>
                            <span style="color: #94a3b8;">None</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo $row['last_login_date'] ? date('M d, Y', strtotime($row['last_login_date'])) : 'Never logged in'; ?>
                    </td>
                </tr>
            <?php 
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: #94a3b8;">No students registered yet.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>
