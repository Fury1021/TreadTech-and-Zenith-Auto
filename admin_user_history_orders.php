<?php

session_name('system1_session');
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include 'supplierdb_connect.php';

// Initialize variable to hold search query
$searchQuery = "";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search'])) {
    // Sanitize user input to prevent SQL injection
    $searchQuery = htmlspecialchars($_POST['search_query']);
    
    try {
        // Fetch orders based on search query
        $stmt = $conn->prepare("
            SELECT id, user_email, total_price, created_at
            FROM orders
            WHERE user_email LIKE :searchQuery
               OR total_price LIKE :searchQuery
               OR created_at LIKE :searchQuery
            ORDER BY created_at DESC
        ");
        $searchParam = "%{$searchQuery}%";
        $stmt->bindParam(':searchQuery', $searchParam, PDO::PARAM_STR);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    // Fetch all orders if no search query or on initial load
    try {
        $stmt = $conn->query("
            SELECT id, user_email, total_price, created_at
            FROM orders
            ORDER BY created_at DESC
        ");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - User History Orders</title>
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
        
            <!-- Search form -->
            <form class="form-inline my-2 my-lg-0 mr-2" method="POST" action="">
                <input class="form-control mr-sm-2" type="search" placeholder="Search for anything" aria-label="Search" name="search_query" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit" name="search">Search</button>
            </form>
        </div>
    </nav>

<!-- Main Content -->
<div class="container mt-4">
    <h2>User History Orders</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User Email</th>
                    <th>Total Price</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                    <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                    <td>$<?php echo htmlspecialchars($order['total_price']); ?></td>
                    <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
