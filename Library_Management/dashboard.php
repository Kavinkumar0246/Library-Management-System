<?php
session_start();
if(!isset($_SESSION['user'])){ header("Location: login.php"); exit(); }
if($_SESSION['user']['role'] !== 'student'){ header("Location: index.php"); exit(); }
include("config/db.php");
include("includes/header.php");

$user_id = $_SESSION['user']['id'];
$user_info = $conn->query("SELECT * FROM users WHERE id=$user_id")->fetch_assoc();

// Count borrowed books
$borrowed_count = $conn->query("SELECT COUNT(*) as c FROM book_issues WHERE user_id=$user_id AND return_date IS NULL")->fetch_assoc()['c'];

?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Student Dashboard</h2>
    <div class="badge success" style="font-size: 1rem; padding: 0.5rem 1rem;">
        🔥 <?php echo $user_info['reading_streak']; ?> Day Reading Streak!
    </div>
</div>

<div class="dashboard-grid">
    <div class="stat-card">
        <h3>Books Borrowed</h3>
        <div class="value" style="color: var(--primary-color);"><?php echo $borrowed_count; ?></div>
    </div>
    <div class="stat-card">
        <h3>Account Status</h3>
        <div class="value" style="color: #10b981; font-size: 1.5rem; line-height: 2.5rem;">Active</div>
    </div>
    <div class="stat-card">
        <h3>Last Login</h3>
        <div class="value" style="font-size: 1.25rem; line-height: 2.5rem; color: #cbd5e1;"><?php echo $user_info['last_login_date'] ?: 'Today'; ?></div>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
    <div class="card" style="margin-bottom: 0;">
        <h3>Quick Actions</h3>
        <ul style="list-style: none; margin-top: 1rem;">
            <li style="margin-bottom: 1rem;"><a href="search.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">🔍 Browse Catalog</a></li>
            <li style="margin-bottom: 1rem;"><a href="mybooks.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">📚 View My Books</a></li>
            <li><a href="eresources.php" style="color: var(--primary-color); text-decoration: none; font-weight: bold;">🌐 Access E-Resources</a></li>
        </ul>
    </div>
    
    <div class="card" style="margin-bottom: 0;">
        <h3>Recent Announcements</h3>
        <div style="margin-top: 1rem; border-left: 3px solid var(--primary-color); padding-left: 1rem; margin-bottom: 1rem;">
            <strong style="display: block;">New Digital Journals Added</strong>
            <span style="font-size: 0.85rem; color: #94a3b8;">Yesterday</span>
            <p style="font-size: 0.9rem; margin-top: 0.25rem;">We've added 50+ new academic journals to our e-resources portal.</p>
        </div>
    </div>
</div>

<?php include("includes/footer.php"); ?>