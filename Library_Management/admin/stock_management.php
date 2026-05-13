<?php
session_start();
include("../config/db.php");

/* SECURITY CHECK */
if(!isset($_SESSION['user']) || $_SESSION['user']['role']!="admin"){
    header("Location: ../login.php");
    exit();
}

$success = "";
// Optionally handle simple stock updates here if someone submits a form
if($_POST && isset($_POST['update_stock_id'])) {
    $id = (int)$_POST['update_stock_id'];
    $new_qty = (int)$_POST['new_qty'];
    
    // Calculate the difference to adjust available_quantity appropriately
    $stmt = $conn->query("SELECT quantity, available_quantity FROM books WHERE id=$id");
    if($stmt && $stmt->num_rows > 0) {
        $b = $stmt->fetch_assoc();
        $diff = $new_qty - $b['quantity'];
        $new_avail = $b['available_quantity'] + $diff;
        
        $conn->query("UPDATE books SET quantity=$new_qty, available_quantity=$new_avail WHERE id=$id");
        $success = "Stock quantity updated successfully!";
    }
}

include("../includes/header.php");
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2>Book Stock Management</h2>
    <div>
        <a href="dashboard.php" class="btn" style="background: transparent; border: 1px solid var(--border-color); margin-right: 1rem;">Back to Dashboard</a>
        <a href="dashboard.php" class="btn">➕ Add New Book</a>
    </div>
</div>

<?php if($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div class="card" style="padding: 1.5rem;">
    <p style="margin-bottom: 1.5rem; color: #94a3b8;">Manage your total inventory, verify physical library coordinates, and adjust stock counts directly.</p>
    
    <table class="table-premium">
        <thead>
            <tr>
                <th>Book Details</th>
                <th>Physical Location</th>
                <th>Total Stock</th>
                <th>Available</th>
                <th>Update Stock</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $query = "SELECT * FROM books ORDER BY title ASC";
            $res = $conn->query($query);
            
            if($res->num_rows > 0):
                while($row=$res->fetch_assoc()): 
            ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($row['title']); ?></strong><br>
                        <span style="font-size:0.8rem; color:#94a3b8;"><?php echo htmlspecialchars($row['author']); ?></span>
                    </td>
                    <td>
                        🏠 <?php echo htmlspecialchars($row['department']); ?><br>
                        <span style="font-size:0.8rem; color:#94a3b8;">
                            Blk <?php echo htmlspecialchars($row['block']); ?> | R<?php echo htmlspecialchars($row['row_num']); ?> | C<?php echo htmlspecialchars($row['column_num']); ?>
                        </span>
                    </td>
                    <td><span class="badge" style="background: rgba(255,255,255,0.1);"><?php echo $row['quantity']; ?> Total</span></td>
                    <td>
                        <?php if($row['available_quantity'] > 0): ?>
                            <span class="badge success"><?php echo $row['available_quantity']; ?> Available</span>
                        <?php else: ?>
                            <span class="badge warning">Out of Stock</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" style="margin:0; padding:0; background:none; border:none; box-shadow:none; display:flex; gap:0.5rem; max-width: 200px;">
                            <input type="hidden" name="update_stock_id" value="<?php echo $row['id']; ?>">
                            <input type="number" name="new_qty" class="form-control" style="padding: 0.25rem 0.5rem;" value="<?php echo $row['quantity']; ?>" min="1">
                            <button type="submit" class="btn" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;">Update</button>
                        </form>
                    </td>
                </tr>
            <?php 
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: #94a3b8;">No books exist in the inventory.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include("../includes/footer.php"); ?>
