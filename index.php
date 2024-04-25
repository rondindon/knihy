<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/x-icon" href="kniha.ico">
<title>Category Viewer with Pagination</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: rgb(145, 149, 219);
    }
    #sidebar {
        margin-top: 50px;
        max-width: 18rem;
        width: 20%;
        float: left;
        background-color: #f4f4f4;
        padding: 20px;
        list-style: none;
        border-radius: 15px;
        height: 100vh;
        border: 1px solid rgba(168, 173, 237);
        background-color: rgb(218, 218, 224);
    }
    #sidebar h2{
        font-size: 2em;
        margin: .5rem 0 1.5rem 0;
    }
    #category-list{
        list-style: none;
        padding: 0;
    }
    #content {
        margin-left: 220px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
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
    .items{
        margin-top: 10px;
        justify-content: center;
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
    }
    .item {
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: space-around;
        align-items: center;
        gap: 1rem;
        width: 20%;
        min-width: 15rem;
        height: 20rem;
        border: 1px solid rgba(168, 173, 237);
        border-radius: 5px;
        padding: 10px;
        background-color: rgb(218, 218, 224);
        text-decoration: none;
    }

    /* Style for item title */
    .item h3 {
        margin: 0;
        color: #333;
    }

    /* Style for item author */
    .item p {
        color: #666;
        margin: 0;
    }

    /* Style for item price */
    .item .price {
        font-weight: bold;
        color: #009688;
    }

    /* Style for item image */
    .item img {
        width: 5rem;
        min-height: 5rem;
        max-width: 100%;
        height: auto;
        margin-bottom: 10px;
    }
    #pagination-controls {
        margin-top: 30px;
    }

    #prev-page,
    #next-page {
        background-color: rgba(168, 173, 237,.8) ;
        color: #232323;
        border: none;
        border-radius: 5px;
        padding: 10px 20px;
        cursor: pointer;
        margin-right: 10px;
        transition: .3s;
    }

    #prev-page:hover,
    #next-page:hover {
        background-color: rgba(112, 117, 207);
    }

    #prev-page:disabled,
    #next-page:disabled {
        background-color: #cccccc;
        color: #666666;
        cursor: not-allowed;
    }
</style>
</head>
<body>
<div id="sidebar">
    <a href="/knihy/"><img src="./kkniha.png" width=75></a>
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
    <!-- Your HTML content here -->
    <div id="content">
        <h2 id="category-title">Nahodny vyber</h2>
        <div id="items" class="items"></div>
        <div id="pagination-controls">
            <button id="prev-page">Predosla</button>
            <button id="next-page">Dalsia</button>
        </div>
    </div>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    var itemsPerPage = 8; // Number of items per page
    var currentPage = 1; // Current page number
    var categoryLinks = document.querySelectorAll(".category-link");
    var categoryFromUrl; // Variable to store category from URL
    var fetchedItems; // Variable to store fetched items

// Function to fetch items with the specified category and page number
// Function to fetch items with the specified category and page number
// Function to fetch items with the specified category and page number
function fetchItems(category, page) {
    console.log("Fetching items for category:", category, "Page:", page); // Log category and page number
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                fetchedItems = JSON.parse(xhr.responseText);
                displayItems(fetchedItems, page);
            } else {
                console.error("Failed to fetch items:", xhr.status);
            }
        }
    };
    var url;
    if (page === 1 && !category) {
        // If it's the main page and the page number is 1, don't include the page number in the URL
        url = "fetch_items.php";
    } else {
        // Include the page number in the URL for other cases
        url = "fetch_items.php";
        var params = [];
        if (category) {
            params.push("category=" + encodeURIComponent(category));
        }
        if (page && (!category || page !== 1)) {
            params.push("page=" + encodeURIComponent(page));
        }
        url += "?" + params.join("&");
    }
    console.log("Fetch URL:", url); // Log the fetch URL
    xhr.open("GET", url, true);
    xhr.send();

    // Update URL with the current page number if necessary
    if (page !== 1 || category) {
        var baseUrl = window.location.origin + '/knihy/';
        if (category) {
            baseUrl += encodeURIComponent(category.toLowerCase().replace(/\s/g, '-'));
        }
        if (page && (!category || page !== 1)) {
            baseUrl += '?page=' + encodeURIComponent(page);
        }
        window.history.pushState({}, '', baseUrl);
    }
}

    // Function to display items
    function displayItems(items, page) {
        var startIndex = (page - 1) * itemsPerPage;
        var endIndex = startIndex + itemsPerPage;
        var itemsHtml = "";
        for (var i = startIndex; i < endIndex && i < items.length; i++) {
            var item = items[i];
            itemsHtml += "<a href='produkt/" + item.id + "' class='item-link item'>";
            itemsHtml += "<h3>" + item.nazov + "</h3>";
            itemsHtml += "<p>Autor: " + item.autor + "</p>";
            itemsHtml += "<p>Cena: " + item.cena + " €</p>";
            itemsHtml += "<img src='" + item.obrazok + "' alt='" + item.nazov + "'>";
            itemsHtml += "</a>";
        }
        document.getElementById("items").innerHTML = itemsHtml;

        // Update category title
        var categoryTitle = document.getElementById("category-title");
        if (categoryFromUrl) {
            var capitalizedCategory = categoryFromUrl.charAt(0).toUpperCase() + categoryFromUrl.slice(1).toLowerCase();
            categoryTitle.innerText = capitalizedCategory; // Use the selected category from URL
            document.title = capitalizedCategory;
        } else {
            categoryTitle.innerText = "Náhodný výber"; // Default category title
        }
        
        // Hide pagination controls when displaying random books
        var paginationControls = document.getElementById("pagination-controls");
        if (!categoryFromUrl) {
            paginationControls.style.display = "none";
        } else {
            paginationControls.style.display = "block";
        }
    }

    // Function to handle category link clicks
    function handleCategoryClick(event) {
        event.preventDefault();
        var category = this.getAttribute("data-category");
        var capitalizedCategory = category.charAt(0).toUpperCase() + category.slice(1).toLowerCase(); // Capitalize the first letter
        fetchItems(category, currentPage);
        document.getElementById("category-title").innerText = capitalizedCategory;

        // Update categoryFromUrl variable
        categoryFromUrl = capitalizedCategory;
        document.title = capitalizedCategory;

        // Construct clean URL with just the category name
        var baseUrl = window.location.origin + '/knihy/'; // Get the base URL with "/knihy/" segment
        var cleanCategory = encodeURIComponent(category.replace(/\s/g, '-')); // Encode and replace spaces with dashes
        var cleanUrl = baseUrl + cleanCategory; // Combine base URL and cleaned category
        window.history.pushState({}, '', cleanUrl);
    }

    // Attach event listeners to category links
    categoryLinks.forEach(function(link) {
        link.addEventListener("click", handleCategoryClick);
    });

    // Extract category from URL and fetch items
    var currentUrl = window.location.href;
    categoryFromUrl = decodeURIComponent(currentUrl.split('/').pop());
    categoryFromUrl = categoryFromUrl.replace(/-/g, ' ');
    fetchItems(categoryFromUrl, currentPage);

    document.getElementById("prev-page").addEventListener("click", function() {
        currentPage--;
        if (currentPage === 0) {
            currentPage = 7;
        }
        fetchItems(document.getElementById("category-title").innerText, currentPage);
    });

    document.getElementById("next-page").addEventListener("click", function() {
        currentPage++;
        if (currentPage >= 7) {
            currentPage = 1;
        }
        fetchItems(document.getElementById("category-title").innerText, currentPage);
    });

    // Fetch random books if on the main page
    if (window.location.pathname === '/knihy/') {
        fetchRandomBooks();
        console.log("Fetching random books");
    }

    function fetchRandomBooks() {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    fetchedItems = JSON.parse(xhr.responseText);
                    displayItems(fetchedItems, currentPage);
                } else {
                    console.error("Failed to fetch random books:", xhr.status);
                }
            }
        };
        var url = "fetch_random_books.php";
        xhr.open("GET", url, true);
        xhr.send();
    }
});

    </script>
</body>
</html>