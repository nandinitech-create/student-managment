<?php
session_start();
include "db.php";

// ====================== NAVBAR ======================
// IDs in the exact order you want in navbar
$navOrder = [3,9,2,4,7,16,5]; // Replace these with your actual category IDs
$placeholders = implode(',', $navOrder);

// Fetch navbar categories
$stmtNav = $conn->prepare("SELECT * FROM categories WHERE id IN ($placeholders) AND status=1 ORDER BY FIELD(id, $placeholders)");
$stmtNav->execute();
$navCategories = $stmtNav->fetchAll(PDO::FETCH_ASSOC);

// ====================== HOMEPAGE SECTIONS ======================
// Exclusive Collections
$stmtExclusive = $conn->prepare("SELECT * FROM categories WHERE type='exclusive' AND status=1 ORDER BY id ASC");
$stmtExclusive->execute();
$exclusiveCategories = $stmtExclusive->fetchAll(PDO::FETCH_ASSOC);

// Trending Collections
$stmtTrending = $conn->prepare("SELECT * FROM categories WHERE type='trending' AND status=1 ORDER BY id ASC");
$stmtTrending->execute();
$trendingCategories = $stmtTrending->fetchAll(PDO::FETCH_ASSOC);

// Normal Categories (for categories grid)
$stmtNormal = $conn->prepare("SELECT * FROM categories WHERE type='normal' AND status=1 ORDER BY id ASC");
$stmtNormal->execute();
$normalCategories = $stmtNormal->fetchAll(PDO::FETCH_ASSOC);

// All categories for footer
$stmtAll = $conn->prepare("SELECT * FROM categories WHERE status=1 ORDER BY id ASC");
$stmtAll->execute();
$allCategories = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

// ================= FUNCTION TO FIX IMAGE PATH =================
function fixImagePath($image){
    return str_replace(' ', '%20', $image);
}

// ================= SOCIAL LINKS =================
$stmt = $conn->prepare("SELECT * FROM social_links");
$stmt->execute();
$socialLinks = $stmt->fetchAll(PDO::FETCH_ASSOC);

$iconMap = [
    'instagram' => 'fa-brands fa-instagram',
    'facebook'  => 'fa-brands fa-facebook-f',
    'email'     => 'fa-solid fa-envelope',
];

// ================= FETCH ACTIVE BANNERS =================
$stmtBanners = $conn->prepare("
    SELECT * 
    FROM banners 
    WHERE status = 1 AND section = 'hero'
    ORDER BY sort_order ASC
");
$stmtBanners->execute();
$banners = $stmtBanners->fetchAll(PDO::FETCH_ASSOC);


// ================= FETCH ABOUT BANNER =================
$stmtAbout = $conn->prepare("
    SELECT * 
    FROM banners 
    WHERE status = 1 AND section = 'about' 
    ORDER BY sort_order ASC, id DESC 
    LIMIT 1
");
$stmtAbout->execute();
$aboutBanner = $stmtAbout->fetch(PDO::FETCH_ASSOC);

// fallback if no banner found
if (!$aboutBanner) {
    $aboutBanner = [
        'title' => 'About Adora',
        'image' => 'default-banner.jpeg',
        'link'  => 'aboutadora.php'
    ];
}

// Prepare about banner variables
$bannerImage = isset($aboutBanner['image']) ? fixImagePath($aboutBanner['image']) : 'default-banner.jpeg';
$bannerLink  = isset($aboutBanner['link']) ? $aboutBanner['link'] : '#';
$bannerTitle = isset($aboutBanner['title']) ? $aboutBanner['title'] : 'About Adora';

// Check if file exists; fallback to default
if(!file_exists("images/banner/".$bannerImage)){
    $bannerImage = 'default-banner.jpeg';
}

// ================= FETCH REVIEWS =================
$reviewStmt = $conn->prepare("SELECT * FROM fashion_reviews ORDER BY created_at DESC LIMIT 6");
$reviewStmt->execute();
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<html>
<head>
    <title>Adora</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&family=Poppins:wght@300;400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ================= NAVBAR ================= -->
<header class="navbar">
    <div class="logo">
        <a href="index.php" class="logo-link">
            <img src="images/banner/adora-logo.jpeg" alt="Adora Logo" class="logo-img">
            <span class="logo-text">Adora</span>
        </a>
    </div>

    <nav class="menu">
        <?php foreach($navCategories as $cat): ?>
            <a href="category.php?id=<?php echo $cat['id']; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="icons">
        <a href="search.php" class="search-icon"><i class="fa-solid fa-magnifying-glass" onclick="openSearch()"></i></a>

        <div class="login-dropdown">
            <i class="fa-regular fa-user"></i>
            <div class="login-menu">
                <a href="login.php">Sign In</a>
                <a href="signin.php">Create Account</a>
                <hr>
                <a href="profile.php">Profile</a>
                <a href="wishlist.php">Wishlist</a>
                <a href="logout.php" class="logout">Logout</a>
            </div>
        </div>

        <i class="fa-solid fa-bag-shopping" onclick="toggleCart()"></i>
    </div>
</header>

<!-- ================= HERO SECTION ================= -->
<section class="hero-slider">
    <div class="hero-track">
        <?php foreach($banners as $b): ?>
        <div class="hero-slide">
            <a href="<?= htmlspecialchars($b['link'] ?? '#') ?>">
                <img src="images/<?= fixImagePath($b['image']) ?>" alt="<?= htmlspecialchars($b['title']) ?>">
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <button class="hero-btn left"><i class="fa-solid fa-chevron-left"></i></button>
    <button class="hero-btn right"><i class="fa-solid fa-chevron-right"></i></button>
</section>
<section class="luxury-trust">
    <div>✨ Certified Craftsmanship</div>
    <div>🚚 Free Shipping</div>
    <div>🔐 Secure Payments</div>
    <div>💎 Premium Quality</div>
</section>


<!-- ================= EXCLUSIVE COLLECTIONS ================= -->
<section class="capsule-collection">
    <h2 class="section-title">EXCLUSIVE COLLECTIONS</h2>

    <div class="capsule-grid">

        <?php foreach($exclusiveCategories as $cat): ?>
            <a href="category.php?id=<?= $cat['id'] ?>" class="capsule-card">

                <div class="capsule-frame">
                    <img src="images/<?= fixImagePath($cat['image']); ?>" alt="<?= htmlspecialchars($cat['name']); ?>">
                    <span class="capsule-sparkle">✦</span>
                </div>

                <div class="capsule-label">
                    <?= htmlspecialchars($cat['name']); ?>
                </div>

            </a>
        <?php endforeach; ?>

    </div>
</section>

<!-- ================= TRENDING COLLECTIONS ================= -->
<section class="trending-section">

    <h2 class="trending-title">Trending Now</h2>
    <p class="trending-subtitle">Timeless pieces, loved by every Adora woman.</p>

    <button class="trend-nav left" onclick="slideTrend(-1)">❮</button>
    <button class="trend-nav right" onclick="slideTrend(1)">❯</button>

    <div class="trend-slider">
        <div class="trend-track" id="trendTrack">

            <?php foreach($trendingCategories as $cat): ?>
                <a href="category.php?id=<?= $cat['id'] ?>" class="trend-card">
                    <img src="images/<?= fixImagePath($cat['image']); ?>" alt="<?= htmlspecialchars($cat['name']); ?>">
                    <div class="trend-label"><?= htmlspecialchars($cat['name']); ?></div>
                </a>
            <?php endforeach; ?>

        </div>
    </div>

</section>
<!-- Shop by categories -->

<section class="luxury-cat-circle">
    <div class="luxury-cat-head">
        <h2>SHOP BY CATEGORY</h2>
        <p>Explore our curated collections</p>
    </div>

    <div class="luxury-circle-grid">
        <?php foreach($normalCategories as $cat): ?>
            <a href="category.php?id=<?= $cat['id']; ?>" class="luxury-circle-card">
                <div class="circle-img">
                    <img src="images/<?= fixImagePath($cat['image']); ?>" alt="<?= htmlspecialchars($cat['name']); ?>">
                </div>
                <span><?= htmlspecialchars($cat['name']); ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</section>


<!-- SHOP BY BUDGET -->
<!-- SHOP BY BUDGET -->
<section class="budget-ref">
    <div class="budget-header">
        <h2>SHOP BY BUDGET</h2>
        <p>Explore styles tailored to your perfect price range</p>
    </div>

    <div class="budget-ref-grid">
        <a href="budget-5000.php?max=5000" class="budget-card">
           <div class="budget-img">
                <img src="images/budget/budget1.jpeg" alt="Under 5000">
           </div>
           <div class="budget-content">
                <h3>Under ₹5000</h3>
                <p>Elegant styles for everyday fashion</p>
                <span>Explore</span>
           </div>
        </a>
    
        <a href="budget-10000.php?max=10000" class="budget-card">
           <div class="budget-img">
                <img src="images/budget/budget2.jpeg" alt="Under 10000">
           </div>
           <div class="budget-content">
                <h3>Under ₹10000</h3>
                <p>Designed to make everyday feel special</p>
                <span>Explore</span>
           </div>
        </a>
    
       <a href="budget-50000.php?max=50000" class="budget-card">
           <div class="budget-img">
                <img src="images/budget/budget3.jpeg" alt="Under 50000">
           </div>
           <div class="budget-content">
                <h3>Under ₹50000</h3>
                <p>Experience fashion at its finest</p>
                <span>Explore</span>
           </div>
        </a>
    </div>
</section>
<!-- ================= NEW ARRIVALS ================= -->
<div class="new-arrivals-header">
    <h2>NEW ARRIVALS</h2>
    <p>Discover our latest styles curated just for you</p>
</div>

<div class="new-arrivals-grid">

    <a href="new_arrivals.php?slug=gown" class="arrival-card">
        <img src="images/gowns/gownn.jpeg">
        <div class="arrival-overlay"><span>Ceremony Chic</span></div>
    </a>

    <a href="new_arrivals.php?slug=saree" class="arrival-card">
        <img src="images/saree/gold saree.jpeg">
        <div class="arrival-overlay"><span>Golden Grace</span></div>
    </a>

    <a href="new_arrivals.php?slug=party-wear" class="arrival-card">
        <img src="images/party wear/NEW ARRIVAL.jpeg">
        <div class="arrival-overlay"><span>Party Glow</span></div>
    </a>

    <a href="new_arrivals.php?slug=lehenga" class="arrival-card">
        <img src="images/Lehenga/new arrival.jpeg">
        <div class="arrival-overlay"><span>Dulhan Diaries</span></div>
    </a>

    <a href="new_arrivals.php?slug=anarkali" class="arrival-card">
        <img src="images/anarkali/new arrival.jpeg">
        <div class="arrival-overlay"><span>Flowing Grace</span></div>
    </a>

</div>
<!-- 🔥 Customer REVIEWS -->
<section class="fashion-reviews">
    <h2 class="fashion-reviews-title">What Our Customers Say</h2>
    <p class="fashion-reviews-sub">Real reviews from our style community</p>

    <div class="fashion-reviews-grid">

        <?php foreach($reviews as $r): ?>
        <div class="fashion-review-card">
            <p>“<?= htmlspecialchars($r['review']); ?>”</p>
            <div class="fashion-review-user">
                <span class="fashion-stars">
                    <?php
                        for($i=1; $i<=5; $i++){
                            echo $i <= $r['stars'] ? '★' : '☆';
                        }
                    ?>
                </span>
                <h4><?= htmlspecialchars($r['name']); ?></h4>
                <small><?= htmlspecialchars($r['city']); ?></small>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</section>


<section class="about-full-banner">
    <a href="<?= htmlspecialchars($bannerLink) ?>">
        <div class="about-banner-img" 
             style="background-image: url('images/banner/<?= htmlspecialchars($Image) ?>'); 
                    height: 400px; 
                    background-size: cover; 
                    background-position: center; 
                    width: 100%;
                    position: relative;">
            <div class="about-banner-text" 
                 style="position: absolute; bottom: 40px; left: 40px; color: #fff; z-index: 2;">
                
            </div>
        </div>
    </a>
</section>
<!-- ================= FOOTER ================= -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-col brand">
            <h2>Adora</h2>
            <p>Where elegance meets modern fashion. Discover timeless styles crafted for every moment.</p>
        </div>

        <div class="footer-col">
            <h4>Shop</h4>
            <?php foreach($normalCategories as $cat): ?>
                <a href="category.php?id=<?php echo $cat['id']; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="footer-col">
            <h4>Customer Care</h4>
            <a href="contactus.php">Contact Us</a>
            <a href="shipping-returns.php">Shipping & Returns</a>
            <a href="faqs.php">FAQs</a>
            <a href="size_guide.php">Size Guide</a>
            <a href="track-order.php">Track Order</a>
        </div>

        <div class="footer-col connect">
            <h4>CONNECT</h4>
            <div class="socials">
                <?php foreach ($socialLinks as $link): 
                    $platformRaw = $link['platform'];
                    $platform = strtolower(trim($platformRaw));
                    $iconClass = $iconMap[$platform] ?? 'fa-solid fa-link';
                ?>
                    <a href="<?= htmlspecialchars($link['url']) ?>" target="_blank">
                        <i class="<?= $iconClass ?>"></i>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- 👇 Bottom line outside grid -->
    <div class="footer-bottom">
        <p>© 2026 Adora. All rights reserved.</p>
    </div>
</footer>

<!-- ================= PROFESSIONAL CHATBOT ================= -->

<div class="chatbot-wrapper">

    <!-- Floating Button -->
    <div class="chatbot-toggle" onclick="toggleChat()">
        <i class="fa-solid fa-comments"></i>
    </div>

    <!-- Chat Window -->
    <div class="chatbot-window" id="chatWindow">
        <div class="chat-header">
            <div>
                <strong>Adora Support</strong><br>
                <small>We reply instantly ⚡</small>
            </div>
            <span onclick="toggleChat()">✖</span>
        </div>

        <div class="chat-body" id="chatBody"></div>

        <div class="chat-footer">
            <input type="text" id="chatInput"
                   placeholder="Type your message..."
                   onkeypress="if(event.key==='Enter'){sendMessage()}">
            <button onclick="sendMessage()">➤</button>
        </div>
    </div>

</div>

<script src="script.js"></script>
<!-- ================= hero section ================= -->
<script>
const track = document.querySelector('.hero-track');
const slides = document.querySelectorAll('.hero-slide');
const leftBtn = document.querySelector('.hero-btn.left');
const rightBtn = document.querySelector('.hero-btn.right');

let index = 0;
const total = slides.length;

// Automatic slide
function autoSlide() {
    index++;
    if(index >= total) index = 0;
    updateSlide();
}
let slideInterval = setInterval(autoSlide, 10000); // change every 10s

function updateSlide() {
    track.style.transform = `translateX(-${index * 100}%)`;
}

// Arrow buttons
leftBtn.addEventListener('click', () => {
    index--;
    if(index < 0) index = total - 1;
    updateSlide();
    resetInterval();
});

rightBtn.addEventListener('click', () => {
    index++;
    if(index >= total) index = 0;
    updateSlide();
    resetInterval();
});

function resetInterval() {
    clearInterval(slideInterval);
    slideInterval = setInterval(autoSlide, 10000);
}

// Optional: drag/swipe support
let isDown = false, startX, scrollX;

track.addEventListener('mousedown', e => {
    isDown = true;
    startX = e.pageX;
});

track.addEventListener('mousemove', e => {
    if(!isDown) return;
    let walk = e.pageX - startX;
    track.style.transform = `translateX(${ -index*100 + walk/slides[0].offsetWidth*100 }%)`;
});

track.addEventListener('mouseup', e => {
    isDown = false;
    let walk = e.pageX - startX;
    if(walk < -50) index++;
    if(walk > 50) index--;
    if(index < 0) index = total - 1;
    if(index >= total) index = 0;
    updateSlide();
    resetInterval();
});

track.addEventListener('mouseleave', () => { isDown = false; });

</script>
<!-- ===== trending collection ===== -->
<script>
let trendIndex = 0;

function slideTrend(dir){
    const track = document.getElementById("trendTrack");
    const cards = Array.from(track.children);

    if(cards.length === 0) return;

    const cardWidth = cards[0].offsetWidth + 10; // gap adjust

    trendIndex += dir;

    // boundary
    if(trendIndex < 0) trendIndex = 0;
    if(trendIndex > cards.length - 3) trendIndex = cards.length - 3; // 3 visible cards

    track.style.transition = "transform 0.5s ease";
    track.style.transform = `translateX(-${trendIndex * cardWidth}px)`;
}
</script>
<!-- ===== CHATBOT JS ===== -->
<script>
function toggleChat(){
    const chatWindow = document.getElementById("chatWindow");
    chatWindow.style.display =
        chatWindow.style.display === "flex" ? "none" : "flex";
}

function sendMessage() {

    let inputField = document.getElementById("chatInput");
    let message = inputField.value.trim();

    if(message === "") return;

    let chatBox = document.getElementById("chatBody");

    // Show user message
    chatBox.innerHTML += "<div class='user-msg'>" + message + "</div>";
    chatBox.scrollTop = chatBox.scrollHeight;

    inputField.value = "";

    // Send to PHP
    fetch("chatbot.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "message=" + encodeURIComponent(message)
    })
    .then(response => response.text())
    .then(data => {
        chatBox.innerHTML += "<div class='bot-msg'>" + data + "</div>";
        chatBox.scrollTop = chatBox.scrollHeight;
    })
    .catch(error => {
        chatBox.innerHTML += "<div class='bot-msg'>Server error. Try again.</div>";
    });
}
</script>


</body>
</html>