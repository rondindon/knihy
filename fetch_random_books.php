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

// Fetch random books from the database
function fetchRandomBooks($conn, $limit = 8) {
    $sql = "SELECT * FROM knihy ORDER BY RAND() LIMIT ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error in prepared statement: " . $conn->error);
    }
    $stmt->bind_param("i", $limit);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    $result = $stmt->get_result();
    if (!$result) {
        die("Error getting result set: " . $conn->error);
    }
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    return $books;
}

// Fetch random books with a default limit of 8
$randomBooks = fetchRandomBooks($conn);

// Close connection
$conn->close();

// Return the random books as JSON
header('Content-Type: application/json');
echo json_encode($randomBooks);
?>
