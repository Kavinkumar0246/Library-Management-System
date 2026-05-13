<?php
session_start();
if(!isset($_SESSION['user'])){ header("Location: login.php"); exit(); }
if($_SESSION['user']['role'] !== 'student'){ header("Location: index.php"); exit(); }
include("config/db.php");
include("includes/header.php");

$user_id = $_SESSION['user']['id'];

$query = "SELECT bi.*, b.title, b.author 
          FROM book_issues bi 
          JOIN books b ON bi.book_id = b.id 
          WHERE bi.user_id = $user_id
          ORDER BY bi.issue_date DESC";
$res = $conn->query($query);
?>

<h2>My Bookshelf</h2>
<p style="color: #94a3b8; margin-bottom: 2rem;">Track your currently borrowed books and reading history.</p>

<?php if($res->num_rows > 0): ?>
    <table class="table-premium">
        <thead>
            <tr>
                <th>Book Title</th>
                <th>Author</th>
                <th>Issue Date</th>
                <th>Return Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td><?php echo date('M d, Y', strtotime($row['issue_date'])); ?></td>
                    <td>
                        <?php 
                        if($row['return_date']) {
                            echo date('M d, Y', strtotime($row['return_date'])); 
                        } else {
                            echo 'Not returned yet';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if($row['return_date']): ?>
                            <span class="badge" style="background: rgba(255,255,255,0.1);">Returned</span>
                        <?php else: ?>
                            <span class="badge success">Active</span>
                            <?php 
                                // Simple logic just to show due in 7 days from issue
                                $due = strtotime($row['issue_date'] . ' + 7 days');
                                if(time() > $due) {
                                    echo '<span class="badge warning" style="margin-top:0.25rem; display:inline-block;">Overdue</span>';
                                }
                            ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="card" style="text-align: center;">
        <h3 style="margin-bottom: 1rem;">No books borrowed yet!</h3>
        <p style="color: #94a3b8; margin-bottom: 1.5rem;">Explore our catalog and find your next great read.</p>
        <a href="search.php" class="btn">Browse Catalog</a>
    </div>
<?php endif; ?>

<?php include("includes/footer.php"); ?>