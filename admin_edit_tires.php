<?php
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

include 'supplierdb_connect.php';

// Fetch tire details for editing
if (isset($_GET['id'])) {
    $tire_id = $_GET['id'];
    try {
        $stmt = $conn->prepare("SELECT * FROM tires WHERE id = :id");
        $stmt->bindParam(':id', $tire_id);
        $stmt->execute();
        $tire = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tire) {
            echo "Tire not found.";
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

// Handle form submission for updating tire details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_name = $_POST['item_name'];
    $brand = $_POST['brand'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $amount = $_POST['amount'];
    $image = $_FILES['image']['name'];

    // File upload handling
    if (!empty($image)) {
        $target_dir = "storage/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    } else {
        $target_file = $tire['image'];
    }

    try {
        $stmt = $conn->prepare("
            UPDATE tires
            SET item_name = :item_name, brand = :brand, description = :description, price = :price, amount = :amount, image = :image
            WHERE id = :id
        ");
        $stmt->bindParam(':item_name', $item_name);
        $stmt->bindParam(':brand', $brand);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':image', $target_file);
        $stmt->bindParam(':id', $tire_id);
        $stmt->execute();

        header("Location: admin_all_tires.php");
    } catch (PDOException $e) {
        echo "Error updating tire: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tire</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include 'admin_header.php'; ?>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="admin_dashboard.php">Home</a>
            <a class="navbar-brand" href="admin_all_tires.php">All Tires</a>
            <a class="navbar-brand" href="admin_add_tires.php">Add Tires</a>
            <a class="navbar-brand" href="admin_user_history_orders.php">See Customer Order Log</a>
            <a class="navbar-brand" href="admin_user_list.php">List of Customers</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Tire Types
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="admin_tire_type_PCT.php">PCT</a>
                            <a class="dropdown-item" href="admin_tire_type_TSUVT.php">TSUVT</a>
                            <a class="dropdown-item" href="admin_tire_type_PT.php">PT</a>
                            <a class="dropdown-item" href="admin_tire_type_ST.php">ST</a>
                            <a class="dropdown-item" href="admin_tire_type_CHDT.php">CHDT</a>
                            <a class="dropdown-item" href="admin_tire_type_ORAT.php">ORAT</a>
                        </div>
                    </li>
                </ul>
            </div>
        
            <form class="form-inline my-2 my-lg-0 mr-2" method="POST" action="">
                <input  class="form-control mr-sm-2" type="search" placeholder="Search for anything" aria-label="Search" name="search_query">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="search">Search</button>
            </form>
        </div>
    </nav>


    <!-- Main Content -->
    <main class="container mt-4">
        <h2>Edit Tire</h2>
        <!--form to update products-->
        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="item_name">Item Name</label>
                <input type="text" class="form-control" id="item_name" name="item_name" value="<?php echo htmlspecialchars($tire['item_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" class="form-control" id="brand" name="brand" value="<?php echo htmlspecialchars($tire['brand']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($tire['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price</label>
                <input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($tire['price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" value="<?php echo htmlspecialchars($tire['amount']); ?>" required>
            </div>
            <div class="form-group">
                <label for="image">Image</label>
                <input type="file" class="form-control-file" id="image" name="image">
                <small class="form-text text-muted">Current Image: <?php echo htmlspecialchars($tire['image']); ?></small>
            </div>
            <a href="<?php echo isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : 'admin_edit_tires.php'; ?>" class="btn btn-secondary mr-2">Back</a>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </main>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
