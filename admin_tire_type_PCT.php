<?php

session_name('system1_session');
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
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
        // Fetch CHDT tires data based on search query
        $stmt = $conn->prepare("
            SELECT *
            FROM tires
            WHERE type = 'PCT'
            AND (item_name LIKE :searchQuery
                 OR brand LIKE :searchQuery
                 OR description LIKE :searchQuery
                 OR price LIKE :searchQuery)
        ");
        $searchParam = "%{$searchQuery}%";
        $stmt->bindParam(':searchQuery', $searchParam, PDO::PARAM_STR);
        $stmt->execute();
        $chdt_tires = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
} else {
    // Fetch all PCT tires if no search query or on initial load
    try {
        $stmt = $conn->prepare("SELECT * FROM tires WHERE type = 'PCT'");
        $stmt->execute();
        $chdt_tires = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>PCT Tires</title>
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
    <h2>PCT Tires</h2>
    
    <div class="row">
        <?php foreach ($chdt_tires as $tire): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card h-100">
                <img class="card-img-top" src="<?php echo htmlspecialchars($tire['image']); ?>" alt="<?php echo htmlspecialchars($tire['item_name']); ?>">
                <div class="card-body">
                    <h4 class="card-title"><?php echo htmlspecialchars($tire['item_name']); ?></h4>
                    <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($tire['brand']); ?></h6>
                    <p class="card-text"><?php echo htmlspecialchars($tire['description']); ?></p>
                    <h5 class="card-text">Price: $<?php echo htmlspecialchars($tire['price']); ?></h5>
                </div>
                <div class="card-footer">
                    <a href="admin_edit_tires.php?id=<?php echo $tire['id']; ?>" class="btn btn-primary">Edit</a>
                    <a href="admin_all_tires.php?id=<?php echo $tire['id']; ?>" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'footer.php'; ?>

<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
