<script>
  import axios from "axios";
  import Swal from "sweetalert2";
  import page from "page";
  import { cart, isLoggedIn } from "../store";
  import { numberWithCommas } from "../utils/utils";

  const API_URL = "http://localhost:8000/api";

  // Edit Modal State
  let showEditModal = false;
  let editingCartItem = null;
  let editQuantity = 1;
  let editNotes = "";
  let editSubmitting = false;

  // Reactively calculate total price
  $: totalPrice = $cart.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);

  function openEditModal(item) {
    editingCartItem = item;
    editQuantity = item.quantity;
    editNotes = item.notes || "";
    showEditModal = true;
  }

  function closeEditModal() {
    showEditModal = false;
    editingCartItem = null;
  }

  function incrementQty() {
    editQuantity += 1;
  }

  function decrementQty() {
    if (editQuantity > 1) {
      editQuantity -= 1;
    }
  }

  async function handleUpdateCartItem() {
    if (!editingCartItem) return;
    
    editSubmitting = true;
    try {
      await axios.put(`${API_URL}/carts/${editingCartItem.id}`, {
        quantity: editQuantity,
        notes: editNotes,
      });

      // Refresh cart store
      const cartRes = await axios.get(`${API_URL}/carts`);
      cart.set(cartRes.data);

      closeEditModal();
      
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Keranjang diperbarui",
        showConfirmButton: false,
        timer: 1500,
        timerProgressBar: true,
      });
    } catch (err) {
      console.error("Error updating cart item:", err);
      Swal.fire({
        icon: "error",
        title: "Gagal memperbarui",
        text: "Terjadi kesalahan saat memperbarui item.",
        confirmButtonColor: "#dc3545",
      });
    } finally {
      editSubmitting = false;
    }
  }

  async function handleDeleteCartItem(id) {
    Swal.fire({
      title: "Hapus Item?",
      text: "Apakah Anda yakin ingin menghapus item ini dari keranjang?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
      cancelButtonColor: "#6c757d",
      confirmButtonText: "Hapus",
      cancelButtonText: "Batal",
    }).then(async (result) => {
      if (result.isConfirmed) {
        try {
          await axios.delete(`${API_URL}/carts/${id}`);

          // Refresh cart store
          const cartRes = await axios.get(`${API_URL}/carts`);
          cart.set(cartRes.data);

          Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Item berhasil dihapus",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
          });
        } catch (err) {
          console.error("Error deleting cart item:", err);
        }
      }
    });
  }

  function proceedToCheckout() {
    page("/checkout");
  }
</script>

<div class="cart-section p-3 bg-white rounded shadow-sm border border-light">
  <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-cart3 me-2 text-warning"></i>Keranjang</h5>
  <hr />

  <!-- Cart Items List (Guests can still see local cart items if we keep it, but wait! persistent cart in Laravel requires auth. Let's show login message or items.) -->
  {#if !$isLoggedIn}
    <div class="text-center py-4">
      <i class="bi bi-shield-lock-fill text-muted" style="font-size: 2.5rem;"></i>
      <p class="text-muted mt-3 mb-4" style="font-size: 0.9rem;">
        Silakan masuk ke akun Anda untuk melihat keranjang dan mulai memesan makanan.
      </p>
      <a href="/login" class="btn btn-warning w-100 fw-bold text-white shadow-sm btn-login-nav">Masuk</a>
    </div>
  {:else if $cart.length === 0}
    <div class="text-center py-5">
      <i class="bi bi-cart-x text-muted" style="font-size: 2.5rem;"></i>
      <p class="text-muted mt-2" style="font-size: 0.9rem;">Keranjang Anda masih kosong.</p>
    </div>
  {:else}
    <div class="cart-items-container mb-4">
      {#each $cart as item}
        <div class="cart-item py-3 border-bottom d-flex align-items-start justify-content-between">
          <div class="me-2 flex-grow-1">
            <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">{item.product.name}</h6>
            <div class="d-flex align-items-center text-muted" style="font-size: 0.8rem;">
              <span>{item.quantity} x Rp. {numberWithCommas(item.product.price)}</span>
            </div>
            {#if item.notes}
              <div class="notes-badge mt-1 d-inline-flex align-items-center text-secondary py-1 px-2 rounded">
                <i class="bi bi-chat-left-text me-1"></i>
                <span class="text-truncate" style="max-width: 140px;">{item.notes}</span>
              </div>
            {/if}
          </div>
          <div class="text-end d-flex flex-column align-items-end">
            <span class="fw-bold text-dark mb-2" style="font-size: 0.9rem;">
              Rp. {numberWithCommas(item.product.price * item.quantity)}
            </span>
            <div class="action-buttons gap-1 d-flex">
              <button 
                class="btn btn-outline-secondary btn-sm p-1 px-2 rounded-circle"
                on:click={() => openEditModal(item)}
                title="Edit item"
              >
                <i class="bi bi-pencil-fill" style="font-size: 0.75rem;"></i>
              </button>
              <button 
                class="btn btn-outline-danger btn-sm p-1 px-2 rounded-circle"
                on:click={() => handleDeleteCartItem(item.id)}
                title="Hapus item"
              >
                <i class="bi bi-trash-fill" style="font-size: 0.75rem;"></i>
              </button>
            </div>
          </div>
        </div>
      {/each}
    </div>

    <!-- Total Price Display -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <span class="fw-bold text-secondary">Total Harga:</span>
      <h5 class="fw-bold text-warning mb-0">Rp. {numberWithCommas(totalPrice)}</h5>
    </div>

    <!-- Proceed to Checkout Button -->
    <button 
      type="button" 
      class="btn btn-warning w-100 fw-bold text-white shadow btn-checkout py-2" 
      on:click={proceedToCheckout}
    >
      <i class="bi bi-credit-card-2-front me-2"></i>Lanjutkan ke Checkout
    </button>
  {/if}
</div>

<!-- Edit Item Modal (remains same) -->
{#if showEditModal && editingCartItem}
  <div class="modal-backdrop-custom" on:click={closeEditModal}></div>
  <div class="modal-card-custom shadow-lg">
    <div class="modal-header-custom d-flex justify-content-between align-items-center pb-2 border-bottom">
      <h5 class="fw-bold mb-0 text-dark">Ubah Item Keranjang</h5>
      <button class="btn btn-close-custom" on:click={closeEditModal}>
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    
    <div class="modal-body-custom py-3">
      <h5 class="fw-bold text-dark mb-1">{editingCartItem.product.name}</h5>
      <p class="text-muted small mb-3">Rp. {numberWithCommas(editingCartItem.product.price)} / porsi</p>
      
      <!-- Quantity Selector -->
      <div class="mb-3">
        <label class="form-label fw-bold" for="edit-qty-input">Jumlah</label>
        <div class="d-flex align-items-center">
          <button class="btn btn-outline-warning qty-btn shadow-sm" type="button" on:click={decrementQty}>
            <i class="bi bi-dash-lg"></i>
          </button>
          <input
            id="edit-qty-input"
            type="number"
            class="form-control text-center mx-2 qty-input border-warning"
            bind:value={editQuantity}
            min="1"
            readonly
          />
          <button class="btn btn-warning qty-btn shadow-sm text-white" type="button" on:click={incrementQty}>
            <i class="bi bi-plus-lg"></i>
          </button>
        </div>
      </div>
      
      <!-- Edit Notes -->
      <div class="mb-3">
        <label for="edit-notes" class="form-label fw-bold">Catatan Khusus</label>
        <textarea
          id="edit-notes"
          class="form-control border-warning"
          rows="2"
          placeholder="Contoh: Tanpa saus mayo, pedas sedang, dll."
          bind:value={editNotes}
        ></textarea>
      </div>

      <!-- Subtotal -->
      <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <span class="fw-bold text-secondary">Subtotal Baru:</span>
        <h4 class="text-warning fw-bold mb-0">
          Rp. {numberWithCommas(editingCartItem.product.price * editQuantity)}
        </h4>
      </div>
    </div>
    
    <div class="modal-footer-custom pt-3 d-flex gap-2">
      <button class="btn btn-outline-secondary w-50" on:click={closeEditModal}>Batal</button>
      <button class="btn btn-warning w-50 text-white fw-bold" on:click={handleUpdateCartItem} disabled={editSubmitting}>
        {#if editSubmitting}
          <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
          Menyimpan...
        {:else}
          <i class="bi bi-save me-1"></i>Simpan
        {/if}
      </button>
    </div>
  </div>
{/if}

<style>
  .cart-items-container {
    max-height: 380px;
    overflow-y: auto;
    padding-right: 5px;
  }
  
  .cart-items-container::-webkit-scrollbar {
    width: 4px;
  }
  .cart-items-container::-webkit-scrollbar-track {
    background: #f1f1f1;
  }
  .cart-items-container::-webkit-scrollbar-thumb {
    background: #ffc107;
    border-radius: 4px;
  }
  .cart-items-container::-webkit-scrollbar-thumb:hover {
    background: #e0a800;
  }

  .cart-item {
    transition: background-color 0.2s;
  }
  .cart-item:hover {
    background-color: #fafafa;
  }
  
  .notes-badge {
    background-color: #f1f2f6;
    font-size: 0.72rem;
  }

  .btn-login-nav {
    transition: all 0.3s ease;
  }
  .btn-login-nav:hover {
    background-color: #e0a800;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(224, 168, 0, 0.3);
  }

  .btn-checkout {
    background-color: #ff9f43;
    border: none;
    transition: all 0.3s ease;
    border-radius: 8px;
  }
  .btn-checkout:hover {
    background-color: #f39c12;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(243, 156, 18, 0.35);
  }

  /* Custom Modal Styling */
  .modal-backdrop-custom {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    z-index: 1040;
  }
  .modal-card-custom {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    max-width: 450px;
    background: white;
    border-radius: 16px;
    padding: 24px;
    z-index: 1050;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
    animation: zoomIn 0.3s ease forwards;
  }
  @keyframes zoomIn {
    from {
      opacity: 0;
      transform: translate(-50%, -45%) scale(0.95);
    }
    to {
      opacity: 1;
      transform: translate(-50%, -50%) scale(1);
    }
  }
  .btn-close-custom {
    background: none;
    border: none;
    color: #a0a0a0;
    font-size: 1.2rem;
    transition: color 0.2s;
  }
  .btn-close-custom:hover {
    color: #333;
  }
  .qty-btn {
    width: 42px;
    height: 42px;
    font-size: 1.1rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .qty-input {
    max-width: 70px;
    height: 42px;
    font-size: 1.1rem;
    font-weight: bold;
    border-radius: 8px;
  }
</style>
