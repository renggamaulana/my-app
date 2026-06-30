<script>
  import Cart from "../Components/Cart.svelte";
  import Category from "../Components/Category.svelte";
  import Navbar from "../Components/Navbar.svelte";
  import axios from "axios";
  import Swal from "sweetalert2";
  import { numberWithCommas } from "../utils/utils";
  import { selectedCategory, searchTerm, isLoggedIn, cart } from "../store";

  const API_URL = "http://localhost:8000/api";

  let menus = [];
  let loading = false;

  // Modal State
  let showModal = false;
  let selectedProduct = null;
  let modalQuantity = 1;
  let modalNotes = "";
  let modalSubmitting = false;

  // Reactively fetch products when category or search changes
  $: fetchProducts($selectedCategory, $searchTerm);

  async function fetchProducts(catId, search) {
    loading = true;
    try {
      const params = {};
      if (catId !== null) {
        params.category_id = catId;
      }
      if (search) {
        params.search = search;
      }
      const res = await axios.get(`${API_URL}/products`, { params });
      menus = res.data;
    } catch (err) {
      console.error("Error loading products:", err);
    } finally {
      loading = false;
    }
  }

  function handleProductClick(product) {
    if (!$isLoggedIn) {
      Swal.fire({
        icon: "warning",
        title: "Perlu Masuk",
        text: "Silakan masuk ke akun Anda terlebih dahulu untuk memesan.",
        confirmButtonColor: "#ff9f43",
        showCancelButton: true,
        confirmButtonText: "Masuk",
        cancelButtonText: "Batal",
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "/login";
        }
      });
      return;
    }

    if (!product.is_ready) {
      Swal.fire({
        icon: "error",
        title: "Habis",
        text: "Maaf, menu ini sedang tidak tersedia.",
        confirmButtonColor: "#dc3545",
      });
      return;
    }

    selectedProduct = product;
    modalQuantity = 1;
    modalNotes = "";
    showModal = true;
  }

  function closeModal() {
    showModal = false;
    selectedProduct = null;
  }

  function incrementQty() {
    modalQuantity += 1;
  }

  function decrementQty() {
    if (modalQuantity > 1) {
      modalQuantity -= 1;
    }
  }

  async function handleAddToCart() {
    if (!selectedProduct) return;
    
    modalSubmitting = true;
    try {
      const res = await axios.post(`${API_URL}/carts`, {
        product_id: selectedProduct.id,
        quantity: modalQuantity,
        notes: modalNotes,
      });

      // Fetch fresh cart items to update global badge and sidebar
      const cartRes = await axios.get(`${API_URL}/carts`);
      cart.set(cartRes.data);

      closeModal();

      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: `${res.data.product.name} dimasukkan ke keranjang`,
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      });

    } catch (err) {
      console.error("Error adding to cart:", err);
      Swal.fire({
        icon: "error",
        title: "Gagal",
        text: "Terjadi kesalahan saat menambahkan item ke keranjang.",
        confirmButtonColor: "#dc3545",
      });
    } finally {
      modalSubmitting = false;
    }
  }
</script>

<!-- Navbar -->
<Navbar />

<!-- Main Page -->
<div class="container-fluid py-4">
  <div class="row">
    <!-- Left Sidebar: Category -->
    <div class="col-md-2 mb-4">
      <Category />
    </div>

    <!-- Center Content: Product Grid -->
    <div class="col-md-7 mb-4">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-secondary">Daftar Menu</h4>
        {#if $searchTerm}
          <span class="badge bg-light text-dark py-2 px-3 shadow-sm border">
            Hasil pencarian: "{$searchTerm}"
          </span>
        {/if}
      </div>
      <hr />

      {#if loading}
        <div class="d-flex justify-content-center my-5 py-5">
          <div class="spinner-border text-warning" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      {:else if menus.length === 0}
        <div class="text-center my-5 py-5">
          <i class="bi bi-egg-fried text-muted" style="font-size: 3rem;"></i>
          <h5 class="text-muted mt-3">Tidak ada menu yang cocok.</h5>
        </div>
      {:else}
        <div class="row">
          {#each menus as menu}
            <div class="col-md-4 col-sm-6 col-xs-12 my-3">
              <div 
                class="card menu-card h-100 shadow-sm border-0 position-relative {!menu.is_ready ? 'disabled-card' : ''}" 
                on:click={() => handleProductClick(menu)}
              >
                {#if !menu.is_ready}
                  <span class="badge bg-danger position-absolute top-0 end-0 m-2 z-index-2 px-3 py-2 rounded-pill">
                    Habis
                  </span>
                {/if}
                <div class="image-container bg-light">
                  <img
                    src="./assets/img/{menu.category.name.toLowerCase()}/{menu.img}"
                    class="card-img-top menu-img"
                    alt={menu.name}
                    on:error={(e) => {
                      e.target.src = 'https://placehold.co/400x300?text=Corndog+Alle';
                    }}
                  />
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                  <div>
                    <h6 class="card-title fw-bold mb-1 text-dark">{menu.name}</h6>
                    <span class="badge bg-secondary mb-2" style="font-size: 0.75rem;">
                      {menu.category.name}
                    </span>
                  </div>
                  <div class="d-flex justify-content-between align-items-center mt-3">
                    <p class="card-text text-warning fw-bold mb-0">
                      Rp. {numberWithCommas(menu.price)}
                    </p>
                    <button class="btn btn-warning btn-sm rounded-circle px-2 py-1 add-btn" disabled={!menu.is_ready}>
                      <i class="bi bi-plus-lg text-white"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          {/each}
        </div>
      {/if}
    </div>

    <!-- Right Sidebar: Cart -->
    <div class="col-md-3 mb-4">
      <Cart />
    </div>
  </div>
</div>

<!-- Custom Product Configuration Modal -->
{#if showModal && selectedProduct}
  <div class="modal-backdrop-custom" on:click={closeModal}></div>
  <div class="modal-card-custom shadow-lg">
    <div class="modal-header-custom d-flex justify-content-between align-items-center pb-2 border-bottom">
      <h5 class="fw-bold mb-0 text-dark">Detail Pesanan</h5>
      <button class="btn btn-close-custom" on:click={closeModal}>
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    
    <div class="modal-body-custom py-3">
      <div class="row align-items-center">
        <div class="col-sm-5 text-center mb-3 mb-sm-0">
          <img
            src="./assets/img/{selectedProduct.category.name.toLowerCase()}/{selectedProduct.img}"
            class="img-fluid rounded modal-img shadow-sm"
            alt={selectedProduct.name}
            on:error={(e) => {
              e.target.src = 'https://placehold.co/400x300?text=Corndog+Alle';
            }}
          />
        </div>
        <div class="col-sm-7">
          <h4 class="fw-bold mb-1 text-dark">{selectedProduct.name}</h4>
          <p class="text-muted mb-2">{selectedProduct.category.name}</p>
          <h5 class="text-warning fw-bold">Rp. {numberWithCommas(selectedProduct.price)}</h5>
        </div>
      </div>
      
      <hr class="my-3" />
      
      <!-- Quantity Selector -->
      <div class="mb-3">
        <label class="form-label fw-bold" for="modal-qty-input">Jumlah</label>
        <div class="d-flex align-items-center">
          <button class="btn btn-outline-warning qty-btn shadow-sm" type="button" on:click={decrementQty}>
            <i class="bi bi-dash-lg"></i>
          </button>
          <input
            id="modal-qty-input"
            type="number"
            class="form-control text-center mx-2 qty-input border-warning"
            bind:value={modalQuantity}
            min="1"
            readonly
          />
          <button class="btn btn-warning qty-btn shadow-sm text-white" type="button" on:click={incrementQty}>
            <i class="bi bi-plus-lg"></i>
          </button>
        </div>
      </div>
      
      <!-- Custom Notes -->
      <div class="mb-3">
        <label for="order-notes" class="form-label fw-bold">Catatan Khusus (Opsional)</label>
        <textarea
          id="order-notes"
          class="form-control border-warning"
          rows="2"
          placeholder="Contoh: Tanpa saus mayo, pedas sedang, dll."
          bind:value={modalNotes}
        ></textarea>
      </div>

      <!-- Reactive Subtotal -->
      <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
        <span class="fw-bold text-secondary">Subtotal:</span>
        <h4 class="text-warning fw-bold mb-0">
          Rp. {numberWithCommas(selectedProduct.price * modalQuantity)}
        </h4>
      </div>
    </div>
    
    <div class="modal-footer-custom pt-3 d-flex gap-2">
      <button class="btn btn-outline-secondary w-50" on:click={closeModal}>Batal</button>
      <button class="btn btn-warning w-50 text-white fw-bold" on:click={handleAddToCart} disabled={modalSubmitting}>
        {#if modalSubmitting}
          <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
          Menyimpan...
        {:else}
          <i class="bi bi-cart-plus me-1"></i>Masukkan
        {/if}
      </button>
    </div>
  </div>
{/if}

<style>
  .menu-card {
    transition: all 0.3s ease;
    cursor: pointer;
    border-radius: 12px;
    overflow: hidden;
  }
  .menu-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
  }
  .image-container {
    height: 180px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .menu-img {
    height: 100%;
    width: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
  }
  .menu-card:hover .menu-img {
    transform: scale(1.08);
  }
  .add-btn {
    transition: all 0.2s ease;
  }
  .menu-card:hover .add-btn {
    transform: scale(1.1);
  }
  .disabled-card {
    opacity: 0.65;
    cursor: not-allowed;
  }
  .disabled-card:hover {
    transform: none;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05) !important;
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
    max-width: 500px;
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
  .modal-img {
    max-height: 150px;
    object-fit: cover;
    width: 100%;
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
