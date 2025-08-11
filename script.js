const CART_KEY = 'glowmart_cart';

// Get cart from localStorage
function getCart() {
  try {
    return JSON.parse(localStorage.getItem(CART_KEY)) || [];
  } catch {
    return [];
  }
}

// Save cart to localStorage
function saveCart(cart) {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
}

// Find product by id from PRODUCTS array (defined in index.html)
function findProductById(id) {
  if (!window.PRODUCTS) return null;
  return window.PRODUCTS.find(p => Number(p.id) === Number(id)) || null;
}

// Add to cart (called from index product cards)
function addToCart(productId) {
  const prod = findProductById(productId);
  if (!prod) {
    alert('Product not found');
    return;
  }
  const qtyInput = document.getElementById(`qty-${productId}`);
  const qty = qtyInput ? Math.max(1, parseInt(qtyInput.value) || 1) : 1;

  const cart = getCart();
  const existing = cart.find(i => Number(i.id) === Number(prod.id));
  if (existing) existing.quantity = Number(existing.quantity) + Number(qty);
  else cart.push({ id: prod.id, name: prod.name, price: Number(prod.price), img: prod.img, quantity: Number(qty) });

  saveCart(cart);
  alert(`${prod.name} × ${qty} added to cart`);
}

// Render cart on checkout page
function renderCheckoutCart() {
  const cart = getCart();
  const tbody = document.getElementById('cartItemsBody');
  const totalEl = document.getElementById('totalAmount');
  if (!tbody || !totalEl) return;

  tbody.innerHTML = '';

  if (cart.length === 0) {
    tbody.innerHTML = `<tr><td colspan="4" class="text-center">Your cart is empty.</td></tr>`;
    totalEl.textContent = '0.00';
    return;
  }

  let total = 0;

  cart.forEach(item => {
    const subtotal = item.price * item.quantity;
    total += subtotal;

    const tr = document.createElement('tr');

    tr.innerHTML = `
      <td>
        <img src="${item.img}" alt="${item.name}" style="width:40px; height:40px; object-fit:cover; border-radius:5px; margin-right:10px; vertical-align: middle;">
        ${item.name}
      </td>
      <td>₹${item.price.toFixed(2)}</td>
      <td>${item.quantity}</td>
      <td>₹${subtotal.toFixed(2)}</td>
    `;

    tbody.appendChild(tr);
  });

  totalEl.textContent = total.toFixed(2);
}

// Escape HTML helper
function escapeHtml(text) {
  const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
  return String(text).replace(/[&<>"']/g, m => map[m]);
}

// Place order - sends data to save_order.php
async function placeOrder() {
  const name = document.getElementById('customerName').value.trim();
  const email = document.getElementById('customerEmail').value.trim();
  const address = document.getElementById('customerAddress').value.trim();
  const statusEl = document.getElementById('orderStatus');

  if (!name || !email || !address) {
    alert('Please fill in all shipping details.');
    return;
  }

  const cart = getCart();
  if (cart.length === 0) {
    alert('Your cart is empty. Add items before placing an order.');
    return;
  }

  if (statusEl) {
    statusEl.style.color = '#6a3f7c';
    statusEl.textContent = 'Placing order...';
  }

  try {
    const response = await fetch('save_order.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        customerName: name,
        customerEmail: email,
        customerAddress: address,
        cart: cart
      })
    });

    const data = await response.json();

    if (response.ok && data.status === 'success') {
      // Order saved successfully
      localStorage.removeItem(CART_KEY);
      renderCheckoutCart();

      // Clear form fields
      document.getElementById('customerName').value = '';
      document.getElementById('customerEmail').value = '';
      document.getElementById('customerAddress').value = '';

      if (statusEl) {
        statusEl.textContent = `Thank you, ${escapeHtml(name)}! Your order has been placed successfully. Order ID: ${data.order_id}`;
      }

      alert(`Order placed successfully! Order ID: ${data.order_id}`);

      // Optionally redirect to home or orders page
      // window.location.href = 'index.html';

    } else {
      const msg = data.message || 'Failed to place order.';
      if (statusEl) statusEl.textContent = msg;
      alert(msg);
    }

  } catch (error) {
    if (statusEl) {
      statusEl.textContent = 'Network error, please try again.';
    }
    alert('Network error, please try again.');
    console.error('Place order error:', error);
  }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  renderCheckoutCart();
  const placeOrderBtn = document.getElementById('placeOrderBtn');
  if (placeOrderBtn) {
    placeOrderBtn.addEventListener('click', placeOrder);
  }
});

// Expose addToCart globally (called from inline onclick in index.html)
window.addToCart = addToCart;
window.renderCheckoutCart = renderCheckoutCart;
window.placeOrder = placeOrder;
