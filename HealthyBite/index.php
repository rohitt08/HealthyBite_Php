<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include_once 'includes/db.php';
include 'includes/header.php';

$menu_items = array();
if ($conn) {
    $sql = "SELECT * FROM menu_items LIMIT 3";
    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $parsed_tags = json_decode($row['tags'], true);
            if ($parsed_tags == null) {
                $parsed_tags = array();
            }
            $row['tags'] = $parsed_tags;
            $menu_items[] = $row;
        }
    }
}
?>

<section class="container hero reveal">
    <div class="hero-content">
        <h1 class="hero-title">Deliciously <span class="gradient-text">Healthy</span> eating, delivered to you.</h1>
        <p>Elevate your daily nutrition with chef-crafted, sustainably sourced meals that taste as good as they make you feel. Order today for fresh delivery.</p>
        <div class="hero-actions">
            <a href="menu.php" class="btn btn-primary">Explore Menu</a>
            <a href="about.php" class="btn btn-outline">Learn More</a>
        </div>
        
        <div class="hero-badges">
            <div class="badge">🌱 100% Organic</div>
            <div class="badge">⚡ Chef Prepared</div>
        </div>
    </div>
    <div class="hero-image">
        <img src="assets/hero.png" alt="Healthy fresh salad bowl">
    </div>
</section>

<section class="menu-section container reveal">
    <div class="section-header">
        <h2>Popular Dishes</h2>
        <p class="section-subtitle">Our community's favorite healthy bites this week.</p>
    </div>
    
    <div class="menu-grid">
        <?php foreach ($menu_items as $item): ?>
        <div class="menu-card">
            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="card-img">
            <div class="card-content">
                <div class="card-header">
                    <h3 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                    <span class="card-price">₹<?php echo number_format($item['price'], 2); ?></span>
                </div>
                
                <div class="card-tags">
                    <?php foreach ($item['tags'] as $tag): ?>
                        <span class="tag"><?php echo htmlspecialchars($tag); ?></span>
                    <?php endforeach; ?>
                </div>
                
                <p class="card-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                <button class="btn add-to-cart" data-id="<?php echo htmlspecialchars($item['id']); ?>">Add to Cart</button>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="center-container">
        <a href="menu.php" class="btn btn-outline">View Full Menu</a>
    </div>
</section>

<section class="features reveal">
    <div class="container">
        <div class="section-header">
            <h2>Why Choose Nutrivo?</h2>
            <p class="section-subtitle">Premium quality ingredients handled with care.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🥗</div>
                <h3>Sourced Locally</h3>
                <p class="feature-desc">We partner with local organic farms for the freshest seasonal produce.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">👩‍🍳</div>
                <h3>Expert Dietitians</h3>
                <p class="feature-desc">Every meal is perfectly balanced for optimal macros and energy.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚀</div>
                <h3>Fast Delivery</h3>
                <p class="feature-desc">Eco-friendly packaging delivered right to your doorstep.</p>
            </div>
        </div>
    </div>
</section>

<section class="testimonials reveal bg-light">
    <div class="container">
        <div class="section-header">
            <h2>What They Say</h2>
            <p class="section-subtitle">Don't just take our word for it.</p>
        </div>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p class="quote">"Nutrivo completely changed how I eat during the week. The avocado chicken salad is mind-blowing, and I actually have energy after lunch now!"</p>
                <div class="author">- Priya S.</div>
            </div>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p class="quote">"The delivery is fast, the packaging is fully recyclable, and the food tastes like it came directly out of a high-end restaurant kitchen."</p>
                <div class="author">- Rahul M.</div>
            </div>
            <div class="testimonial-card">
                <div class="stars">★★★★★</div>
                <p class="quote">"As a vegan, the Quinoa Buddha Bowl is a lifesaver. Hard to find such fresh, nutrient-dense food delivered so reliably. Highly recommend."</p>
                <div class="author">- Ananya D.</div>
            </div>
        </div>
    </div>
</section>

<section class="newsletter-section reveal">
    <div class="container newsletter-card">
        <h2 class="newsletter-title">Join the Nutrivo Family</h2>
        <p class="newsletter-subtitle">Subscribe to get weekly nutrition tips and 15% off your first order!</p>
        
        <form class="newsletter-form" onsubmit="event.preventDefault(); alert('Thanks for subscribing!');">
            <input type="email" required class="newsletter-input">
            <button type="submit" class="btn btn-accent">Subscribe</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
