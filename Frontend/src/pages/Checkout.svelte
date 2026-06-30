<script>
  import { onMount } from "svelte";
  import axios from "axios";
  import page from "page";
  import Swal from "sweetalert2";
  import Navbar from "../Components/Navbar.svelte";
  import { cart, user, isLoggedIn } from "../store";
  import { numberWithCommas } from "../utils/utils";

  const API_URL = "http://localhost:8000/api";

  // Form Fields
  let firstName = "";
  let lastName = "";
  let phone = "+62";
  let email = "";
  let address = "";
  let altAddress = "";
  let province = "";
  let city = "";
  let postalCode = "";
  let paymentMethod = "BCA Virtual Account"; // default payment method
  let shippingCost = 10000; // default shipping fee
  let promoCode = "";
  let promoApplied = false;
  let promoDiscount = 0;
  let submitting = false;

  // Static Provinces & Cities data for the dropdowns
  const locations = {
    "DKI Jakarta": ["Jakarta Barat", "Jakarta Pusat", "Jakarta Selatan", "Jakarta Timur", "Jakarta Utara"],
    "Jawa Barat": ["Kota Bogor", "Kota Bandung", "Kota Depok", "Kota Bekasi", "Kota Cirebon"],
    "Banten": ["Kota Tangerang", "Kota Tangerang Selatan", "Kota Serang", "Kota Cilegon"],
    "Jawa Tengah": ["Kota Semarang", "Kota Surakarta", "Kota Magelang", "Kota Tegal"],
    "Jawa Timur": ["Kota Surabaya", "Kota Malang", "Kota Pasuruan", "Kota Kediri"]
  };

  $: provinces = Object.keys(locations);
  $: cities = province ? locations[province] : [];

  // Reactive calculations
  $: totalCartItemsCount = $cart.reduce((sum, item) => sum + item.quantity, 0);
  $: subtotalPrice = $cart.reduce((sum, item) => sum + (item.product.price * item.quantity), 0);
  $: grandTotalPrice = subtotalPrice + shippingCost - promoDiscount;

  onMount(() => {
    // Redirect if cart is empty
    if ($cart.length === 0) {
      Swal.fire({
        icon: "info",
        title: "Keranjang Kosong",
        text: "Silakan pilih menu terlebih dahulu sebelum melakukan checkout.",
        confirmButtonColor: "#ff9f43"
      });
      page("/");
      return;
    }

    // Autofill name and email if logged in
    if ($isLoggedIn && $user) {
      email = $user.email || "";
      const names = $user.name.split(" ");
      firstName = names[0] || "";
      lastName = names.slice(1).join(" ") || "";
    }
  });

  function handleRedeemPromo() {
    if (promoCode.trim().toUpperCase() === "HEMAT") {
      promoDiscount = 10000;
      promoApplied = true;
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: "Promo HEMAT berhasil digunakan! Diskon Rp. 10.000",
        showConfirmButton: false,
        timer: 3000
      });
    } else {
      Swal.fire({
        icon: "error",
        title: "Promo Tidak Valid",
        text: "Kode promo tidak ditemukan.",
        confirmButtonColor: "#dc3545"
      });
    }
  }

  async function handlePlaceOrder() {
    if (!firstName || !lastName || !phone || !address || !province || !city || !postalCode) {
      Swal.fire({
        icon: "warning",
        title: "Data Belum Lengkap",
        text: "Mohon isi semua kolom alamat tagihan yang wajib.",
        confirmButtonColor: "#ff9f43"
      });
      return;
    }

    submitting = true;
    try {
      const orderData = {
        first_name: firstName,
        last_name: lastName,
        phone: phone,
        email: email || null,
        address: address,
        alt_address: altAddress || null,
        province: province,
        city: city,
        postal_code: postalCode,
        payment_method: paymentMethod,
        shipping_cost: shippingCost,
      };

      const res = await axios.post(`${API_URL}/orders`, orderData);

      // Clear local shopping cart store
      cart.set([]);

      Swal.fire({
        icon: "success",
        title: "Pesanan Berhasil Dibuat!",
        html: `Terima kasih! Pesanan Anda dengan Invoice <b>#${res.data.invoice_number}</b> sedang kami proses.<br>Silakan transfer ke nomor rekening pembayaran yang tertera.`,
        confirmButtonColor: "#ff9f43"
      });

      page("/");
    } catch (err) {
      console.error("Error creating checkout order:", err);
      Swal.fire({
        icon: "error",
        title: "Gagal Membuat Pesanan",
        text: err.response?.data?.message || "Terjadi kesalahan pada sistem backend.",
        confirmButtonColor: "#dc3545"
      });
    } finally {
      submitting = false;
    }
  }
</script>

<Navbar />

<div class="container py-5">
  <div class="text-center mb-5 mt-2">
    <h1 class="fw-bold page-title">CHECKOUT</h1>
    <hr class="mx-auto" style="width: 80px; border-top: 3px solid #ff9f43; opacity: 1;" />
  </div>

  <div class="row">
    <!-- Left Column: Billing Address Form -->
    <div class="col-md-7 mb-4">
      <div class="card border-0 shadow-sm p-4">
        <h4 class="fw-bold text-dark mb-4 section-heading">Alamat Tagihan</h4>
        
        <div class="row mb-3">
          <div class="col-sm-6 mb-3 mb-sm-0">
            <label for="first-name" class="form-label fw-semibold">Nama depan</label>
            <input
              type="text"
              id="first-name"
              class="form-control"
              bind:value={firstName}
              required
            />
          </div>
          <div class="col-sm-6">
            <label for="last-name" class="form-label fw-semibold">Nama belakang</label>
            <input
              type="text"
              id="last-name"
              class="form-control"
              bind:value={lastName}
              required
            />
          </div>
        </div>

        <div class="mb-3">
          <label for="phone-number" class="form-label fw-semibold">No. Telepon</label>
          <input
            type="text"
            id="phone-number"
            class="form-control"
            bind:value={phone}
            required
          />
        </div>

        <div class="mb-3">
          <label for="email-address" class="form-label fw-semibold">Email (Optional)</label>
          <input
            type="email"
            id="email-address"
            class="form-control"
            placeholder="email@example.com"
            bind:value={email}
          />
        </div>

        <div class="mb-3">
          <label for="bill-address" class="form-label fw-semibold">Alamat</label>
          <input
            type="text"
            id="bill-address"
            class="form-control"
            placeholder="Nama jalan, nomor rumah, RT/RW, kecamatan"
            bind:value={address}
            required
          />
        </div>

        <div class="mb-3">
          <label for="alt-address" class="form-label fw-semibold">Alamat lain (Optional)</label>
          <input
            type="text"
            id="alt-address"
            class="form-control"
            placeholder="Apartemen, blok, unit, dll."
            bind:value={altAddress}
          />
        </div>

        <div class="row mb-4">
          <div class="col-sm-5 mb-3 mb-sm-0">
            <label for="province-select" class="form-label fw-semibold">Provinsi</label>
            <select id="province-select" class="form-select" bind:value={province} on:change={() => city = ""}>
              <option value="">Pilih...</option>
              {#each provinces as prov}
                <option value={prov}>{prov}</option>
              {/each}
            </select>
          </div>
          <div class="col-sm-4 mb-3 mb-sm-0">
            <label for="city-select" class="form-label fw-semibold">Kota</label>
            <select id="city-select" class="form-select" bind:value={city} disabled={!province}>
              <option value="">Pilih...</option>
              {#each cities as c}
                <option value={c}>{c}</option>
              {/each}
            </select>
          </div>
          <div class="col-sm-3">
            <label for="postal-code" class="form-label fw-semibold">Kode Pos</label>
            <input
              type="text"
              id="postal-code"
              class="form-control"
              bind:value={postalCode}
              required
            />
          </div>
        </div>

        <!-- Payment Method selection -->
        <h5 class="fw-bold text-dark mt-4 mb-3">Metode Pembayaran</h5>
        <div class="payment-methods-grid d-flex flex-wrap gap-3">
          <div class="form-check payment-option p-3 border rounded flex-grow-1">
            <input 
              class="form-check-input ms-0 me-2" 
              type="radio" 
              name="payment" 
              id="pay-bca" 
              value="BCA Virtual Account" 
              bind:group={paymentMethod}
            />
            <label class="form-check-label fw-semibold" for="pay-bca">
              BCA Virtual Account
            </label>
          </div>
          <div class="form-check payment-option p-3 border rounded flex-grow-1">
            <input 
              class="form-check-input ms-0 me-2" 
              type="radio" 
              name="payment" 
              id="pay-cod" 
              value="Cash on Delivery" 
              bind:group={paymentMethod}
            />
            <label class="form-check-label fw-semibold" for="pay-cod">
              Cash on Delivery
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Shopping Summary -->
    <div class="col-md-5">
      <div class="card border-0 shadow-sm p-4 sticky-top" style="top: 24px; z-index: 10;">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h4 class="fw-bold text-primary mb-0 flex-grow-1" style="color: #ff9f43 !important;">
            Ringkasan Belanja
          </h4>
          <span class="badge bg-primary rounded-circle fs-6 d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background-color: #3b82f6 !important;">
            {totalCartItemsCount}
          </span>
        </div>

        <!-- Cart Items List -->
        <div class="summary-items mb-4">
          {#each $cart as item}
            <div class="summary-item py-3 border-bottom d-flex justify-content-between align-items-start">
              <div>
                <h6 class="fw-bold text-dark mb-1">{item.product.name}</h6>
                <small class="text-muted d-block">Harga: Rp. {numberWithCommas(item.product.price)}</small>
                <small class="text-muted d-block">Jumlah: {item.quantity}</small>
                {#if item.notes}
                  <small class="text-secondary d-block"><i class="bi bi-chat-left-text me-1"></i>Catatan: {item.notes}</small>
                {/if}
              </div>
              <span class="fw-bold text-dark">
                Rp. {numberWithCommas(item.product.price * item.quantity)}
              </span>
            </div>
          {/each}
        </div>

        <!-- Subtotal, Shipping, Discount -->
        <div class="price-rows mb-4">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Subtotal</span>
            <span class="fw-semibold">Rp. {numberWithCommas(subtotalPrice)}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Ongkos Kirim</span>
            <span class="fw-semibold">Rp. {numberWithCommas(shippingCost)}</span>
          </div>
          {#if promoApplied}
            <div class="d-flex justify-content-between mb-2 text-success">
              <span>Diskon (HEMAT)</span>
              <span>- Rp. {numberWithCommas(promoDiscount)}</span>
            </div>
          {/if}
          <div class="d-flex justify-content-between border-top pt-3 fs-5">
            <span class="fw-bold">Total (IDR)</span>
            <span class="fw-bold text-dark">Rp. {numberWithCommas(grandTotalPrice)}</span>
          </div>
        </div>

        <!-- Promo Code Section -->
        <form on:submit|preventDefault={handleRedeemPromo} class="promo-code-form p-2 border rounded bg-light mb-4">
          <div class="input-group">
            <input
              type="text"
              class="form-control border-0 bg-transparent"
              placeholder="Promo code"
              bind:value={promoCode}
              disabled={promoApplied}
            />
            <button 
              type="submit" 
              class="btn btn-secondary px-3" 
              disabled={promoApplied || !promoCode}
              style="background-color: #4b5563; border-color: #4b5563;"
            >
              Redeem
            </button>
          </div>
        </form>

        <!-- Place Order Submit Button -->
        <button 
          type="button" 
          class="btn btn-warning w-100 fw-bold text-white shadow-sm py-3 btn-place-order fs-5" 
          on:click={handlePlaceOrder}
          disabled={submitting}
        >
          {#if submitting}
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
            Memproses Pesanan...
          {:else}
            Buat Pesanan Sekarang
          {/if}
        </button>

        <a href="/" class="btn btn-link w-100 text-center mt-3 text-secondary text-decoration-none">
          <i class="bi bi-arrow-left me-1"></i>Kembali ke Home
        </a>
      </div>
    </div>
  </div>
</div>

<style>
  .page-title {
    letter-spacing: 2px;
    color: #2c3e50;
  }
  .section-heading {
    border-bottom: 2px solid #f1f2f6;
    padding-bottom: 12px;
  }
  .form-control:focus, .form-select:focus {
    border-color: #ff9f43;
    box-shadow: 0 0 0 0.25rem rgba(255, 159, 67, 0.25);
  }
  .payment-option {
    cursor: pointer;
    transition: all 0.2s ease;
  }
  .payment-option:hover {
    border-color: #ff9f43 !important;
    background-color: #fffaf5;
  }
  .payment-option input:checked + label {
    color: #ff9f43;
  }
  .btn-place-order {
    background-color: #ff9f43;
    border: none;
    border-radius: 8px;
    transition: all 0.3s ease;
  }
  .btn-place-order:hover {
    background-color: #f39c12;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(243, 156, 18, 0.3);
  }
  .summary-items {
    max-height: 250px;
    overflow-y: auto;
  }
</style>
