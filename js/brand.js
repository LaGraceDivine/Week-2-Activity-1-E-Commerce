/**
 * Brand Management JavaScript
 * Handles all AJAX operations for brand CRUD
 */

$(document).ready(function() {
    
    // Load brands on page load
    loadBrands();
    
    // Add Brand Form Submit
    $("#addBrandForm").submit(function(e) {
        e.preventDefault();
        
        const brandName = $("#brand_name").val().trim();
        const categoryId = $("#category_id").val();
        
        // Client-side validation
        if (!brandName) {
            alert("Please enter a brand name");
            return;
        }
        
        if (!categoryId) {
            alert("Please select a category");
            return;
        }
        
        // Disable submit button to prevent double submission
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Adding...');
        
        $.ajax({
            type: "POST",
            url: "../actions/add_brand_action.php",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                console.log("Add Response:", response);
                
                if (response.success) {
                    alert(response.message);
                    $("#addBrandForm")[0].reset();
                    loadBrands(); // Refresh the table
                } else {
                    alert("Error: " + (response.error || "Failed to add brand"));
                }
            },
            error: function(xhr, status, error) {
                console.error("Add Error:", xhr.responseText);
                alert("Failed to add brand. Please try again.");
            },
            complete: function() {
                // Re-enable submit button
                submitBtn.prop('disabled', false).text('Add Brand');
            }
        });
    });
    
    // Edit Brand Form Submit
    $("#editBrandForm").submit(function(e) {
        e.preventDefault();
        
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Updating...');
        
        $.ajax({
            type: "POST",
            url: "../actions/update_brand_action.php",
            data: $(this).serialize(),
            dataType: "json",
            success: function(response) {
                console.log("Update Response:", response);
                
                if (response.success) {
                    alert(response.message);
                    closeEditModal();
                    loadBrands(); // Refresh the table
                } else {
                    alert("Error: " + (response.error || "Failed to update brand"));
                }
            },
            error: function(xhr, status, error) {
                console.error("Update Error:", xhr.responseText);
                alert("Failed to update brand. Please try again.");
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Update Brand');
            }
        });
    });
    
    // Delete Brand Button Click (Event Delegation)
    $(document).on('click', '.btn-delete', function() {
        const brandId = $(this).data('id');
        const brandName = $(this).data('name');
        
        if (!confirm(`Are you sure you want to delete "${brandName}"?`)) {
            return;
        }
        
        $.ajax({
            type: "POST",
            url: "../actions/delete_brand_action.php",
            data: { brand_id: brandId },
            dataType: "json",
            success: function(response) {
                console.log("Delete Response:", response);
                
                if (response.success) {
                    alert(response.message);
                    loadBrands(); // Refresh the table
                } else {
                    alert("Error: " + (response.error || "Failed to delete brand"));
                }
            },
            error: function(xhr, status, error) {
                console.error("Delete Error:", xhr.responseText);
                alert("Failed to delete brand. Please try again.");
            }
        });
    });
    
    // Edit Brand Button Click (Event Delegation)
    $(document).on('click', '.btn-edit', function() {
        const brandId = $(this).data('id');
        const brandName = $(this).data('name');
        const categoryId = $(this).data('category');
        
        // Populate edit form
        $("#edit_brand_id").val(brandId);
        $("#edit_brand_name").val(brandName);
        $("#edit_category_id").val(categoryId);
        
        // Show modal
        $("#editModal").addClass('active');
    });
    
});

/**
 * Load all brands from server
 */
function loadBrands() {
    $.ajax({
        url: "../actions/fetch_brand_action.php",
        method: "GET",
        dataType: "json",
        success: function(response) {
            console.log("Fetch Response:", response);
            
            if (response.success) {
                if (response.brands && response.brands.length > 0) {
                    displayBrands(response.brands);
                } else {
                    $("#brandTable tbody").html(
                        '<tr><td colspan="4" class="no-data">No brands found. Add your first brand above!</td></tr>'
                    );
                }
            } else {
                console.error("Fetch failed:", response.error);
                $("#brandTable tbody").html(
                    '<tr><td colspan="4" class="no-data">Failed to load brands</td></tr>'
                );
            }
        },
        error: function(xhr, status, error) {
            console.error("Fetch Error:", xhr.responseText);
            $("#brandTable tbody").html(
                '<tr><td colspan="4" class="no-data">Error loading brands. Please refresh the page.</td></tr>'
            );
        }
    });
}

/**
 * Display brands in table
 * @param {Array} brands - Array of brand objects
 */
function displayBrands(brands) {
    let rows = "";
    
    brands.forEach(function(brand) {
        rows += `
            <tr>
                <td>${escapeHtml(brand.brand_id)}</td>
                <td>${escapeHtml(brand.brand_name)}</td>
                <td>${escapeHtml(brand.category_name || 'N/A')}</td>
                <td>
                    <button class="btn-edit" 
                            data-id="${escapeHtml(brand.brand_id)}" 
                            data-name="${escapeHtml(brand.brand_name)}" 
                            data-category="${escapeHtml(brand.cat_id)}">
                        Edit
                    </button>
                    <button class="btn-delete" 
                            data-id="${escapeHtml(brand.brand_id)}" 
                            data-name="${escapeHtml(brand.brand_name)}">
                        Delete
                    </button>
                </td>
            </tr>
        `;
    });
    
    $("#brandTable tbody").html(rows);
}

/**
 * Close edit modal
 */
function closeEditModal() {
    $("#editModal").removeClass('active');
    $("#editBrandForm")[0].reset();
}

/**
 * Escape HTML to prevent XSS
 * @param {string} text - Text to escape
 * @return {string} Escaped text
 */
function escapeHtml(text) {
    if (text === null || text === undefined) {
        return '';
    }
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Close modal when clicking outside
$(document).on('click', '.modal', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Close modal on Escape key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});