// cart.js - Handle all cart UI interactions

// Determine base path for API calls
const getBasePath = () => {
    const path = window.location.pathname;
    if (path.includes('/admin/')) {
        return '../../';
    } else if (path.includes('/login/')) {
        return '../';
    }
    return './';
};

const BASE_PATH = getBasePath();

// Show flash message
function showFlashMessage(message, type = 'success') {
    const flash = document.createElement('div');
    flash.className = 'flash-msg';
    flash.style.background = type === 'success' ? '#10b981' : '#ef4444';
    flash.textContent = message;
    document.body.appendChild(flash);
    
    setTimeout(() => {
        flash.style.opacity = '0';
        flash.style.transition = 'opacity 0.3s';
        setTimeout(() => flash.remove(), 300);
    }, 3000);
}

// Add to cart
async function addToCart(productId, quantity = 1) {
    try {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('qty', quantity);
        
        const response = await fetch(BASE_PATH + 'actions/add_to_cart_action.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showFlashMessage(data.message || 'Product added to cart!', 'success');
            // Update cart count if element exists
            updateCartCount();
            return true;
        } else {
            showFlashMessage(data.message || 'Failed to add to cart', 'error');
            return false;
        }
    } catch (error) {
        console.error('Add to cart error:', error);
        showFlashMessage('An error occurred. Please try again.', 'error');
        return false;
    }
}

// Update quantity
async function updateQuantity(productId, inputElement) {
    const qty = parseInt(inputElement.value) || 1;
    
    if (qty < 1) {
        inputElement.value = 1;
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('qty', qty);
        
        const response = await fetch(BASE_PATH + 'actions/update_quantity_action.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Reload page to update totals
            location.reload();
        } else {
            showFlashMessage(data.message || 'Failed to update quantity', 'error');
            // Revert input value
            location.reload();
        }
    } catch (error) {
        console.error('Update quantity error:', error);
        showFlashMessage('An error occurred. Please try again.', 'error');
        location.reload();
    }
}

// Remove item from cart
async function removeItem(productId) {
    if (!confirm('Are you sure you want to remove this item from your cart?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('product_id', productId);
        
        const response = await fetch(BASE_PATH + 'actions/remove_from_cart_action.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showFlashMessage(data.message || 'Item removed from cart', 'success');
            // Reload page to update cart
            setTimeout(() => location.reload(), 500);
        } else {
            showFlashMessage(data.message || 'Failed to remove item', 'error');
        }
    } catch (error) {
        console.error('Remove item error:', error);
        showFlashMessage('An error occurred. Please try again.', 'error');
    }
}

// Empty cart
async function emptyCart() {
    if (!confirm('Are you sure you want to empty your cart? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(BASE_PATH + 'actions/empty_cart_action.php', {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showFlashMessage(data.message || 'Cart emptied successfully', 'success');
            // Reload page
            setTimeout(() => location.reload(), 500);
        } else {
            showFlashMessage(data.message || 'Failed to empty cart', 'error');
        }
    } catch (error) {
        console.error('Empty cart error:', error);
        showFlashMessage('An error occurred. Please try again.', 'error');
    }
}

// Update cart count (if cart count element exists)
async function updateCartCount() {
    const cartCountEl = document.getElementById('cartCount');
    if (cartCountEl) {
        const current = parseInt(cartCountEl.textContent) || 0;
        const newCount = current + 1;
        cartCountEl.textContent = newCount;
        cartCountEl.style.display = newCount > 0 ? 'block' : 'none';
    }
}

// Load cart count on page load
async function loadCartCount() {
    const cartCountEl = document.getElementById('cartCount');
    if (cartCountEl && typeof BASE_PATH !== 'undefined') {
        try {
            // You can create an endpoint to get cart count, or reload the page
            // For now, we'll keep the server-side count
        } catch (error) {
            console.error('Error loading cart count:', error);
        }
    }
}

// Initialize cart page
document.addEventListener('DOMContentLoaded', function() {
    // Make functions globally available
    window.addToCart = addToCart;
    window.updateQuantity = updateQuantity;
    window.removeItem = removeItem;
    window.emptyCart = emptyCart;
    window.updateCartCount = updateCartCount;
    
    // Load cart count on page load
    loadCartCount();
    
    console.log('Cart.js loaded');
});
