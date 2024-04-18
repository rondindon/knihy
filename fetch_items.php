<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "knihy";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch items from the database for a specific category and limit to 24 items
function fetchItems($category, $conn) {
    $sql = "SELECT * FROM knihy WHERE kategoria=? LIMIT 60";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error in prepared statement: " . $conn->error);
    }
    $stmt->bind_param("s", $category);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    $result = $stmt->get_result();
    if (!$result) {
        die("Error getting result set: " . $conn->error);
    }
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    return $items;
}

// Handle AJAX request
if (isset($_GET['category'])) {
    $category = $_GET['category'];
    $items = fetchItems($category, $conn);
    // Encode items as JSON and return
    echo json_encode($items);
}

// Close connection
$conn->close();
?>
