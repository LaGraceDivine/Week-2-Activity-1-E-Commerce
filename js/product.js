// js/product.js
document.addEventListener('DOMContentLoaded', () => {
  const productForm = document.getElementById('productForm');
  const uploadBtn = document.getElementById('uploadImageBtn');
  const fileInput = document.getElementById('product_image_file');
  const uploadedPreview = document.getElementById('uploadedPreview');
  const productsContainer = document.getElementById('productsContainer');
  let lastUploadedPath = null;

  async function fetchProducts() {
    // Use an actions endpoint or load server-side; for simplicity we'll ask for all for admin
    const res = await fetch('/actions/fetch_products_action.php').catch(()=>null);
    if (!res) { productsContainer.innerText = 'Could not load products'; return; }
    const data = await res.json();
    if (!data.success) { productsContainer.innerText = 'Error: '+(data.error||''); return; }
    renderProducts(data.products);
  }

  function renderProducts(products) {
    if (!products.length) { productsContainer.innerHTML = '<p>No products yet</p>'; return; }
    let html = '<table><thead><tr><th>ID</th><th>Title</th><th>Category</th><th>Brand</th><th>Price</th><th>Image</th><th>Actions</th></tr></thead><tbody>';
    products.forEach(p => {
      html += `<tr>
        <td>${p.product_id}</td>
        <td>${escapeHtml(p.product_title)}</td>
        <td>${escapeHtml(p.category_name || '')}</td>
        <td>${escapeHtml(p.brand_name || '')}</td>
        <td>${p.product_price}</td>
        <td>${p.product_image ? '<img src="/'+escapeHtml(p.product_image)+'" style="height:40px">' : ''}</td>
        <td>
          <button data-id="${p.product_id}" class="edit">Edit</button>
        </td>
      </tr>`;
    });
    html += '</tbody></table>';
    productsContainer.innerHTML = html;
  }

  uploadBtn.addEventListener('click', async () => {
    if (!fileInput.files.length) { alert('Select a file'); return; }
    const fd = new FormData();
    fd.append('image', fileInput.files[0]);
    // product_id optional - when editing set it
    const productId = document.getElementById('product_id').value;
    if (productId && parseInt(productId) > 0) fd.append('product_id', productId);

    const res = await fetch('/actions/upload_product_image_action.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.success) {
      lastUploadedPath = data.path;
      uploadedPreview.innerHTML = `<p>Uploaded: ${escapeHtml(lastUploadedPath)}</p><img src="/${escapeHtml(lastUploadedPath)}" style="max-height:120px">`;
      alert('Image uploaded');
    } else alert('Upload error: ' + (data.error||''));
  });

  productForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const product_id = parseInt(document.getElementById('product_id').value || '0');
    const payload = {
      product_cat: document.getElementById('product_cat').value,
      product_brand: document.getElementById('product_brand').value,
      product_title: document.getElementById('product_title').value,
      product_price: document.getElementById('product_price').value,
      product_desc: document.getElementById('product_desc').value,
      product_keywords: document.getElementById('product_keywords').value,
      product_image: lastUploadedPath
    };

    const url = product_id > 0 ? '/actions/update_product_action.php' : '/actions/add_product_action.php';
    if (product_id > 0) payload.product_id = product_id;

    const res = await fetch(url, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (data.success) {
      alert('Saved');
      resetForm();
      fetchProducts();
    } else alert('Error: ' + (data.error || 'unknown'));
  });

  productsContainer.addEventListener('click', async (e) => {
    if (e.target.matches('.edit')) {
      const id = e.target.dataset.id;
      const res = await fetch('/actions/get_product_action.php?product_id='+encodeURIComponent(id));
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
      lastUploadedPath = p.product_image;
      uploadedPreview.innerHTML = p.product_image ? `<img src="/${escapeHtml(p.product_image)}" style="max-height:120px">` : '';
      window.scrollTo({top:0,behavior:'smooth'});
    }
  });

  document.getElementById('resetBtn').addEventListener('click', resetForm);

  function resetForm(){
    productForm.reset();
    document.getElementById('product_id').value = 0;
    uploadedPreview.innerHTML = '';
    lastUploadedPath = null;
  }

  function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

  // initial
  fetchProducts();
});
