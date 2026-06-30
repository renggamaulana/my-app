<script>
  import axios from "axios";
  import page from "page";
  import Swal from "sweetalert2";
  import { token, user } from "../store";

  const API_URL = "http://localhost:8000/api";

  let email = "";
  let password = "";
  let loading = false;
  let errors = {};

  async function handleLogin() {
    loading = true;
    errors = {};

    try {
      const response = await axios.post(`${API_URL}/login`, {
        email,
        password,
      });

      // Save to store (which also handles localStorage saving)
      token.set(response.data.access_token);
      user.set(response.data.user);

      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "success",
        title: `Selamat Datang, ${response.data.user.name}!`,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
      });

      if (response.data.user.role === "admin") {
        page("/admin");
      } else {
        page("/");
      }
    } catch (err) {
      if (err.response && err.response.data && err.response.data.errors) {
        errors = err.response.data.errors;
        let errorMessage = Object.values(errors).flat().join("\n");
        Swal.fire({
          icon: "error",
          title: "Gagal Masuk",
          text: errorMessage,
          confirmButtonColor: "#dc3545",
        });
      } else if (err.response && err.response.status === 422) {
        Swal.fire({
          icon: "error",
          title: "Kredensial Salah",
          text: "Email atau kata sandi yang Anda masukkan salah.",
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

<div class="login-container d-flex align-items-center justify-content-center">
  <div class="login-card p-4 shadow-lg">
    <div class="text-center mb-4">
      <h2 class="brand-title">Corndog Alle</h2>
      <p class="text-muted">Masuk ke akun Anda untuk mulai memesan</p>
    </div>

    <form on:submit|preventDefault={handleLogin}>
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

      <div class="mb-4">
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

      <button type="submit" class="btn btn-warning w-100 btn-login" disabled={loading}>
        {#if loading}
          <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
          Memproses...
        {:else}
          Masuk
        {/if}
      </button>
    </form>

    <div class="text-center mt-4">
      <span class="text-muted">Belum punya akun? </span>
      <a href="/register" class="register-link font-weight-bold">Daftar Sekarang</a>
    </div>
  </div>
</div>

<style>
  .login-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #1f4068 0%, #162447 100%);
    padding: 20px;
  }
  .login-card {
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
  .btn-login {
    background-color: #ff9f43;
    border: none;
    color: white;
    font-weight: bold;
    padding: 10px;
    border-radius: 8px;
    transition: all 0.3s ease;
  }
  .btn-login:hover {
    background-color: #f39c12;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(243, 156, 18, 0.3);
  }
  .register-link {
    color: #ff9f43;
    text-decoration: none;
    font-weight: 600;
  }
  .register-link:hover {
    text-decoration: underline;
    color: #f39c12;
  }
</style>
