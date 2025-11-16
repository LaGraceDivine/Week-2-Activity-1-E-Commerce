// checkout.js - Handle checkout and payment simulation

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

// Update payment amount display
function updatePaymentAmount() {
    const totalEl = document.getElementById('checkoutTotal');
    const amountEl = document.getElementById('paymentAmount');
    if (totalEl && amountEl) {
        amountEl.textContent = totalEl.textContent;
    }
}

// Show payment modal
function showPaymentModal() {
    updatePaymentAmount();
    const modal = document.getElementById('paymentModal');
    
    if (modal) {
        modal.style.display = 'flex';
        modal.style.opacity = '1';
        setTimeout(() => {
            const content = modal.querySelector('.modal-content');
            if (content) {
                content.style.transform = 'scale(1)';
            }
        }, 10);
    }
}

// Close payment modal
function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.querySelector('.modal-content').style.transform = 'scale(0.9)';
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
    }
}

// Process checkout
async function processCheckout() {
    const confirmBtn = document.getElementById('confirmPaymentBtn');
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.textContent = 'Processing...';
    }
    
    try {
        const response = await fetch(BASE_PATH + 'actions/process_checkout_action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Close payment modal
            closePaymentModal();
            
            // Show success modal
            showSuccessModal(data);
        } else {
            // Show error message
            alert('Checkout failed: ' + (data.message || 'Unknown error'));
            
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.textContent = '💳 Pay Now';
            }
        }
    } catch (error) {
        console.error('Checkout error:', error);
        alert('An error occurred during checkout. Please try again.');
        
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.textContent = '💳 Pay Now';
        }
    }
}

// Show success modal
function showSuccessModal(data) {
    const modal = document.getElementById('successModal');
    
    if (modal) {
        // Populate success modal with order details
        const invoiceEl = document.getElementById('successInvoice');
        const amountEl = document.getElementById('successAmount');
        const dateEl = document.getElementById('successDate');
        const itemsEl = document.getElementById('successItems');
        
        if (invoiceEl) invoiceEl.textContent = data.invoice || 'N/A';
        if (amountEl) amountEl.textContent = (data.currency || 'GHS') + ' ' + (data.amount || '0.00');
        if (dateEl) dateEl.textContent = new Date().toLocaleDateString();
        if (itemsEl) itemsEl.textContent = (data.item_count || 0) + ' item(s)';
        
        modal.style.display = 'flex';
        modal.style.opacity = '1';
        setTimeout(() => {
            modal.querySelector('.modal-content').style.transform = 'scale(1)';
        }, 10);
    }
}

// Continue shopping
function continueShopping() {
    window.location.href = BASE_PATH + 'index.php';
}

// View orders
function viewOrders() {
    // Redirect to orders page if it exists, otherwise go to home
    window.location.href = BASE_PATH + 'index.php';
}

// Load cart items for checkout display
async function loadCheckoutItems() {
    try {
        const response = await fetch(BASE_PATH + 'actions/get_cart_items.php');
        // For now, we'll use the items from PHP
        // This can be enhanced to load via AJAX if needed
    } catch (error) {
        console.error('Load checkout items error:', error);
    }
}

// Initialize checkout page
document.addEventListener('DOMContentLoaded', function() {
    // Make functions globally available
    window.showPaymentModal = showPaymentModal;
    window.closePaymentModal = closePaymentModal;
    window.processCheckout = processCheckout;
    window.continueShopping = continueShopping;
    window.viewOrders = viewOrders;
    
    // Close modal when clicking outside
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                if (modal.id === 'paymentModal') {
                    closePaymentModal();
                }
            }
        });
    });
    
    console.log('Checkout.js loaded');
});
