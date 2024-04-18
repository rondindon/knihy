<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Category Viewer with Pagination</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: rgb(220, 222, 250);
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
        border: 1px solid rgba(168, 173, 237,.7);
        background-color: rgb(240, 241, 250);
    }
    #sidebar h2{
        font-size: 2em;
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
        height: 20rem;
        border: 1px solid rgba(168, 173, 237,.7);
        border-radius: 5px;
        padding: 10px;
        background-color: rgb(240, 241, 250);
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
        background-color: rgba(168, 173, 237,.5);
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
        <h2>Kategórie</h2>
        <ul id="category-list">
            <li><a class="category-link" href="#" data-category="Učebnice pre stredné školy">Učebnice pre stredné školy</a></li>
            <li><a class="category-link" href="#" data-category="Učebnice pre autoškoly">Učebnice pre autoškoly</a></li>
            <li><a class="category-link" href="#" data-category="Učebnice pre vysoké školy">Učebnice pre vysoké školy</a></li>
            <li><a class="category-link" href="#" data-category="Knihy o programovaní">Knihy o programovaní</a></li>
            <li><a class="category-link" href="#" data-category="Knihy o tvorbe webu">Knihy o tvorbe webu</a></li>
            <li><a class="category-link" href="#" data-category="Knihy o databázach">Knihy o databázach</a></li>
        </ul>
    </div>
    <!-- Your HTML content here -->
    <div id="content">
        <h2 id="category-title">Vyberte si kategoriu</h2>
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

    function fetchItems(category, page) {
        console.log("Fetching items for category:", category, "Page:", page); // Log category and page number
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                var items = JSON.parse(xhr.responseText);
                displayItems(items, page);
            }
        };
        var url = "fetch_items.php?category=" + encodeURIComponent(category) + "&page=" + page;
        console.log("Fetch URL:", url); // Log the fetch URL
        xhr.open("GET", url, true);
        xhr.send();
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
        if (items.length > 0) {
            categoryTitle.innerText = categoryFromUrl; // Use the selected category from URL
        } else {
            categoryTitle.innerText = "No items found for " + categoryFromUrl;
        }
    }

    // Function to handle category link clicks
    function handleCategoryClick(event) {
        event.preventDefault();
        var category = this.getAttribute("data-category");
        fetchItems(category, currentPage);
        document.getElementById("category-title").innerText = category;

        // Update categoryFromUrl variable
        categoryFromUrl = category;

        // Construct clean URL with just the category name
        var baseUrl = window.location.origin + '/lol/'; // Get the base URL with "/lol/" segment
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
        fetchItems(document.getElementById("category-title").innerText, currentPage);
        if(currentPage === 0){
            currentPage = 7;
        }

    });

    document.getElementById("next-page").addEventListener("click", function() {
        currentPage++;
        fetchItems(document.getElementById("category-title").innerText, currentPage);
        if(currentPage >= 7){
            currentPage = 1;
        }
    });

});
    </script>
</body>
</html>