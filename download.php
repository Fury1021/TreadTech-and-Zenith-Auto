<?php
session_name('system1_session');
session_start();

// Check if user is logged in as admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit();
}

include 'supplierdb_connect.php';

try {
    // Check if there is a search query
    $searchQuery = isset($_SESSION['search_query']) ? $_SESSION['search_query'] : '';

    if (!empty($searchQuery)) {
        // Fetch tires based on search query
        $stmt = $conn->prepare("
            SELECT id, item_name, type, price, description, brand, amount, image
            FROM tires
            WHERE item_name LIKE :searchQuery
            OR brand LIKE :searchQuery
            OR description LIKE :searchQuery
        ");
        $searchParam = "%{$searchQuery}%";
        $stmt->bindParam(':searchQuery', $searchParam, PDO::PARAM_STR);
    } else {
        // Fetch all tires if no search query
        $stmt = $conn->prepare("SELECT id, item_name, type, price, description, brand, amount, image FROM tires");
    }

    $stmt->execute();
    $tires = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($tires) > 0) {
        // Set CSV headers
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=tires.csv');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write column headers
        fputcsv($output, array('ID', 'Item Name', 'Type', 'Price', 'Description', 'Brand', 'Amount', 'Image'));

        // Write rows
        foreach ($tires as $tire) {
            fputcsv($output, $tire);
        }

        // Close output stream
        fclose($output);
        exit();
    } else {
        echo "No data found to download.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
