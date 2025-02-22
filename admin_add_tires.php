<?php
session_name('system1_session');
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Include database connection
include 'supplierdb_connect.php';

// Initialize variables for form submission
$item_name = $type = $price = $description = $brand = $amount = $image = '';
$error = '';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $item_name = htmlspecialchars($_POST['item_name']);
    $type = htmlspecialchars($_POST['type']);
    if ($type === 'others') {
        $type = htmlspecialchars($_POST['other_type']);
    }
    $price = htmlspecialchars($_POST['price']);
    $description = htmlspecialchars($_POST['description']);
    $brand = htmlspecialchars($_POST['brand']);
    $amount = htmlspecialchars($_POST['amount']);

    // Upload image file
    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_name = $_FILES['image']['name'];
        $image_temp = $_FILES['image']['tmp_name'];
        $image_path = '../supplier/storage/' . $image_name;
        $image_path = '../retailer/storage/' . $image_name;
        move_uploaded_file($image_temp, $image_path);
        $image = $image_path;
    } else {
        $error = 'Failed to upload image.';
    }

    // Insert data into database
    if (empty($error)) {
        try {
            $stmt = $conn->prepare("INSERT INTO tires (item_name, type, price, description, brand, amount, image) 
                                    VALUES (:item_name, :type, :price, :description, :brand, :amount, :image)");
            $stmt->bindParam(':item_name', $item_name);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':brand', $brand);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':image', $image);
            $stmt->execute();

            // Redirect to admin dashboard after successful insert
            header("Location: admin_dashboard.php");
            exit();
        } catch (PDOException $e) {
            $error = 'Error inserting data: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Tire</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
<div class="container mt-4">
    <h2>Add Tire</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="item_name">Item Name:</label>
            <input type="text" class="form-control" id="item_name" name="item_name" required>
        </div>
        <div class="form-group">
            <label for="type">Type:</label>
            <select class="form-control" id="type" name="type" required onchange="toggleOtherTypeInput(this)">
                <option value="">Select type</option>
                <option value="PCT">PCT</option>
                <option value="TSUVT">TSUVT</option>
                <option value="PT">PT</option>
                <option value="ST">ST</option>
                <option value="CHDT">CHDT</option>
                <option value="ORAT">ORAT</option>
                <option value="others">Others</option>
            </select>
        </div>
        <div class="form-group" id="otherTypeInput" style="display: none;">
            <label for="other_type">Other Type:</label>
            <input type="text" class="form-control" id="other_type" name="other_type">
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" class="form-control" id="price" name="price" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="brand">Brand:</label>
            <input type="text" class="form-control" id="brand" name="brand" required>
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" class="form-control" id="amount" name="amount" required>
        </div>
        <div class="form-group">
            <label for="image">Image:</label>
            <input type="file" class="form-control-file" id="image" name="image" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mt-3" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
function toggleOtherTypeInput(selectElement) {
    var otherTypeInput = document.getElementById('otherTypeInput');
    if (selectElement.value === 'others') {
        otherTypeInput.style.display = 'block';
    } else {
        otherTypeInput.style.display = 'none';
    }
}
</script>
</body>
</html>
