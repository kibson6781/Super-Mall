<!-- index.html -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperMall Web App</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header section -->
    <header>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Product listing section -->
    <section id="product-listing">
        <h2>Product Listing</h2>
        <ul id="product-list">
            <!-- products will be listed here -->
        </ul>
    </section>

    <!-- Product update section -->
    <section id="product-update">
        <h2>Update Product Information</h2>
        <form id="update-product-form">
            <label for="product-name">Product Name:</label>
            <input type="text" id="product-name" name="product-name"><br><br>
            <label for="product-description">Product Description:</label>
            <textarea id="product-description" name="product-description"></textarea><br><br>
            <label for="product-price">Product Price:</label>
            <input type="number" id="product-price" name="product-price"><br><br>
            <input type="submit" value="Update Product">
        </form>
    </section>

    <!-- JavaScript files -->
    <script src="script.js"></script>
</body>
</html>
