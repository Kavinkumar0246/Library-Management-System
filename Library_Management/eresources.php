<?php
session_start();
if(!isset($_SESSION['user'])){ header("Location: login.php"); exit(); }
include("config/db.php");
include("includes/header.php");

$type_filter = $_GET['type'] ?? '';

$query = "SELECT * FROM eresources";
if($type_filter){
    $query .= " WHERE type='" . $conn->real_escape_string($type_filter) . "'";
}
$res = $conn->query($query);
?>

<h2>E-Resources & Digital Library</h2>
<p style="color: #94a3b8; margin-bottom: 2rem;">Access our premium collection of digital journals, newspapers, and e-books from anywhere.</p>

<div style="margin-bottom: 1.5rem;">
    <a href="?type=" class="badge <?php echo !$type_filter ? 'success' : ''; ?>" style="padding: 0.5rem 1rem; text-decoration: none; border: 1px solid var(--border-color);">All</a>
    <a href="?type=ebook" class="badge <?php echo $type_filter=='ebook' ? 'success' : ''; ?>" style="padding: 0.5rem 1rem; text-decoration: none; border: 1px solid var(--border-color);">E-Books</a>
    <a href="?type=newspaper" class="badge <?php echo $type_filter=='newspaper' ? 'success' : ''; ?>" style="padding: 0.5rem 1rem; text-decoration: none; border: 1px solid var(--border-color);">Newspapers</a>
    <a href="?type=journal" class="badge <?php echo $type_filter=='journal' ? 'success' : ''; ?>" style="padding: 0.5rem 1rem; text-decoration: none; border: 1px solid var(--border-color);">Journals</a>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
    <?php while($row = $res->fetch_assoc()): ?>
        <div class="card" style="margin-bottom: 0;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <span class="badge warning" style="text-transform: capitalize;"><?php echo htmlspecialchars($row['type']); ?></span>
                <span style="font-size: 0.8rem; color: #94a3b8;">Added: <?php echo htmlspecialchars($row['added_on']); ?></span>
            </div>
            
            <h3 style="margin: 1rem 0;"><?php echo htmlspecialchars($row['title']); ?></h3>
            
            <a href="<?php echo htmlspecialchars($row['link']); ?>" target="_blank" class="btn" style="width: 100%; text-align: center;">Access Resource</a>
        </div>
    <?php endwhile; ?>
</div>

<?php if($res->num_rows == 0): ?>
    <div class="alert alert-warning">No e-resources found in this category.</div>
<?php endif; ?>

<?php include("includes/footer.php"); ?>
