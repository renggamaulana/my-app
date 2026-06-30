<script>
  import { onMount } from "svelte";
  import axios from "axios";
  import page from "page";
  import Swal from "sweetalert2";
  import { user, isLoggedIn, token } from "../store";
  import { numberWithCommas } from "../utils/utils";

  const API_URL = "http://localhost:8000/api";

  // Active View Tab State
  let activeTab = "dashboard"; // dashboard, customers, products, categories, orders, invoice, shop-settings, bank-accounts
  
  // Sidebar open state
  let sidebarCollapsed = false;

  // Stats Data
  let stats = { new_orders: 0, revenue: 0, users: 0 };

  // Data Lists
  let customersList = [];
  let productsList = [];
  let categoriesList = [];
  let ordersList = [];
  let bankAccountsList = [];
  let shopSettings = { name: "", address: "", phone: "", email: "" };

  // Eager-loaded data for Invoice
  let selectedInvoice = null;

  // CRUD Forms States
  // Category Form
  let catFormName = "";
  let editingCategoryId = null;
  
  // Product Form
  let prodFormName = "";
  let prodFormPrice = 0;
  let prodFormIsReady = true;
  let prodFormImg = "";
  let prodFormCatId = "";
  let editingProductId = null;

  // Bank Account Form
  let bankFormName = "";
  let bankFormHolder = "";
  let bankFormNumber = "";
  let editingBankAccountId = null;

  // Order Status Edit Form
  let showStatusModal = false;
  let editingOrderId = null;
  let editOrderStatus = "Perlu di Cek";
  let editOrderPayment = "unpaid";

  // Loading States
  let loading = false;
  let checkingAuth = true;

  onMount(() => {
    const localToken = localStorage.getItem("token");
    if (!localToken) {
      checkingAuth = false;
      triggerAccessDenied();
      return;
    }

    // Subscribe to user store to wait for auth check
    const unsubscribe = user.subscribe(($user) => {
      if ($user) {
        checkingAuth = false;
        if ($user.role !== "admin") {
          triggerAccessDenied();
        } else {
          loadDashboardData();
        }
        unsubscribe();
      }
    });

    // Fallback safeguard to prevent infinite loader if token is invalid or request fails
    setTimeout(() => {
      if (checkingAuth) {
        checkingAuth = false;
        triggerAccessDenied();
        unsubscribe();
      }
    }, 2500);
  });

  function triggerAccessDenied() {
    Swal.fire({
      icon: "error",
      title: "Akses Ditolak",
      text: "Anda tidak memiliki otorisasi untuk mengakses Dashboard Admin.",
      confirmButtonColor: "#dc3545",
    });
    page("/");
  }

  // Watch tab changes to load data dynamically
  $: {
    if (activeTab === "dashboard") loadStats();
    if (activeTab === "customers") loadCustomers();
    if (activeTab === "products") { loadProducts(); loadCategories(); }
    if (activeTab === "categories") loadCategories();
    if (activeTab === "orders") loadOrders();
    if (activeTab === "shop-settings") loadShopSettings();
    if (activeTab === "bank-accounts") loadBankAccounts();
  }

  // API Call Helpers
  async function loadDashboardData() {
    await loadStats();
  }

  async function loadStats() {
    try {
      const res = await axios.get(`${API_URL}/admin/stats`);
      stats = res.data;
    } catch (err) {
      console.error("Error loading stats:", err);
    }
  }

  async function loadCustomers() {
    loading = true;
    try {
      const res = await axios.get(`${API_URL}/admin/customers`);
      customersList = res.data;
    } catch (err) {
      console.error(err);
    } finally {
      loading = false;
    }
  }

  async function loadProducts() {
    loading = true;
    try {
      const res = await axios.get(`${API_URL}/products`);
      productsList = res.data;
    } catch (err) {
      console.error(err);
    } finally {
      loading = false;
    }
  }

  async function loadCategories() {
    loading = true;
    try {
      const res = await axios.get(`${API_URL}/categories`);
      categoriesList = res.data;
    } catch (err) {
      console.error(err);
    } finally {
      loading = false;
    }
  }

  async function loadOrders() {
    loading = true;
    try {
      const res = await axios.get(`${API_URL}/admin/orders`);
      ordersList = res.data;
    } catch (err) {
      console.error(err);
    } finally {
      loading = false;
    }
  }

  async function loadShopSettings() {
    try {
      const res = await axios.get(`${API_URL}/shop-settings`);
      shopSettings = res.data;
    } catch (err) {
      console.error(err);
    }
  }

  async function loadBankAccounts() {
    loading = true;
    try {
      const res = await axios.get(`${API_URL}/bank-accounts`);
      bankAccountsList = res.data;
    } catch (err) {
      console.error(err);
    } finally {
      loading = false;
    }
  }

  // --- CRUD Category Actions ---
  function editCategory(cat) {
    editingCategoryId = cat.id;
    catFormName = cat.name;
  }

  async function handleSaveCategory() {
    if (!catFormName) return;
    try {
      if (editingCategoryId) {
        await axios.put(`${API_URL}/categories/${editingCategoryId}`, { name: catFormName });
        Swal.fire("Berhasil", "Kategori diperbarui.", "success");
      } else {
        await axios.post(`${API_URL}/categories`, { name: catFormName });
        Swal.fire("Berhasil", "Kategori ditambahkan.", "success");
      }
      catFormName = "";
      editingCategoryId = null;
      loadCategories();
    } catch (err) {
      Swal.fire("Gagal", err.response?.data?.message || "Terjadi kesalahan.", "error");
    }
  }

  async function deleteCategory(id) {
    Swal.fire({
      title: "Hapus Kategori?",
      text: "Semua produk dalam kategori ini juga akan dihapus!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
      confirmButtonText: "Hapus",
    }).then(async (res) => {
      if (res.isConfirmed) {
        try {
          await axios.delete(`${API_URL}/categories/${id}`);
          Swal.fire("Terhapus", "Kategori berhasil dihapus.", "success");
          loadCategories();
        } catch (err) {
          console.error(err);
        }
      }
    });
  }

  // --- CRUD Product Actions ---
  function editProduct(prod) {
    editingProductId = prod.id;
    prodFormName = prod.name;
    prodFormPrice = prod.price;
    prodFormIsReady = prod.is_ready;
    prodFormImg = prod.img;
    prodFormCatId = prod.category_id;
  }

  async function handleSaveProduct() {
    if (!prodFormName || !prodFormPrice || !prodFormImg || !prodFormCatId) {
      Swal.fire("Gagal", "Semua kolom input wajib diisi.", "warning");
      return;
    }
    
    try {
      const payload = {
        name: prodFormName,
        price: prodFormPrice,
        is_ready: prodFormIsReady,
        img: prodFormImg,
        category_id: prodFormCatId
      };

      if (editingProductId) {
        await axios.put(`${API_URL}/products/${editingProductId}`, payload);
        Swal.fire("Berhasil", "Produk diperbarui.", "success");
      } else {
        await axios.post(`${API_URL}/products`, payload);
        Swal.fire("Berhasil", "Produk ditambahkan.", "success");
      }

      // Reset
      prodFormName = "";
      prodFormPrice = 0;
      prodFormIsReady = true;
      prodFormImg = "";
      prodFormCatId = "";
      editingProductId = null;
      loadProducts();
    } catch (err) {
      Swal.fire("Gagal", err.response?.data?.message || "Terjadi kesalahan.", "error");
    }
  }

  async function deleteProduct(id) {
    Swal.fire({
      title: "Hapus Produk?",
      text: "Tindakan ini tidak dapat dibatalkan.",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
      confirmButtonText: "Hapus",
    }).then(async (res) => {
      if (res.isConfirmed) {
        try {
          await axios.delete(`${API_URL}/products/${id}`);
          Swal.fire("Terhapus", "Produk berhasil dihapus.", "success");
          loadProducts();
        } catch (err) {
          console.error(err);
        }
      }
    });
  }

  // --- CRUD Bank Account Actions ---
  function editBankAccount(bank) {
    editingBankAccountId = bank.id;
    bankFormName = bank.bank_name;
    bankFormHolder = bank.account_name;
    bankFormNumber = bank.account_number;
  }

  async function handleSaveBankAccount() {
    if (!bankFormName || !bankFormHolder || !bankFormNumber) return;
    try {
      const payload = {
        bank_name: bankFormName,
        account_name: bankFormHolder,
        account_number: bankFormNumber
      };

      if (editingBankAccountId) {
        await axios.put(`${API_URL}/bank-accounts/${editingBankAccountId}`, payload);
        Swal.fire("Berhasil", "Rekening diperbarui.", "success");
      } else {
        await axios.post(`${API_URL}/bank-accounts`, payload);
        Swal.fire("Berhasil", "Rekening ditambahkan.", "success");
      }

      bankFormName = "";
      bankFormHolder = "";
      bankFormNumber = "";
      editingBankAccountId = null;
      loadBankAccounts();
    } catch (err) {
      Swal.fire("Gagal", err.response?.data?.message || "Terjadi kesalahan.", "error");
    }
  }

  async function deleteBankAccount(id) {
    Swal.fire({
      title: "Hapus Rekening?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
      confirmButtonText: "Hapus",
    }).then(async (res) => {
      if (res.isConfirmed) {
        try {
          await axios.delete(`${API_URL}/bank-accounts/${id}`);
          Swal.fire("Terhapus", "Rekening berhasil dihapus.", "success");
          loadBankAccounts();
        } catch (err) {
          console.error(err);
        }
      }
    });
  }

  // --- Shop Settings Save ---
  async function handleSaveShopSettings() {
    try {
      await axios.put(`${API_URL}/shop-settings`, shopSettings);
      Swal.fire("Berhasil", "Alamat Toko berhasil disimpan.", "success");
      loadShopSettings();
    } catch (err) {
      Swal.fire("Gagal", "Terjadi kesalahan.", "error");
    }
  }

  // --- Order Management Actions ---
  function openStatusModal(order) {
    editingOrderId = order.id;
    editOrderStatus = order.status;
    editOrderPayment = order.payment_status;
    showStatusModal = true;
  }

  async function handleSaveOrderStatus() {
    try {
      await axios.put(`${API_URL}/admin/orders/${editingOrderId}/status`, {
        status: editOrderStatus,
        payment_status: editOrderPayment
      });
      showStatusModal = false;
      editingOrderId = null;
      Swal.fire("Berhasil", "Status pesanan diperbarui.", "success");
      loadOrders();
    } catch (err) {
      Swal.fire("Gagal", "Terjadi kesalahan saat memperbarui status.", "error");
    }
  }

  async function deleteOrder(id) {
    Swal.fire({
      title: "Hapus Pesanan?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#dc3545",
      confirmButtonText: "Hapus",
    }).then(async (res) => {
      if (res.isConfirmed) {
        try {
          await axios.delete(`${API_URL}/admin/orders/${id}`);
          Swal.fire("Terhapus", "Pesanan berhasil dihapus.", "success");
          loadOrders();
        } catch (err) {
          console.error(err);
        }
      }
    });
  }

  // Redirect to invoice page
  function showInvoice(order) {
    selectedInvoice = order;
    activeTab = "invoice";
  }

  async function submitPayment() {
    if (!selectedInvoice) return;
    try {
      await axios.put(`${API_URL}/admin/orders/${selectedInvoice.id}/status`, {
        status: "Perlu dikirim",
        payment_status: "paid"
      });
      
      // Update local object
      selectedInvoice.status = "Perlu dikirim";
      selectedInvoice.payment_status = "paid";
      
      Swal.fire("Berhasil", "Pembayaran disubmit. Status berubah menjadi 'Perlu dikirim'.", "success");
    } catch (err) {
      Swal.fire("Gagal", "Terjadi kesalahan.", "error");
    }
  }

  function triggerPrint() {
    window.print();
  }

  async function handleLogout() {
    try {
      await axios.post(`${API_URL}/logout`);
    } catch (err) {
      console.error(err);
    } finally {
      token.set(null);
      user.set(null);
      localStorage.removeItem("token");
      Swal.fire("Keluar", "Sesi admin telah berakhir.", "info");
      page("/login");
    }
  }
</script>

{#if checkingAuth}
  <div class="d-flex align-items-center justify-content-center min-vh-100 bg-light no-print">
    <div class="text-center">
      <div class="spinner-border text-warning" role="status" style="width: 3rem; height: 3rem;"></div>
      <h5 class="text-muted mt-3 fw-bold">Memverifikasi Sesi Admin...</h5>
    </div>
  </div>
{:else}
<div class="admin-wrapper d-flex {sidebarCollapsed ? 'sidebar-hidden' : ''}">
  <!-- Sidebar Panel -->
  <aside class="admin-sidebar bg-dark text-white d-flex flex-column">
    <div class="sidebar-header p-3 border-bottom d-flex align-items-center">
      <img src="https://placehold.co/40x40?text=A" class="rounded-circle me-3 border border-secondary" alt="Admin" />
      <div>
        <h6 class="fw-bold mb-0">Admin</h6>
        <small class="text-success"><i class="bi bi-circle-fill me-1" style="font-size: 0.6rem;"></i>Online</small>
      </div>
    </div>

    <!-- Navigation Menu -->
    <ul class="nav flex-column sidebar-nav p-2 flex-grow-1">
      <li class="nav-item mb-1">
        <button
          class="nav-link w-100 text-start text-white border-0 bg-transparent rounded d-flex align-items-center justify-content-between {activeTab === 'dashboard' ? 'active bg-primary' : ''}"
          on:click={() => activeTab = "dashboard"}
        >
          <span><i class="bi bi-speedometer2 me-2"></i>Dashboard</span>
          <i class="bi bi-chevron-left arrow-icon"></i>
        </button>
      </li>
      
      <li class="nav-item mb-1">
        <button
          class="nav-link w-100 text-start text-white border-0 bg-transparent rounded d-flex align-items-center justify-content-between {activeTab === 'customers' ? 'active bg-primary' : ''}"
          on:click={() => activeTab = "customers"}
        >
          <span><i class="bi bi-people me-2"></i>Pelanggan</span>
          <i class="bi bi-chevron-left arrow-icon"></i>
        </button>
      </li>

      <!-- Data Master dropdown toggle -->
      <li class="nav-item mb-1">
        <span class="nav-header text-muted text-uppercase px-3 py-2 d-block" style="font-size: 0.75rem;">Data Master</span>
        <button
          class="nav-link w-100 text-start text-light border-0 bg-transparent rounded d-flex align-items-center justify-content-between ps-4 {activeTab === 'products' ? 'active bg-secondary text-white' : ''}"
          on:click={() => activeTab = "products"}
        >
          <span><i class="bi bi-box-seam me-2"></i>Produk</span>
        </button>
        <button
          class="nav-link w-100 text-start text-light border-0 bg-transparent rounded d-flex align-items-center justify-content-between ps-4 mt-1 {activeTab === 'categories' ? 'active bg-secondary text-white' : ''}"
          on:click={() => activeTab = "categories"}
        >
          <span><i class="bi bi-tags me-2"></i>Kategori</span>
        </button>
      </li>

      <!-- Transaksi dropdown toggle -->
      <li class="nav-item mb-1">
        <span class="nav-header text-muted text-uppercase px-3 py-2 d-block" style="font-size: 0.75rem;">Transaksi</span>
        <button
          class="nav-link w-100 text-start text-light border-0 bg-transparent rounded d-flex align-items-center justify-content-between ps-4 {activeTab === 'orders' ? 'active bg-secondary text-white' : ''}"
          on:click={() => activeTab = "orders"}
        >
          <span><i class="bi bi-cart-check me-2"></i>Pesanan Baru</span>
        </button>
      </li>

      <!-- Lainnya dropdown toggle -->
      <li class="nav-item mb-1">
        <span class="nav-header text-muted text-uppercase px-3 py-2 d-block" style="font-size: 0.75rem;">Lainnya</span>
        <button
          class="nav-link w-100 text-start text-light border-0 bg-transparent rounded d-flex align-items-center justify-content-between ps-4 {activeTab === 'shop-settings' ? 'active bg-secondary text-white' : ''}"
          on:click={() => activeTab = "shop-settings"}
        >
          <span><i class="bi bi-geo-alt me-2"></i>Alamat Toko</span>
        </button>
        <button
          class="nav-link w-100 text-start text-light border-0 bg-transparent rounded d-flex align-items-center justify-content-between ps-4 mt-1 {activeTab === 'bank-accounts' ? 'active bg-secondary text-white' : ''}"
          on:click={() => activeTab = "bank-accounts"}
        >
          <span><i class="bi bi-credit-card me-2"></i>Data Rekening</span>
        </button>
      </li>
    </ul>
    
    <div class="p-3 border-top mt-auto">
      <button class="btn btn-outline-danger w-100 fw-bold" on:click={handleLogout}>
        <i class="bi bi-box-arrow-right me-2"></i>Keluar
      </button>
    </div>
  </aside>

  <!-- Main Display Screen -->
  <main class="admin-main flex-grow-1 bg-light d-flex flex-column">
    <!-- Admin Top Navbar -->
    <header class="admin-navbar navbar navbar-expand navbar-light bg-white border-bottom px-3 shadow-sm py-2 no-print">
      <button class="btn btn-light border me-3" on:click={() => sidebarCollapsed = !sidebarCollapsed}>
        <i class="bi bi-list fs-5"></i>
      </button>
      
      <div class="ms-auto d-flex align-items-center">
        <a href="/" class="btn btn-link text-secondary me-3 text-decoration-none">
          <i class="bi bi-house me-1"></i>Halaman Utama
        </a>
        <span class="text-secondary fw-semibold">Dashboard Admin</span>
      </div>
    </header>

    <!-- Main Content Panel -->
    <div class="content-wrapper p-4 flex-grow-1">
      {#if activeTab === "dashboard"}
        <!-- 1. DASHBOARD VIEW -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="fw-bold mb-0 text-dark">Dashboard</h2>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="/admin">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </nav>
        </div>

        <div class="row">
          <!-- Card 1: New Orders -->
          <div class="col-lg-4 col-sm-6 mb-4">
            <div class="small-box bg-info text-white p-3 rounded shadow-sm d-flex flex-column justify-content-between position-relative overflow-hidden h-100">
              <div class="inner">
                <h1 class="fw-bold display-4 mb-0">{stats.new_orders}</h1>
                <p class="fs-6">Pesanan Baru</p>
              </div>
              <div class="icon position-absolute top-50 end-0 translate-middle-y me-3 opacity-25">
                <i class="bi bi-bag-fill text-white" style="font-size: 5rem;"></i>
              </div>
              <button class="btn btn-link text-white text-decoration-none text-start p-0 mt-3 d-flex align-items-center" on:click={() => activeTab = "orders"}>
                More info <i class="bi bi-arrow-right-circle-fill ms-2"></i>
              </button>
            </div>
          </div>

          <!-- Card 2: Revenue -->
          <div class="col-lg-4 col-sm-6 mb-4">
            <div class="small-box bg-success text-white p-3 rounded shadow-sm d-flex flex-column justify-content-between position-relative overflow-hidden h-100">
              <div class="inner">
                <h1 class="fw-bold display-5 mb-0" style="font-size: 2.2rem;">Rp. {numberWithCommas(stats.revenue)}</h1>
                <p class="fs-6 mt-2">Pendapatan</p>
              </div>
              <div class="icon position-absolute top-50 end-0 translate-middle-y me-3 opacity-25">
                <i class="bi bi-graph-up text-white" style="font-size: 5rem;"></i>
              </div>
              <button class="btn btn-link text-white text-decoration-none text-start p-0 mt-3 d-flex align-items-center" on:click={() => activeTab = "orders"}>
                More info <i class="bi bi-arrow-right-circle-fill ms-2"></i>
              </button>
            </div>
          </div>

          <!-- Card 3: Users Count -->
          <div class="col-lg-4 col-sm-6 mb-4">
            <div class="small-box bg-warning text-white p-3 rounded shadow-sm d-flex flex-column justify-content-between position-relative overflow-hidden h-100">
              <div class="inner">
                <h1 class="fw-bold display-4 mb-0 text-dark">{stats.users}</h1>
                <p class="fs-6 text-dark">Data User</p>
              </div>
              <div class="icon position-absolute top-50 end-0 translate-middle-y me-3 opacity-25">
                <i class="bi bi-person-plus-fill text-dark" style="font-size: 5rem;"></i>
              </div>
              <button class="btn btn-link text-dark text-decoration-none text-start p-0 mt-3 d-flex align-items-center" on:click={() => activeTab = "customers"}>
                More info <i class="bi bi-arrow-right-circle-fill ms-2"></i>
              </button>
            </div>
          </div>
        </div>

      {:else if activeTab === "customers"}
        <!-- 2. DATA PELANGGAN VIEW -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="fw-bold mb-0 text-dark">Data Pelanggan</h2>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="/admin">Home</a></li>
              <li class="breadcrumb-item active">DataTables</li>
            </ol>
          </nav>
        </div>

        <div class="card border-0 shadow-sm p-4">
          <h5 class="fw-semibold text-secondary mb-3">Daftar Pelanggan Terdaftar</h5>
          <div class="table-responsive">
            <table class="table table-hover border">
              <thead class="table-light">
                <tr>
                  <th scope="col" style="width: 80px;">No</th>
                  <th scope="col">Nama Pelanggan</th>
                  <th scope="col">Email</th>
                  <th scope="col">Alamat</th>
                </tr>
              </thead>
              <tbody>
                {#if loading}
                  <tr>
                    <td colspan="4" class="text-center py-4">
                      <div class="spinner-border text-primary spinner-border-sm" role="status"></div> Loading...
                    </td>
                  </tr>
                {:else if customersList.length === 0}
                  <tr>
                    <td colspan="4" class="text-center py-4 text-muted">Tidak ada data pelanggan.</td>
                  </tr>
                {:else}
                  {#each customersList as cust}
                    <tr>
                      <th scope="row">{cust.no}</th>
                      <td class="fw-semibold">{cust.name}</td>
                      <td>{cust.email}</td>
                      <td>{cust.address}</td>
                    </tr>
                  {/each}
                {/if}
              </tbody>
            </table>
          </div>
        </div>

      {:else if activeTab === "products"}
        <!-- 3. DATA MASTER - PRODUK VIEW -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="fw-bold mb-0 text-dark">Data Master - Produk</h2>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="/admin">Home</a></li>
              <li class="breadcrumb-item active">Produk</li>
            </ol>
          </nav>
        </div>

        <div class="row">
          <!-- Left Column: Products List -->
          <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100">
              <h5 class="fw-semibold text-secondary mb-3">Daftar Menu Makanan & Minuman</h5>
              <div class="table-responsive">
                <table class="table table-hover border">
                  <thead class="table-light">
                    <tr>
                      <th scope="col" style="width: 50px;">No</th>
                      <th scope="col">Nama</th>
                      <th scope="col">Harga</th>
                      <th scope="col">Gambar</th>
                      <th scope="col" style="width: 150px;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {#if loading}
                      <tr>
                        <td colspan="5" class="text-center py-4">
                          <div class="spinner-border text-primary spinner-border-sm" role="status"></div> Loading...
                        </td>
                      </tr>
                    {:else if productsList.length === 0}
                      <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada produk.</td>
                      </tr>
                    {:else}
                      {#each productsList as prod, idx}
                        <tr>
                          <th scope="row">{idx + 1}</th>
                          <td class="fw-semibold">{prod.name}</td>
                          <td>Rp. {numberWithCommas(prod.price)}</td>
                          <td>
                            <img
                              src="./assets/img/{prod.category.name.toLowerCase()}/{prod.img}"
                              class="rounded border"
                              style="width: 45px; height: 45px; object-fit: cover;"
                              alt={prod.name}
                              on:error={(e) => e.target.src = 'https://placehold.co/40x40'}
                            />
                          </td>
                          <td>
                            <div class="d-flex gap-1">
                              <button class="btn btn-success btn-sm" on:click={() => editProduct(prod)}>Edit</button>
                              <button class="btn btn-danger btn-sm" on:click={() => deleteProduct(prod.id)}>Hapus</button>
                            </div>
                          </td>
                        </tr>
                      {/each}
                    {/if}
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Right Column: Add/Edit Form -->
          <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 24px;">
              <h5 class="fw-bold mb-3 text-dark">{editingProductId ? "Ubah Produk" : "Tambah Produk Baru"}</h5>
              <hr />

              <div class="mb-3">
                <label for="prod-name" class="form-label fw-semibold">Nama Produk</label>
                <input type="text" id="prod-name" class="form-control border-warning" bind:value={prodFormName} />
              </div>

              <div class="mb-3">
                <label for="prod-price" class="form-label fw-semibold">Harga</label>
                <input type="number" id="prod-price" class="form-control border-warning" bind:value={prodFormPrice} />
              </div>

              <div class="mb-3">
                <label for="prod-img" class="form-label fw-semibold">Filename Gambar (e.g. moza.jpg)</label>
                <input type="text" id="prod-img" class="form-control border-warning" bind:value={prodFormImg} />
              </div>

              <div class="mb-3">
                <label for="prod-cat" class="form-label fw-semibold">Kategori</label>
                <select id="prod-cat" class="form-select border-warning" bind:value={prodFormCatId}>
                  <option value="">Pilih Kategori...</option>
                  {#each categoriesList as cat}
                    <option value={cat.id}>{cat.name}</option>
                  {/each}
                </select>
              </div>

              <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input" id="prod-ready" bind:checked={prodFormIsReady} />
                <label class="form-check-label fw-semibold" for="prod-ready">Tersedia / Ready</label>
              </div>

              <button class="btn btn-warning w-100 text-white fw-bold py-2 shadow-sm" on:click={handleSaveProduct}>
                <i class="bi bi-save me-1"></i>Simpan Produk
              </button>
              {#if editingProductId}
                <button class="btn btn-outline-secondary w-100 mt-2 btn-sm" on:click={() => {
                  editingProductId = null;
                  prodFormName = "";
                  prodFormPrice = 0;
                  prodFormImg = "";
                  prodFormCatId = "";
                }}>Batal Edit</button>
              {/if}
            </div>
          </div>
        </div>

      {:else if activeTab === "categories"}
        <!-- 4. DATA MASTER - KATEGORI VIEW -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="fw-bold mb-0 text-dark">Data Master - Kategori</h2>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="/admin">Home</a></li>
              <li class="breadcrumb-item active">Kategori</li>
            </ol>
          </nav>
        </div>

        <div class="row">
          <!-- Left Column: Categories List -->
          <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100">
              <h5 class="fw-semibold text-secondary mb-3">Daftar Kategori Menu</h5>
              <div class="table-responsive">
                <table class="table table-hover border">
                  <thead class="table-light">
                    <tr>
                      <th scope="col" style="width: 80px;">No</th>
                      <th scope="col">Nama Kategori</th>
                      <th scope="col" style="width: 150px;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {#if loading}
                      <tr>
                        <td colspan="3" class="text-center py-4">
                          <div class="spinner-border text-primary spinner-border-sm" role="status"></div> Loading...
                        </td>
                      </tr>
                    {:else if categoriesList.length === 0}
                      <tr>
                        <td colspan="3" class="text-center py-4 text-muted">Belum ada kategori.</td>
                      </tr>
                    {:else}
                      {#each categoriesList as cat, idx}
                        <tr>
                          <th scope="row">{idx + 1}</th>
                          <td class="fw-semibold">{cat.name}</td>
                          <td>
                            <div class="d-flex gap-1">
                              <button class="btn btn-success btn-sm" on:click={() => editCategory(cat)}>Edit</button>
                              <button class="btn btn-danger btn-sm" on:click={() => deleteCategory(cat.id)}>Hapus</button>
                            </div>
                          </td>
                        </tr>
                      {/each}
                    {/if}
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Right Column: Add/Edit Form -->
          <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 24px;">
              <h5 class="fw-bold mb-3 text-dark">{editingCategoryId ? "Ubah Kategori" : "Tambah Kategori Baru"}</h5>
              <hr />

              <div class="mb-4">
                <label for="cat-name" class="form-label fw-semibold">Nama Kategori</label>
                <input type="text" id="cat-name" class="form-control border-warning" bind:value={catFormName} placeholder="Contoh: Food" />
              </div>

              <button class="btn btn-warning w-100 text-white fw-bold py-2 shadow-sm" on:click={handleSaveCategory}>
                <i class="bi bi-save me-1"></i>Simpan Kategori
              </button>
              {#if editingCategoryId}
                <button class="btn btn-outline-secondary w-100 mt-2 btn-sm" on:click={() => {
                  editingCategoryId = null;
                  catFormName = "";
                }}>Batal Edit</button>
              {/if}
            </div>
          </div>
        </div>

      {:else if activeTab === "orders"}
        <!-- 5. TRANSAKSI (PESANAN BARU) VIEW -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="fw-bold mb-0 text-dark">Pesanan Baru</h2>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="/admin">Home</a></li>
              <li class="breadcrumb-item active">Transaksi</li>
            </ol>
          </nav>
        </div>

        <div class="card border-0 shadow-sm p-4">
          <h5 class="fw-semibold text-secondary mb-3">Daftar Pesanan Baru</h5>
          <div class="table-responsive">
            <table class="table table-hover border align-middle">
              <thead class="table-light">
                <tr>
                  <th scope="col" style="width: 50px;">No</th>
                  <th scope="col">No Invoice</th>
                  <th scope="col">Pemesan</th>
                  <th scope="col">Subtotal</th>
                  <th scope="col">Metode Pembayaran</th>
                  <th scope="col">Status Pesanan</th>
                  <th scope="col" style="width: 220px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                {#if loading}
                  <tr>
                    <td colspan="7" class="text-center py-4">
                      <div class="spinner-border text-primary spinner-border-sm" role="status"></div> Loading...
                    </td>
                  </tr>
                {:else if ordersList.length === 0}
                  <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi.</td>
                  </tr>
                {:else}
                  {#each ordersList as order, idx}
                    <tr>
                      <th scope="row">{idx + 1}</th>
                      <td>
                        <button class="btn btn-link text-decoration-none p-0 fw-semibold text-primary" on:click={() => showInvoice(order)}>
                          {order.invoice_number}
                        </button>
                      </td>
                      <td class="fw-semibold">{order.customer_name}</td>
                      <td>Rp. {numberWithCommas(order.total_price)}</td>
                      <td>{order.payment_method}</td>
                      <td>
                        <span class="badge {order.status === 'Selesai' ? 'bg-success' : (order.status === 'Perlu dikirim' ? 'bg-info text-dark' : 'bg-danger')}">
                          {order.status}
                        </span>
                      </td>
                      <td>
                        <div class="d-flex gap-1">
                          <button class="btn btn-success btn-sm px-2" on:click={() => openStatusModal(order)}>Edit</button>
                          <button class="btn btn-info btn-sm px-2 text-white" on:click={() => showInvoice(order)}>Invoice</button>
                          <button class="btn btn-danger btn-sm px-2" on:click={() => deleteOrder(order.id)}>Hapus</button>
                        </div>
                      </td>
                    </tr>
                  {/each}
                {/if}
              </tbody>
            </table>
          </div>
        </div>

      {:else if activeTab === "invoice" && selectedInvoice}
        <!-- 6. INVOICE DETAIL VIEW -->
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
          <h2 class="fw-bold mb-0 text-dark">Invoice Detail</h2>
          <div>
            <button class="btn btn-outline-secondary me-2" on:click={() => activeTab = "orders"}>
              <i class="bi bi-arrow-left me-1"></i>Kembali
            </button>
            <button class="btn btn-outline-primary" on:click={triggerPrint}>
              <i class="bi bi-printer me-1"></i>Cetak
            </button>
          </div>
        </div>

        <div class="card border-0 shadow p-5 invoice-print-box bg-white">
          <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
              <h2 class="fw-bold brand-logo text-warning" style="font-family: 'Lobster', sans-serif;"><i class="bi bi-shop me-2"></i>Corndog Alle</h2>
            </div>
            <div class="text-end">
              <h5 class="fw-bold text-secondary mb-0">Tanggal: {new Date(selectedInvoice.created_at).toLocaleDateString('id-ID')}</h5>
            </div>
          </div>

          <div class="row mb-5">
            <div class="col-sm-4 mb-3 mb-sm-0">
              <span class="text-muted text-uppercase d-block mb-2" style="font-size: 0.8rem; font-weight: bold;">Dari</span>
              <h6 class="fw-bold mb-1">{shopSettings.name || 'Corndog Alle'}</h6>
              <p class="text-muted mb-0 small" style="line-height: 1.4;">
                {shopSettings.address || 'Jl. Cilandak, No 45, Jakarta Barat, DKI Jakarta 94107'}<br>
                Telepon: {shopSettings.phone || '(+62) 895-3653-05896'}<br>
                Email: {shopSettings.email || 'info@corndogalle.com'}
              </p>
            </div>
            
            <div class="col-sm-4 mb-3 mb-sm-0">
              <span class="text-muted text-uppercase d-block mb-2" style="font-size: 0.8rem; font-weight: bold;">Kepada</span>
              <h6 class="fw-bold mb-1">{selectedInvoice.first_name} {selectedInvoice.last_name}</h6>
              <p class="text-muted mb-0 small" style="line-height: 1.4;">
                {selectedInvoice.address}<br>
                {#if selectedInvoice.alt_address}{selectedInvoice.alt_address}<br>{/if}
                {selectedInvoice.city}, {selectedInvoice.province} {selectedInvoice.postal_code}<br>
                Telepon: {selectedInvoice.phone}<br>
                Email: {selectedInvoice.email || '-'}
              </p>
            </div>

            <div class="col-sm-4">
              <h5 class="fw-bold text-dark mb-2">Invoice #{selectedInvoice.invoice_number}</h5>
              <div class="small">
                <div class="d-flex justify-content-between mb-1">
                  <span class="text-muted">ID Pesanan:</span>
                  <span class="fw-semibold">{selectedInvoice.id}ORDER</span>
                </div>
                <div class="d-flex justify-content-between mb-1">
                  <span class="text-muted">Waktu Pembayaran:</span>
                  <span class="fw-semibold">
                    {selectedInvoice.payment_status === 'paid' ? new Date(selectedInvoice.updated_at).toLocaleDateString('id-ID') : 'Belum Bayar'}
                  </span>
                </div>
                <div class="d-flex justify-content-between">
                  <span class="text-muted">Metode Pembayaran:</span>
                  <span class="fw-semibold">{selectedInvoice.payment_method}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="table-responsive mb-4">
            <table class="table border">
              <thead class="table-light">
                <tr>
                  <th scope="col" style="width: 80px;">Jumlah</th>
                  <th scope="col">Produk</th>
                  <th scope="col">Deskripsi / Catatan</th>
                  <th scope="col" class="text-end" style="width: 150px;">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                {#each selectedInvoice.items as item}
                  <tr>
                    <td class="fw-bold">{item.quantity}</td>
                    <td class="fw-semibold">{item.product.name}</td>
                    <td class="text-muted small">{item.notes || '-'}</td>
                    <td class="text-end fw-semibold">Rp. {numberWithCommas(item.price * item.quantity)}</td>
                  </tr>
                {/each}
              </tbody>
            </table>
          </div>

          <div class="row">
            <div class="col-md-7 mb-4 mb-md-0">
              <h6 class="fw-bold mb-3 text-secondary">Metode Pembayaran:</h6>
              <div class="payment-icons-box d-flex gap-2 align-items-center mb-3">
                <img src="https://img.icons8.com/color/48/000000/visa.png" style="width: 38px;" alt="Visa" />
                <img src="https://img.icons8.com/color/48/000000/mastercard.png" style="width: 38px;" alt="Mastercard" />
                <img src="https://img.icons8.com/color/48/000000/paypal.png" style="width: 38px;" alt="Paypal" />
              </div>
              <p class="text-muted small w-75" style="font-size: 0.78rem;">
                Silakan lakukan pembayaran sesuai dengan metode pilihan Anda. Jika memilih BCA Virtual Account, mohon transfer ke nomor rekening yang tertera di menu Data Rekening.
              </p>
            </div>
            
            <div class="col-md-5">
              <div class="border rounded p-3 bg-light">
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Subtotal:</span>
                  <span class="fw-semibold">Rp. {numberWithCommas(selectedInvoice.total_price - selectedInvoice.shipping_cost)}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Ongkir:</span>
                  <span class="fw-semibold">Rp. {numberWithCommas(selectedInvoice.shipping_cost)}</span>
                </div>
                <hr />
                <div class="d-flex justify-content-between fs-5 fw-bold text-dark">
                  <span>Total:</span>
                  <span>Rp. {numberWithCommas(selectedInvoice.total_price)}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end gap-2 mt-5 no-print">
            <button class="btn btn-secondary px-4 py-2" on:click={() => activeTab = "orders"}>Kembali</button>
            <button class="btn btn-outline-primary px-4 py-2" on:click={triggerPrint}><i class="bi bi-printer me-1"></i>Cetak</button>
            
            {#if selectedInvoice.payment_status !== 'paid'}
              <button class="btn btn-success px-4 py-2 fw-bold" on:click={submitPayment}>
                <i class="bi bi-check-circle me-1"></i>Submit Pembayaran
              </button>
            {/if}
          </div>
        </div>

      {:else if activeTab === "shop-settings"}
        <!-- 7. ALAMAT TOKO VIEW -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="fw-bold mb-0 text-dark">Alamat Toko</h2>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="/admin">Home</a></li>
              <li class="breadcrumb-item active">Alamat</li>
            </ol>
          </nav>
        </div>

        <div class="card border-0 shadow-sm p-4 max-width-600">
          <h5 class="fw-bold text-secondary mb-4">Edit Info Toko</h5>
          <hr />

          <div class="mb-3">
            <label for="shop-name" class="form-label fw-semibold">Nama Toko</label>
            <input type="text" id="shop-name" class="form-control border-warning" bind:value={shopSettings.name} />
          </div>

          <div class="mb-3">
            <label for="shop-addr" class="form-label fw-semibold">Alamat Lengkap</label>
            <textarea id="shop-addr" class="form-control border-warning" rows="3" bind:value={shopSettings.address}></textarea>
          </div>

          <div class="mb-3">
            <label for="shop-phone" class="form-label fw-semibold">Nomor Telepon</label>
            <input type="text" id="shop-phone" class="form-control border-warning" bind:value={shopSettings.phone} />
          </div>

          <div class="mb-4">
            <label for="shop-email" class="form-label fw-semibold">Email Toko</label>
            <input type="email" id="shop-email" class="form-control border-warning" bind:value={shopSettings.email} />
          </div>

          <button class="btn btn-warning text-white fw-bold px-4 py-2" on:click={handleSaveShopSettings}>
            <i class="bi bi-save me-1"></i>Simpan Perubahan
          </button>
        </div>

      {:else if activeTab === "bank-accounts"}
        <!-- 8. DATA REKENING VIEW -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="fw-bold mb-0 text-dark">Data Rekening</h2>
          <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
              <li class="breadcrumb-item"><a href="/admin">Home</a></li>
              <li class="breadcrumb-item active">Rekening</li>
            </ol>
          </nav>
        </div>

        <div class="row">
          <!-- Left Column: Accounts List -->
          <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm p-4 h-100">
              <h5 class="fw-semibold text-secondary mb-3">Daftar Rekening Bank Aktif</h5>
              <div class="table-responsive">
                <table class="table table-hover border">
                  <thead class="table-light">
                    <tr>
                      <th scope="col" style="width: 80px;">No</th>
                      <th scope="col">Nama Bank</th>
                      <th scope="col">Atas Nama</th>
                      <th scope="col">No Rekening</th>
                      <th scope="col" style="width: 150px;">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    {#if loading}
                      <tr>
                        <td colspan="5" class="text-center py-4">
                          <div class="spinner-border text-primary spinner-border-sm" role="status"></div> Loading...
                        </td>
                      </tr>
                    {:else if bankAccountsList.length === 0}
                      <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Belum ada rekening pembayaran.</td>
                      </tr>
                    {:else}
                      {#each bankAccountsList as bank, idx}
                        <tr>
                          <th scope="row">{idx + 1}</th>
                          <td class="fw-semibold">{bank.bank_name}</td>
                          <td>{bank.account_name}</td>
                          <td class="fw-mono">{bank.account_number}</td>
                          <td>
                            <div class="d-flex gap-1">
                              <button class="btn btn-success btn-sm text-white" on:click={() => editBankAccount(bank)}>Edit</button>
                              <button class="btn btn-danger btn-sm" on:click={() => deleteBankAccount(bank.id)}>Hapus</button>
                            </div>
                          </td>
                        </tr>
                      {/each}
                    {/if}
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Right Column: Add/Edit Account Form -->
          <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 24px;">
              <h5 class="fw-bold mb-3 text-dark">{editingBankAccountId ? "Ubah Rekening" : "Tambah Rekening Baru"}</h5>
              <hr />

              <div class="mb-3">
                <label for="bank-name" class="form-label fw-semibold">Nama Bank (e.g. BCA)</label>
                <input type="text" id="bank-name" class="form-control border-warning" bind:value={bankFormName} />
              </div>

              <div class="mb-3">
                <label for="bank-holder" class="form-label fw-semibold">Atas Nama</label>
                <input type="text" id="bank-holder" class="form-control border-warning" bind:value={bankFormHolder} />
              </div>

              <div class="mb-4">
                <label for="bank-num" class="form-label fw-semibold">Nomor Rekening</label>
                <input type="text" id="bank-num" class="form-control border-warning" bind:value={bankFormNumber} />
              </div>

              <button class="btn btn-warning w-100 text-white fw-bold py-2 shadow-sm" on:click={handleSaveBankAccount}>
                <i class="bi bi-save me-1"></i>Simpan Rekening
              </button>
              {#if editingBankAccountId}
                <button class="btn btn-outline-secondary w-100 mt-2 btn-sm" on:click={() => {
                  editingBankAccountId = null;
                  bankFormName = "";
                  bankFormHolder = "";
                  bankFormNumber = "";
                }}>Batal Edit</button>
              {/if}
            </div>
          </div>
        </div>
      {/if}
    </div>

    <!-- Admin Footer -->
    <footer class="admin-footer bg-white border-top text-center py-3 text-muted small mt-auto no-print">
      Copyright &copy; 2020-2026 <strong>CorndogAlle</strong>. All rights reserved.
      <span class="float-end me-3 d-none d-sm-inline">Version 3.1.0</span>
    </footer>
  </main>
</div>

<!-- Modal Update Order Status -->
{#if showStatusModal}
  <div class="modal-backdrop-custom" on:click={() => showStatusModal = false}></div>
  <div class="modal-card-custom shadow-lg p-4" style="max-width: 400px; z-index: 1060;">
    <div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-3">
      <h5 class="fw-bold mb-0">Ubah Status Pesanan</h5>
      <button class="btn btn-close-custom" on:click={() => showStatusModal = false}>
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <div class="mb-3">
      <label for="order-status-select" class="form-label fw-semibold">Status Pengiriman</label>
      <select id="order-status-select" class="form-select border-warning" bind:value={editOrderStatus}>
        <option value="Perlu di Cek">Perlu di Cek</option>
        <option value="Perlu dikirim">Perlu dikirim</option>
        <option value="Selesai">Selesai</option>
      </select>
    </div>

    <div class="mb-4">
      <label for="payment-status-select" class="form-label fw-semibold">Status Pembayaran</label>
      <select id="payment-status-select" class="form-select border-warning" bind:value={editOrderPayment}>
        <option value="unpaid">Belum Bayar (unpaid)</option>
        <option value="paid">Lunas (paid)</option>
      </select>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-outline-secondary w-50" on:click={() => showStatusModal = false}>Batal</button>
      <button class="btn btn-warning text-white fw-bold w-50" on:click={handleSaveOrderStatus}>Simpan</button>
    </div>
  </div>
{/if}
{/if}

<style>
  .admin-wrapper {
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .admin-sidebar {
    width: 250px;
    flex-shrink: 0;
    transition: all 0.3s ease;
    z-index: 1000;
  }

  .sidebar-nav .nav-link {
    padding: 10px 15px;
    font-weight: 500;
    transition: all 0.2s;
  }
  .sidebar-nav .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
  }
  .sidebar-nav .nav-link.active {
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.35);
  }

  .arrow-icon {
    font-size: 0.8rem;
    transition: transform 0.3s;
  }
  .nav-link.active .arrow-icon {
    transform: rotate(-90deg);
  }

  .admin-main {
    overflow-x: hidden;
    min-width: 0;
  }

  /* Metric cards custom (AdminLTE box style) */
  .small-box {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
  }
  .small-box .icon {
    top: 15px;
    transition: all 0.3s linear;
  }
  .small-box:hover .icon {
    transform: scale(1.1) translateY(-50%);
  }

  .max-width-600 {
    max-width: 600px;
  }

  /* Sidebar collapsed class toggle */
  .sidebar-hidden .admin-sidebar {
    margin-left: -250px;
  }

  /* Invoice Print styling */
  @media print {
    .no-print {
      display: none !important;
    }
    .admin-sidebar {
      display: none !important;
    }
    .admin-navbar {
      display: none !important;
    }
    .admin-footer {
      display: none !important;
    }
    .content-wrapper {
      padding: 0 !important;
    }
    .invoice-print-box {
      border: none !important;
      box-shadow: none !important;
      padding: 0 !important;
    }
    body {
      background: white !important;
      color: black !important;
    }
  }

  /* Custom Modal styling */
  .modal-backdrop-custom {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(2px);
    z-index: 1050;
  }
  .modal-card-custom {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 90%;
    background: white;
    border-radius: 12px;
    z-index: 1060;
  }
  .btn-close-custom {
    background: none;
    border: none;
    color: #aaa;
  }
  .btn-close-custom:hover {
    color: #333;
  }
</style>
