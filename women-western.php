<?php
include "db.php";

// Fetch products for category_id = 7 (Western Wear)
$query = "SELECT * FROM products WHERE category_id = :cat_id";
$stmt = $conn->prepare($query);
$stmt->execute(['cat_id' => 7]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>WESTERN WEAR</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="demo.css">
    <style>
        /* Make entire product card clickable */
        .product-card {
            cursor: pointer;
            position: relative;
        }
        /* Prevent buttons/icons inside card from being clickable to redirect */
        .product-card .buy-btn,
        .product-card .wishlist-icon,
        .product-card .sizes button {
            cursor: default;
        }
        /* Optional hover effect */
        .product-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            transition: 0.3s;
        }
    </style>
</head>
<body>

<header class="navbar">
    <div class="logo">
        <a href="index.php" class="logo-link">
            <img src="images/banner/adora-logo.jpeg" alt="Adora Logo" class="logo-img">
            <span class="logo-text">Adora</span>
        </a>
    </div>

    <nav class="menu">
        <a href="suit.php">Suits</a>
        <a href="winter.php">Winter Wear</a>
        <a href="saree.php">Sarees</a>
        <a href="gown.php">Gowns</a>
        <a href="women-western.php">Western Wear</a>
        <a href="top wear.php">Top Wear</a>
        <a href="lehenga.php">Lehenga</a>
    </nav>

    <div class="icons">
        <a href="search.php"><i class="fa-solid fa-magnifying-glass"></i></a>

        <div class="login-dropdown">
            <i class="fa-regular fa-user"></i>
            <div class="login-menu">
                <a href="login.html">Sign In</a>
                <a href="register.html">Create Account</a>
                <hr>
                <a href="orders.html">My Orders</a>
                <a href="wishlist.html">Wishlist</a>
                <a href="logout.html" class="logout">Logout</a>
            </div>
        </div>

        <i class="fa-solid fa-bag-shopping" onclick="toggleCart()"></i>
    </div>
</header>

<h1 class="page-title">WESTERN WEAR COLLECTION</h1>

<div class="product-container">

<?php foreach ($products as $row): ?>
    <div class="product-card" onclick="window.location.href='product-detail.php?id=<?php echo $row['id']; ?>'">

        <!-- Wishlist -->
        <i class="fa-regular fa-heart wishlist-icon"
           onclick="event.stopPropagation(); toggleWishlist(
             this,
             '<?php echo addslashes($row['name']); ?>',
             <?php echo $row['price']; ?>,
             '<?php echo $row['main_image']; ?>'
           )"></i>

        <!-- Product Image -->
        <img src="<?php echo $row['main_image']; ?>" class="product-img">

        <!-- Name -->
        <h3><?php echo htmlspecialchars($row['name']); ?></h3>

        <!-- Price -->
        <p class="price">₹<?php echo $row['price']; ?></p>

        <!-- Sizes -->
        <div class="sizes">
            <button onclick="event.stopPropagation();">S</button>
            <button onclick="event.stopPropagation();">M</button>
            <button onclick="event.stopPropagation();">L</button>
            <button onclick="event.stopPropagation();">XL</button>
        </div>

        <!-- Add to Cart -->
        <button class="buy-btn"
            onclick="event.stopPropagation(); addToCart(
              '<?php echo addslashes($row['name']); ?>',
              <?php echo $row['price']; ?>,
              '<?php echo $row['main_image']; ?>'
            )">
            Add to Cart
        </button>

    </div>
<?php endforeach; ?>

</div>
<footer class="footer">

    <div class="footer-container">

        <!-- BRAND -->
        <div class="footer-col brand">
            <h2>Adora</h2>
            <p>Where elegance meets modern fashion. Discover timeless styles crafted for every moment.</p>
        </div>

        <!-- SHOP -->
        <div class="footer-col">
            <h4>Shop</h4>
            <a href="suit.php">Suits</a>
            <a href="saree.php">Sarees</a>
            <a href="gown.php">Gowns</a>
            <a href="lehenga.php">Lehenga</a>
            <a href="winter.php">Winter Wear</a>
        </div>

        <!-- CUSTOMER CARE -->
        <div class="footer-col">
            <h4>Customer Care</h4>
            <a href="contactus.html">Contact Us</a>
            <a href="#">Shipping & Returns</a>
            <a href="faq.html">FAQs</a>
            <a href="#">Size Guide</a>
            <a href="#">Track Order</a>
        </div>

        <!-- CONNECT -->
        <div class="footer-col">
            <h4>Connect</h4>
            <div class="socials">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fa-solid fa-envelope"></i></a>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© 2026 Adora. All rights reserved.</p>
    </div>

</footer>
<script src="script.js"></script>
</body>
</html>
