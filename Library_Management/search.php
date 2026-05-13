<?php
session_start();
if(!isset($_SESSION['user'])){ header("Location: login.php"); exit(); }
include("config/db.php");

$success_msg = "";
$error_msg = "";

// Handle Issue Request
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_issue_id']) && $_SESSION['user']['role'] == 'student') {
    $book_id = (int)$_POST['request_issue_id'];
    $user_id = $_SESSION['user']['id'];
    
    // Check available quantity
    $check = $conn->query("SELECT available_quantity FROM books WHERE id=$book_id");
    if($check && $check->num_rows > 0) {
        $b = $check->fetch_assoc();
        if($b['available_quantity'] > 0) {
            // Check if user already has this book active
            $user_check = $conn->query("SELECT id FROM book_issues WHERE book_id=$book_id AND user_id=$user_id AND return_date IS NULL");
            if($user_check->num_rows > 0) {
                $error_msg = "You have already borrowed this book!";
            } else {
                $conn->begin_transaction();
                try {
                    $conn->query("UPDATE books SET available_quantity = available_quantity - 1 WHERE id=$book_id");
                    $conn->query("INSERT INTO book_issues (book_id, user_id, issue_date) VALUES ($book_id, $user_id, CURDATE())");
                    $conn->commit();
                    $success_msg = "Book issued successfully! Check 'My Books'.";
                } catch(Exception $e) {
                    $conn->rollback();
                    $error_msg = "Failed to issue book.";
                }
            }
        } else {
            $error_msg = "Book is currently out of stock!";
        }
    }
}

include("includes/header.php");

$q = $_GET['q'] ?? "";
$sort = $_GET['sort'] ?? "title ASC";

$valid_sorts = [
    "title ASC" => "Title (A-Z)",
    "title DESC" => "Title (Z-A)",
    "author ASC" => "Author (A-Z)",
    "author DESC" => "Author (Z-A)"
];
if(!array_key_exists($sort, $valid_sorts)) $sort = "title ASC";

$stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? ORDER BY $sort");
$like_q = "%$q%";
$stmt->bind_param("ss", $like_q, $like_q);
$stmt->execute();
$res = $stmt->get_result();
?>

<h2>Book Catalog</h2>

<?php if($success_msg): ?>
    <div class="alert alert-success"><?php echo $success_msg; ?></div>
<?php endif; ?>
<?php if($error_msg): ?>
    <div class="alert alert-error"><?php echo $error_msg; ?></div>
<?php endif; ?>

<div class="card" style="padding: 1.5rem; margin-bottom: 2rem;">
    <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end; margin: 0; padding: 0; background: none; border: none; box-shadow: none;">
        <div style="flex: 1; min-width: 250px;">
            <label style="display:block; margin-bottom: 0.5rem; color: #94a3b8;">Search Catalog:</label>
            <input type="text" name="q" class="form-control" placeholder="Search by Title or Author..." value="<?php echo htmlspecialchars($q); ?>">
        </div>
        
        <div>
            <label style="display:block; margin-bottom: 0.5rem; color: #94a3b8;">Sort By:</label>
            <select name="sort" class="form-control" onchange="this.form.submit()">
                <?php foreach($valid_sorts as $val => $label): ?>
                    <option value="<?php echo $val; ?>" <?php echo $sort === $val ? 'selected' : ''; ?>><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <button type="submit" class="btn">Search</button>
        </div>
    </form>
</div>

<?php if($res->num_rows > 0): ?>
    <table class="table-premium">
        <thead>
            <tr>
                <th>Title & Author</th>
                <th>Availability</th>
                <th>Physical Location</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $res->fetch_assoc()): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                        <span style="color: #94a3b8; font-size: 0.85rem;">By <?php echo htmlspecialchars($row['author']); ?></span>
                    </td>
                    <td>
                        <?php if($row['available_quantity'] > 0): ?>
                            <span class="badge success"><?php echo $row['available_quantity']; ?> copies available</span>
                        <?php else: ?>
                            <span class="badge warning">Waitlist</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div>🏠 <?php echo htmlspecialchars($row['department']); ?> Dept.</div>
                        <div style="color: #94a3b8; font-size: 0.85rem;">Block <?php echo htmlspecialchars($row['block']); ?> | Row <?php echo htmlspecialchars($row['row_num']); ?> | Col <?php echo htmlspecialchars($row['column_num']); ?></div>
                    </td>
                    <td>
                        <?php if($row['available_quantity'] > 0 && $_SESSION['user']['role'] == 'student'): ?>
                            <form method="POST" style="margin:0; padding:0; background:none; border:none; box-shadow:none;">
                                <input type="hidden" name="request_issue_id" value="<?php echo $row['id']; ?>">
                                <button class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;">Request Issue</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-error">No books found matching your criteria.</div>
<?php endif; ?>

<?php include("includes/footer.php"); ?>