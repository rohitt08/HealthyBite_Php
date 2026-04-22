<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
$pageTitles = [
    'index.php'   => 'Nutrivo | Fresh Healthy Food Delivery',
    'menu.php'    => 'Our Menu | Nutrivo',
    'about.php'   => 'About Us | Nutrivo',
    'contact.php' => 'Contact Us | Nutrivo',
    'cart.php'    => 'Your Cart | Nutrivo',
    'orders.php'  => 'My Orders | Nutrivo',
    'login.php'   => 'Login | Nutrivo',
];
$pageTitle = $pageTitles[$currentPage] ?? 'Nutrivo | Premium Fresh Food Delivery';
$cart_count = 0;
if(isset($conn)) {
    $sess_id = session_id();
    $cc_query = $conn->prepare("SELECT SUM(quantity) as t_count FROM cart WHERE session_id = ?");
    $cc_query->bind_param("s", $sess_id);
    $cc_query->execute();
    $res = $cc_query->get_result();
    $row = $res->fetch_assoc();
    $cart_count = $row['t_count'] ?? 0;
} else {
    if(file_exists(__DIR__ . '/db.php')) {
        include_once __DIR__ . '/db.php';
        $sess_id = session_id();
        $cc_query = $conn->prepare("SELECT SUM(quantity) as t_count FROM cart WHERE session_id = ?");
        $cc_query->bind_param("s", $sess_id);
        $cc_query->execute();
        $res = $cc_query->get_result();
        $row = $res->fetch_assoc();
        $cart_count = $row['t_count'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@400;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/png" href="assets/logo.png">
    <meta name="description" content="Nutrivo offers premium, organic, and delicious healthy food delivered right to your door.">
</head>
<body>

<header>
    <div class="container nav-container">
        <a href="index.php" class="logo">
            <img src="assets/logo.png" alt="Nutrivo Logo" class="brand-logo"> Nutrivo
        </a>
        <ul class="nav-links">
            <li><a href="index.php" class="<?php echo ($currentPage == 'index.php') ? 'active' : ''; ?>">Home</a></li>
            <li><a href="menu.php" class="<?php echo ($currentPage == 'menu.php') ? 'active' : ''; ?>">Our Menu</a></li>
            <li><a href="about.php" class="<?php echo ($currentPage == 'about.php') ? 'active' : ''; ?>">About</a></li>
            <li><a href="contact.php" class="<?php echo ($currentPage == 'contact.php') ? 'active' : ''; ?>">Contact</a></li>
        </ul>
        <div class="nav-actions">
            <a href="cart.php" class="cart-icon">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                <span id="cart-badge" class="cart-badge" style="display: <?php echo $cart_count > 0 ? 'flex' : 'none'; ?>;"><?php echo $cart_count; ?></span>
            </a>
            <a href="menu.php" class="btn btn-primary">Order Now</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-dropdown" id="userDropdown">
                    <button class="user-dropdown-btn" id="userDropdownBtn" aria-expanded="false" aria-haspopup="true">
                        <span class="user-avatar">👋</span>
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                        <svg class="dropdown-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                    <div class="dropdown-menu" id="dropdownMenu" role="menu">
                        <div class="dropdown-header">
                            <div class="dropdown-user-info">
                                <div class="dropdown-avatar">👤</div>
                                <div>
                                    <div class="dropdown-username"><?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="orders.php" class="dropdown-item" role="menuitem">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            My Orders
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item dropdown-item-danger" role="menuitem">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<script>
(function() {
    const btn = document.getElementById('userDropdownBtn');
    const menu = document.getElementById('dropdownMenu');
    const wrapper = document.getElementById('userDropdown');
    if (!btn) return;
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const isOpen = wrapper.classList.toggle('open');
        btn.setAttribute('aria-expanded', isOpen);
    });
    document.addEventListener('click', function(e) {
        if (!wrapper.contains(e.target)) {
            wrapper.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            wrapper.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
        }
    });
})();
</script>
<main>
