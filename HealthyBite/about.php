<?php 
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
include 'includes/header.php'; 
?>

<div class="hero-bg-light reveal">
    <div class="container">
        <h1 class="hero-heading">Food That Heals,<br>Delivered Fresh.</h1>
        <p class="hero-subtext">
            At Nutrivo, we believe that nutrition shouldn't compromise on taste or convenience. We're on a mission to reconnect people with vibrant, earth-grown ingredients curated by culinary experts.
        </p>
    </div>
</div>

<section class="story-section container">
    <div class="story-grid reveal">
        <div>
            <img src="assets/hero.png" alt="Our Story" class="story-img">
        </div>
        <div>
            <h2 class="story-title">Our Story</h2>
            <p class="story-text">
                It started in 2024 with a simple observation: eating healthy during a busy workweek usually means sacrificing flavor or breaking the bank. Nutrivo was founded in a humble kitchen to bridge that gap.
            </p>
            <p class="story-text">
                Today, we partner with over 40 local organic farms to source the absolute best seasonal produce, and our dedicated team of dietitians and head chefs hand-craft meals that feed your body and soul.
            </p>
        </div>
    </div>
</section>

<section class="features bg-white">
    <div class="container reveal">
        <div class="section-header">
            <h2>Our Core Values</h2>
            <p class="section-subtitle">Sanely simple principles that guide every meal we prep.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🌱</div>
                <h3>100% Sustainability</h3>
                <p class="feature-desc">Zero-waste kitchens, fully compostable packaging, and a commitment to carbon-neutral delivery.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🤝</div>
                <h3>Community First</h3>
                <p class="feature-desc">We donate 5% of all meals produced directly to local food banks and community shelters.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔍</div>
                <h3>Total Transparency</h3>
                <p class="feature-desc">No hidden sugars, no artificial preservatives. You know exactly what's inside every bite.</p>
            </div>
        </div>
    </div>
</section>

<section class="team-section">
    <div class="container reveal">
        <div class="section-header">
            <h2>Meet the Minds</h2>
            <p class="section-subtitle">The passionately healthy humans behind Nutrivo.</p>
        </div>
        
        <div class="team-grid">
            <div class="team-card">
                <div class="profile-container profile-img">
                    <div class="profile-emoji">👩🏽‍💼</div>
                </div>
                <h3>Rohini Sharma</h3>
                <p class="team-role">Founder & CEO</p>
                <p class="team-desc">Former personal trainer obsessed with making elite nutrition accessible to everyone.</p>
            </div>
            <div class="team-card">
                <div class="profile-container profile-img">
                    <div class="profile-emoji">👨🏻‍🍳</div>
                </div>
                <h3>Chef Vikram Singh</h3>
                <p class="team-role">Head Culinary Chef</p>
                <p class="team-desc">Trained in Michelin-starred kitchens, Marcus brings fine dining flavor to fitness macros.</p>
            </div>
            <div class="team-card">
                <div class="profile-container profile-img">
                    <div class="profile-emoji">👩🏼‍⚕️</div>
                </div>
                <h3>Dr. Neha Kapoor</h3>
                <p class="team-role">Lead Dietitian</p>
                <p class="team-desc">Ensures every meal hits perfectly balanced nutritional thresholds for sustainable energy.</p>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
