const CART_KEY = 'glowmart_cart';

function getCart() {
  try { 
    return JSON.parse(localStorage.getItem(CART_KEY)) || []; 
  }
  catch { 
    return []; 
  }
}

function saveCart(cart) {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
}

function findProduct(id) {
  return window.PRODUCTS ? window.PRODUCTS.find(p => p.id === id) : null;
}

function addToCart(productId) {
  const qtyInput = document.getElementById(`qty-${productId}`);
  const qty = qtyInput ? parseInt(qtyInput.value) : 1;
  if (!qty || qty < 1) {
    alert('Enter a valid quantity');
    return;
  }
  const prod = findProduct(productId);
  if (!prod) {
    alert('Product not found');
    return;
  }
  const cart = getCart();
  const existing = cart.find(i => i.id === prod.id);
  if (existing) existing.quantity += qty;
  else cart.push({ id: prod.id, name: prod.name, price: prod.price, quantity: qty });
  saveCart(cart);
  alert(`${prod.name} x ${qty} added to cart`);
}

function escapeHtml(text) {
  const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
  return String(text).replace(/[&<>"']/g, m => map[m]);
}

window.getCart = getCart;
window.addToCart = addToCart;
window.escapeHtml = escapeHtml;
