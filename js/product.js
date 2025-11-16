// js/product.js
console.log('Product.js script loaded');

// Determine base path for API calls
const getBasePath = () => {
  const path = window.location.pathname;
  // If we're in admin folder, go up one level
  if (path.includes('/admin/')) {
    return '../';
  }
  // Otherwise, assume we're at root
  return './';
};

const BASE_PATH = getBasePath();

// Wait for DOM to be ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initProductManagement);
} else {
  // DOM is already loaded
  initProductManagement();
}

function initProductManagement() {
  console.log('DOM Content Loaded - initializing product management');
  
  const productForm = document.getElementById('productForm');
  const fileInput = document.getElementById('product_image_file');
  const uploadedPreview = document.getElementById('uploadedPreview');
  const productsContainer = document.getElementById('productsContainer');
  const resetBtn = document.getElementById('resetBtn');
  const saveBtn = document.getElementById('saveProductBtn');

  // Debug: Check if all elements are found
  console.log('Checking elements:', {
    productForm: !!productForm,
    resetBtn: !!resetBtn,
    saveBtn: !!saveBtn,
    productsContainer: !!productsContainer
  });

  if (!productForm) {
    console.error('productForm not found!');
    alert('Product found. Click ok to see it');
    return;
  }
  if (!resetBtn) {
    console.error('resetBtn not found!');
  }
  if (!saveBtn) {
    console.error('saveProductBtn not found!');
    alert('Error: Save button not found. Please refresh the page.');
    return;
  }
  if (!productsContainer) {
    console.error('productsContainer not found!');
  }
  
  console.log('All elements found, initializing product management...');
  
  // Show image preview when file is selected
  if (fileInput && uploadedPreview) {
    fileInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          uploadedPreview.innerHTML = `<img src="${e.target.result}" style="max-height:120px; border:1px solid #ddd; padding:4px;">`;
        };
        reader.readAsDataURL(file);
      } else {
        uploadedPreview.innerHTML = '';
      }
    });
  }

  async function fetchProducts() {
    if (!productsContainer) {
      console.error('Cannot fetch products - productsContainer is null');
      return;
    }
    
    try {
      console.log('Fetching products...');
      const res = await fetch(BASE_PATH + 'actions/fetch_products_actions.php');
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      const data = await res.json();
      console.log('Products fetched:', data);
      if (!data.success) { 
        productsContainer.innerText = 'Error: ' + (data.error || 'Failed to load products'); 
        return; 
      }
      renderProducts(data.products || []);
    } catch (error) {
      console.error('Fetch products error:', error);
      if (productsContainer) {
        productsContainer.innerText = 'Could not load products: ' + error.message;
      }
    }
  }

  function renderProducts(products) {
    if (!productsContainer) {
      console.error('Cannot render products - productsContainer is null');
      return;
    }
    
    if (!products.length) { 
      productsContainer.innerHTML = '<p>No products yet</p>'; 
      return; 
    }
    
    let html = '<table><thead><tr><th>ID</th><th>Title</th><th>Category</th><th>Brand</th><th>Price</th><th>Image</th><th>Actions</th></tr></thead><tbody>';
    products.forEach(p => {
      html += `<tr>
        <td>${p.product_id}</td>
        <td>${escapeHtml(p.product_title)}</td>
        <td>${escapeHtml(p.category_name || '')}</td>
        <td>${escapeHtml(p.brand_name || '')}</td>
        <td>${p.product_price}</td>
        <td>${p.product_image ? '<img src="'+BASE_PATH+escapeHtml(p.product_image)+'" style="height:40px">' : ''}</td>
        <td>
          <button data-id="${p.product_id}" class="edit">Edit</button>
        </td>
      </tr>`;
    });
    html += '</tbody></table>';
    productsContainer.innerHTML = html;
  }


  // Also attach click handler to save button as backup
  if (saveBtn) {
    saveBtn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      console.log('Save button clicked directly - triggering form submit');
      // Trigger form validation first
      if (productForm.checkValidity()) {
        productForm.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
      } else {
        console.log('Form validation failed');
        productForm.reportValidity();
      }
    });
  }

  if (productForm) {
    productForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    e.stopPropagation();
    console.log('Form submit event triggered');
    
    try {
      const product_id = parseInt(document.getElementById('product_id').value || '0');
      
      // Validate required fields first
      const product_cat = document.getElementById('product_cat').value;
      const product_brand = document.getElementById('product_brand').value;
      const product_title = document.getElementById('product_title').value;
      const product_price = document.getElementById('product_price').value;
      
      if (!product_cat || !product_brand || !product_title || !product_price) {
        alert('Please fill in all required fields (Category, Brand, Title, Price)');
        return;
      }

      // Use FormData to handle file upload
      const formData = new FormData();
      formData.append('product_cat', product_cat);
      formData.append('product_brand', product_brand);
      formData.append('product_title', product_title);
      formData.append('product_price', product_price);
      formData.append('product_desc', document.getElementById('product_desc').value);
      formData.append('product_keywords', document.getElementById('product_keywords').value);
      
      // Add image file if selected
      if (fileInput && fileInput.files.length > 0) {
        formData.append('image', fileInput.files[0]);
      }
      
      if (product_id > 0) {
        formData.append('product_id', product_id);
      }

      const url = product_id > 0 ? BASE_PATH + 'actions/update_product_action.php' : BASE_PATH + 'actions/add_product_action.php';

      console.log('Saving product with FormData...');
      const res = await fetch(url, {
        method: 'POST',
        body: formData 
      });
      
      console.log('Response status:', res.status, res.statusText);
      
      let data;
      const responseText = await res.text();
      console.log('Raw response:', responseText);
      
      try {
        data = JSON.parse(responseText);
      } catch (parseError) {
        console.error('JSON parse error:', parseError);
        throw new Error('Invalid response from server: ' + responseText.substring(0, 100));
      }
      
      console.log('Parsed response:', data);
      
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status} - ${data.error || data.message || 'Unknown error'}`);
      }
      
      if (data.success) {
        alert('Product saved successfully!');
        resetForm();
        fetchProducts();
      } else {
        const errorMsg = data.error || data.message || 'Unknown error';
        console.error('Save failed:', errorMsg);
        alert('Error: ' + errorMsg);
      }
    } catch (error) {
      console.error('Save error:', error);
      alert('Failed to save product: ' + error.message);
    }
  });
  }

  if (productsContainer) {
    productsContainer.addEventListener('click', async (e) => {
    if (e.target.matches('.edit')) {
      const id = e.target.dataset.id;
      const res = await fetch(BASE_PATH + 'actions/get_product_action.php?product_id='+encodeURIComponent(id));
      const data = await res.json();
      if (!data.success) { alert('Error loading product'); return; }
      const p = data.product;
      document.getElementById('product_id').value = p.product_id;
      document.getElementById('product_cat').value = p.product_cat;
      document.getElementById('product_brand').value = p.product_brand;
      document.getElementById('product_title').value = p.product_title;
      document.getElementById('product_price').value = p.product_price;
      document.getElementById('product_desc').value = p.product_desc;
      document.getElementById('product_keywords').value = p.product_keywords;
      if (uploadedPreview) {
        uploadedPreview.innerHTML = p.product_image ? `<img src="${BASE_PATH}${escapeHtml(p.product_image)}" style="max-height:120px; border:1px solid #ddd; padding:4px;">` : '';
      }
      window.scrollTo({top:0,behavior:'smooth'});
    }
  });
  }

  if (resetBtn) {
    resetBtn.addEventListener('click', () => {
      console.log('Reset button clicked');
      resetForm();
    });
  }

  function resetForm(){
    if (productForm) {
      productForm.reset();
      const productIdEl = document.getElementById('product_id');
      if (productIdEl) productIdEl.value = 0;
    }
    if (uploadedPreview) {
      uploadedPreview.innerHTML = '';
    }
    console.log('Form reset');
  }

  function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

  // initial - only fetch if productsContainer exists
  if (productsContainer) {
    fetchProducts();
  } else {
    console.error('Cannot fetch products - productsContainer not found');
  }
}
