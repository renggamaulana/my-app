<script>
  import axios from "axios";
  import page from "page";
  import Swal from "sweetalert2";

  const API_URL = "http://localhost:8000/api";

  let name = "";
  let username = "";
  let email = "";
  let password = "";
  let password_confirmation = "";
  let loading = false;
  let errors = {};

  async function handleRegister() {
    loading = true;
    errors = {};

    try {
      const response = await axios.post(`${API_URL}/register`, {
        name,
        username,
        email,
        password,
        password_confirmation,
      });

      Swal.fire({
        icon: "success",
        title: "Pendaftaran Berhasil!",
        text: "Silakan masuk menggunakan akun baru Anda.",
        confirmButtonColor: "#ffc107",
      });

      page("/login");
    } catch (err) {
      if (err.response && err.response.data && err.response.data.errors) {
        errors = err.response.data.errors;
        let errorMessage = Object.values(errors).flat().join("\n");
        Swal.fire({
          icon: "error",
          title: "Gagal Mendaftar",
          text: errorMessage,
          confirmButtonColor: "#dc3545",
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Terjadi Kesalahan",
          text: "Silakan coba lagi beberapa saat lagi.",
          confirmButtonColor: "#dc3545",
        });
      }
    } finally {
      loading = false;
    }
  }
</script>

<div class="register-container d-flex align-items-center justify-content-center">
  <div class="register-card p-4 shadow-lg">
    <div class="text-center mb-4">
      <h2 class="brand-title">Corndog Alle</h2>
      <p class="text-muted">Buat akun untuk memesan menu favoritmu</p>
    </div>

    <form on:submit|preventDefault={handleRegister}>
      <div class="mb-3">
        <label for="name" class="form-label">Nama Lengkap</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input
            type="text"
            id="name"
            class="form-control {errors.name ? 'is-invalid' : ''}"
            placeholder="Masukkan nama lengkap"
            bind:value={name}
            required
          />
        </div>
        {#if errors.name}
          <div class="invalid-feedback d-block">{errors.name[0]}</div>
        {/if}
      </div>

      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
          <input
            type="text"
            id="username"
            class="form-control {errors.username ? 'is-invalid' : ''}"
            placeholder="Pilih username unik"
            bind:value={username}
            required
          />
        </div>
        {#if errors.username}
          <div class="invalid-feedback d-block">{errors.username[0]}</div>
        {/if}
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input
            type="email"
            id="email"
            class="form-control {errors.email ? 'is-invalid' : ''}"
            placeholder="nama@email.com"
            bind:value={email}
            required
          />
        </div>
        {#if errors.email}
          <div class="invalid-feedback d-block">{errors.email[0]}</div>
        {/if}
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Kata Sandi</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input
            type="password"
            id="password"
            class="form-control {errors.password ? 'is-invalid' : ''}"
            placeholder="Masukkan kata sandi"
            bind:value={password}
            required
          />
        </div>
        {#if errors.password}
          <div class="invalid-feedback d-block">{errors.password[0]}</div>
        {/if}
      </div>

      <div class="mb-4">
        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
          <input
            type="password"
            id="password_confirmation"
            class="form-control"
            placeholder="Ulangi kata sandi"
            bind:value={password_confirmation}
            required
          />
        </div>
      </div>

      <button type="submit" class="btn btn-warning w-100 btn-register" disabled={loading}>
        {#if loading}
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Memproses...
        {:else}
          Daftar Akun
        {/if}
      </button>
    </form>

    <div class="text-center mt-4">
      <span class="text-muted">Sudah punya akun? </span>
      <a href="/login" class="login-link font-weight-bold">Masuk Sekarang</a>
    </div>
  </div>
</div>

<style>
  .register-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #1f4068 0%, #162447 100%);
    padding: 20px;
  }
  .register-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 16px;
    max-width: 450px;
    width: 100%;
    backdrop-filter: blur(10px);
  }
  .brand-title {
    font-family: "Lobster", sans-serif;
    color: #ff9f43;
    font-size: 2.5rem;
  }
  .input-group-text {
    background-color: #f1f2f6;
    border-right: none;
    color: #57606f;
  }
  .form-control {
    border-left: none;
  }
  .form-control:focus {
    box-shadow: none;
    border-color: #ced4da;
  }
  .input-group:focus-within .input-group-text {
    border-color: #ff9f43;
  }
  .input-group:focus-within .form-control {
    border-color: #ff9f43;
  }
  .btn-register {
    background-color: #ff9f43;
    border: none;
    color: white;
    font-weight: bold;
    padding: 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
  }
  .btn-register:hover {
    background-color: #f39c12;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(243, 156, 18, 0.3);
  }
  .login-link {
    color: #ff9f43;
    text-decoration: none;
    font-weight: 600;
  }
  .login-link:hover {
    text-decoration: underline;
    color: #f39c12;
  }
</style>
