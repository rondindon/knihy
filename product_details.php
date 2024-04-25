<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/x-icon" href="../kniha.ico">
<title><?php echo isset($productDetails['nazov']) ? $productDetails['nazov'] : 'Product Details'; ?></title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: rgb(145, 149, 219);
    }
    #sidebar {
        margin-top: 50px;
        width: 250px;
        float: left;
        background-color: #f4f4f4;
        padding: 20px;
        list-style: none;
        border-radius: 15px;
        height: 100vh;
        border: 1px solid rgba(168, 173, 237);;
        background-color: rgb(218, 218, 224);
    }
    #sidebar h2{
        font-size: 2em;
        margin: .5rem 0 1.5rem 0;
    }
    #category-list{
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .category-link {
        display: block;
        margin-bottom: 15px;
        text-decoration: none;
        color: #333;
    }
    #content {
        width: 45%;
        margin-left: 330px; /* Adjusted margin to accommodate the sidebar */
        padding: 20px;
    }
    /* Style for product details */
    .product-details {
        margin-top: 2rem;
        border: 1px solid rgba(168, 173, 237);;
        border-radius: 5px;
        padding: 20px;
        background-color: #f9f9f9;
        display: flex;
        flex-direction: column;
        align-items: center;
        background-color: rgb(218, 218, 224);
    }
    /* Style for product image */
    .product-details img {
        max-width: 100%;
        height: 25rem;
        margin-bottom: 10px;
    }
    .category-link {
        display: block;
        margin-bottom: 15px;
        text-decoration: none;
        color: #333;
    }
    .category-link:hover {
        color: #000;
    }

    .product-title {
        font-size: 24px;
        color: #333;
    }

    .product-author, .product-price, .product-info {
        margin-top: 10px;
        color: #666;
    }

    .product-image {
        max-width: 100%;
        height: auto;
        margin-top: 10px;
    }

    .product-info a {
        color: blue;;
        text-decoration: none;
    }

    .product-info a:hover {
        text-decoration: underline;
    }

    .product-not-found {
        color: red;
        font-weight: bold;
    }

</style>
</head>
<body>
<!-- Main sidebar -->
<div id="sidebar">
<a href="/knihy/"><img src="../kkniha.png" width=75></a>
    <h2>Kategórie</h2>
    <ul id="category-list">
        <li><a class="category-link" href="#" data-category="ucebnice pre stredne skoly">Učebnice pre stredné školy</a></li>
        <li><a class="category-link" href="#" data-category="ucebnice pre autoskoly">Učebnice pre autoškoly</a></li>
        <li><a class="category-link" href="#" data-category="ucebnice pre vysoke skoly">Učebnice pre vysoké školy</a></li>
        <li><a class="category-link" href="#" data-category="knihy o programovani">Knihy o programovaní</a></li>
        <li><a class="category-link" href="#" data-category="knihy o tvorbe webu">Knihy o tvorbe webu</a></li>
        <li><a class="category-link" href="#" data-category="knihy o databazach">Knihy o databázach</a></li>
    </ul>
</div>
<!-- Content section -->
<div id="content">
<?php
// Check if product ID is provided in the URL
if(isset($_GET['id'])) {
    // Retrieve product details from the database based on the product ID
    $productId = $_GET['id'];

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

    // Fetch product details from the database
    function fetchProductDetails($productId, $conn) {
        $sql = "SELECT * FROM knihy WHERE id=?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Error in prepared statement: " . $conn->error);
        }
        $stmt->bind_param("i", $productId);
        if (!$stmt->execute()) {
            die("Error executing statement: " . $stmt->error);
        }
        $result = $stmt->get_result();
        if (!$result) {
            die("Error getting result set: " . $conn->error);
        }
        $productDetails = $result->fetch_assoc();
        return $productDetails;
    }

    // Function to remove diacritics from category names
    function removeDiacritics($string) {
        return iconv('UTF-8', 'ASCII//TRANSLIT', $string);
    }

    // Fetch product details
    $productDetails = fetchProductDetails($productId, $conn);

    // Close connection
    $conn->close();

    if($productDetails) {
        // Display product details
        echo "<div class='product-details'>";
        echo "<h2 class='product-title'>{$productDetails['nazov']}</h2>";
        echo "<p class='product-author'>Autor: {$productDetails['autor']}</p>";
        echo "<p class='product-price'>Cena: {$productDetails['cena']} €</p>";
        echo "<img class='product-image' src='{$productDetails['obrazok']}' alt='{$productDetails['nazov']}'>";
        echo "<p class='product-info'>Informácie o knihe: <a class='tu' href='{$productDetails['informacieoknihe']}'>Tu</a></p>";
        echo "</div>";
    } else {
        echo "<p class='product-not-found'>Product not found.</p>";
    }
    
} else {
    echo "<p>Product ID not provided.</p>";
}
?>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var categoryLinks = document.querySelectorAll(".category-link");

    // Function to handle category link clicks
    function handleCategoryClick(event) {
        event.preventDefault();
        var category = this.getAttribute("data-category");
        var baseUrl = window.location.origin + '/knihy/'; // Base URL with "/lol/" segment
        var cleanCategory = encodeURIComponent(category.replace(/\s/g, '-')); // Encode and replace spaces with dashes
        var cleanUrl = baseUrl + cleanCategory; // Construct URL with cleaned category
        window.location.href = cleanUrl; // Redirect to the URL
    }

    // Attach event listeners to category links
    categoryLinks.forEach(function(link) {
        link.addEventListener("click", handleCategoryClick);
    });

    var productTitle = document.querySelector(".product-title");
    if (productTitle) {
        document.title = productTitle.innerText;
    }
});
</script>
</body>
</html>