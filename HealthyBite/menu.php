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

$menu_items = [];
if (isset($conn)) {
    $res = $conn->query("SELECT * FROM menu_items");
    if($res) {
        while($row = $res->fetch_assoc()) {
            $parsed = json_decode($row['tags'], true);
            if ($parsed == null) {
                $row['tags'] = array();
            } else {
                $row['tags'] = $parsed;
            }
            $menu_items[] = $row;
        }
    }
}
?>

<div class="hero-bg-light">
    <div class="container">
        <h1 class="hero-heading">Our Signature Menu</h1>
        <p class="hero-subtext">
            Explore our chef-curated selection of vibrant, nutrient-dense meals. Filter by categories and dietary preferences.
        </p>
    </div>
</div>

<section class="menu-section container">
    <div class="filter-container" id="filter-container">
        <?php 
        $categories = array_unique(array_column($menu_items, 'category'));
        sort($categories);
        ?>
        <button class="btn btn-accent" id="openAddModalBtn">+ Add Item</button>
        <button class="btn filter-btn active" data-filter="all">All Items</button>
        <?php foreach($categories as $cat): ?>
            <button class="btn filter-btn" data-filter="<?php echo htmlspecialchars($cat); ?>"><?php echo htmlspecialchars($cat); ?></button>
        <?php endforeach; ?>
    </div>

    <div class="menu-grid" id="menu-grid">
        <?php foreach ($menu_items as $item): ?>
        <div class="menu-card menu-item" data-category="<?php echo htmlspecialchars($item['category']); ?>">
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
</section>

<div id="addItemModal" class="modal-overlay">
    <div class="modal-content">
        <h2 style="margin-bottom:1.5rem; color:var(--primary-dark);">Add New Menu Item</h2>
        <form id="addItemForm">
            <div class="form-group">
                <label>Item Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <input type="text" name="category" required>
            </div>
            <div style="display:flex; gap:1rem; margin-bottom:1.5rem;">
                <div class="form-group" style="flex:1; margin-bottom:0;">
                    <label>Price (₹)</label>
                    <input type="number" name="price" step="0.01" required>
                </div>
                <div class="form-group" style="flex:1; margin-bottom:0;">
                    <label>Image URL</label>
                    <input type="text" name="image" value="assets/hero.png" required>
                </div>
            </div>
            <div class="form-group">
                <label>Tags (comma separated)</label>
                <input type="text" name="tags">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="3" required></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" id="closeModalBtn">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveItemBtn">Save Item</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const addModal = document.getElementById('addItemModal');
    const openBtn = document.getElementById('openAddModalBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const form = document.getElementById('addItemForm');

    if(openBtn) openBtn.addEventListener('click', () => addModal.classList.add('show'));
    if(closeBtn) closeBtn.addEventListener('click', () => addModal.classList.remove('show'));

    if(form) {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const btn = document.getElementById('saveItemBtn');
            btn.innerText = 'Saving...';
            btn.disabled = true;

            fetch('api_add_item.php', {
                method: 'POST',
                body: new FormData(form)
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    window.location.reload(); 
                } else {
                    alert('Error: ' + data.message);
                    btn.innerText = 'Save Item';
                    btn.disabled = false;
                }
            })
            .catch(err => {
                btn.innerText = 'Save Item';
                btn.disabled = false;
            });
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
