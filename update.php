<?php

$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "knihy"; // Replace with your MySQL database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Start transaction
$conn->begin_transaction();

// Drop existing table if it exists
$sql = "DROP TABLE IF EXISTS knihy_update";
$conn->query($sql);

// Create new table
$sql = "CREATE TABLE knihy_update (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nazov VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL,
    kategoria VARCHAR(255) NOT NULL,
    cena DECIMAL(10, 2) NOT NULL,
    informacieoknihe TEXT,
    obrazok VARCHAR(255)
)";

if ($conn->query($sql) === FALSE) {
    echo "Error creating table: " . $conn->error;
    $conn->rollback(); // Rollback transaction on error
    exit;
}

// URLs for each category
$urls = array(
    "Učebnice pre stredné školy" => "http://export.martinus.sk/?a=XmlPartner&cat=6758&q=&z=B7GET5&key=NYtvbkOHAzPzGJNz7qR9Kk",
    "Učebnice pre autoškoly" => "http://export.martinus.sk/?a=XmlPartner&cat=6768&q=&z=B7GET5&key=NYtvbkOHAzPzGJNz7qR9Kk",
    "Učebnice pre vysoké školy" => "http://export.martinus.sk/?a=XmlPartner&cat=6764&q=&z=B7GET5&key=NYtvbkOHAzPzGJNz7qR9Kk",
    "Knihy o programovaní" => "http://export.martinus.sk/?a=XmlPartner&cat=6408&q=&z=B7GET5&key=NYtvbkOHAzPzGJNz7qR9Kk",
    "Knihy o tvorbe webu" => "http://export.martinus.sk/?a=XmlPartner&cat=6406&q=&z=B7GET5&key=NYtvbkOHAzPzGJNz7qR9Kk",
    "Knihy o databázach" => "http://export.martinus.sk/?a=XmlPartner&cat=6414&q=&z=B7GET5&key=NYtvbkOHAzPzGJNz7qR9Kk"
);

// Function to fetch and parse XML data from URL
function fetchDataFromURL($url) {
    $xml = simplexml_load_file($url);
    if ($xml === false) {
        return null; // Unable to parse XML
    }
    return $xml;
}

// Function to insert items into the database
function insertItemsIntoDatabase($items,$category, $conn) {
    foreach ($items as $item) {
        $title = $conn->real_escape_string($item->title);
        $author = $conn->real_escape_string($item->author);
        $link = $conn->real_escape_string($item->link);
        $img = $conn->real_escape_string($item->image);
        $price = (float)$item->price;
        // Insert data into the database
        $sql = "INSERT INTO knihy_update (nazov, autor, kategoria, cena, informacieoknihe, obrazok)
                VALUES ('$title', '$author', '$category', $price, '$link', '$img')";
        if ($conn->query($sql) === false) {
            echo "Error inserting data: " . $conn->error;
            $conn->rollback(); // Rollback transaction on error
            exit;
        }
    }
}

// Process update request
foreach ($urls as $category => $url) {
    $xmlData = fetchDataFromURL($url);
    if ($xmlData !== null && isset($xmlData->channel->item)) {
        $items = $xmlData->channel->item;
        insertItemsIntoDatabase($items,$category, $conn);
        echo "Pridane polozky: $category<br>";
    } else {
        echo "Error fetching data for category: $category<br>";
    }
}

$sql = "DROP TABLE knihy";
$conn->query($sql);

// Rename new table to match original table name
$sql = "ALTER TABLE knihy_update RENAME TO knihy";
$conn->query($sql);
echo "Tabulka knihy bola aktualizovana<br>";

// Commit transaction
$conn->commit();

// Close connection
$conn->close();
?>