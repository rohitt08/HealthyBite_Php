document.addEventListener('DOMContentLoaded', () => {
    const reveals = document.querySelectorAll('.reveal');

    const revealOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px"
    };

    const revealOnScroll = new IntersectionObserver(function(
        entries,
        observer
    ) {
        entries.forEach(entry => {
            if (!entry.isIntersecting) {
                return;
            } else {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, revealOptions);

    reveals.forEach(reveal => {
        revealOnScroll.observe(reveal);
    });

    const filterBtns = document.querySelectorAll('.filter-btn');
    const menuItems = document.querySelectorAll('.menu-item');

    if(filterBtns.length > 0 && menuItems.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                
                btn.classList.add('active');
                
                const filterValue = btn.getAttribute('data-filter');
                
                menuItems.forEach(item => {
                    if (filterValue === 'all' || item.getAttribute('data-category') === filterValue) {
                        item.classList.remove('hidden-item');
                    } else {
                        item.classList.add('hidden-item');
                    }
                });
            });
        });
    }

    const updateCartBadge = (count) => {
        const badge = document.getElementById('cart-badge');
        if (badge) {
            badge.innerText = count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }
    };

    const addToCartBtns = document.querySelectorAll('.add-to-cart');
    addToCartBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const target = e.currentTarget;
            const itemId = target.getAttribute('data-id');
            if (!itemId) return;

            const formData = new FormData();
            formData.append('action', 'add');
            formData.append('item_id', itemId);

            const originalText = target.innerText;
            target.disabled = true;
            target.innerText = 'Adding...';

            fetch('cart_action.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    updateCartBadge(data.cart_count);
                    target.innerText = '✓ Added!';
                    target.style.background = 'var(--primary-color)';
                    target.style.color = 'white';
                    setTimeout(() => {
                        target.innerText = originalText;
                        target.style.background = '';
                        target.style.color = '';
                        target.disabled = false;
                    }, 2000);
                } else {
                    target.disabled = false;
                    target.innerText = originalText;
                }
            })
            .catch(() => {
                target.disabled = false;
                target.innerText = originalText;
            });
        });
    });

    const cartContainer = document.querySelector('.cart-grid');
    if (cartContainer) {
        cartContainer.addEventListener('click', (e) => {
            const row = e.target.closest('.cart-item');
            if(!row) return;
            const itemId = row.getAttribute('data-id');
            const qtyInput = row.querySelector('.qty-input');
            let newQty = parseInt(qtyInput.value);
            
            let action = '';

            if (e.target.classList.contains('plus-qty')) {
                newQty++;
                action = 'update_qty';
            } else if (e.target.classList.contains('minus-qty')) {
                newQty--;
                if(newQty < 1) newQty = 1; 
                action = 'update_qty';
            } else if (e.target.classList.contains('remove-item')) {
                action = 'remove';
            } else {
                return; 
            }

            const formData = new FormData();
            formData.append('action', action);
            formData.append('item_id', itemId);
            if(action === 'update_qty') formData.append('quantity', newQty);

            fetch('cart_action.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    updateCartBadge(data.cart_count);
                    window.location.reload();
                }
            });
        });
    }
});
