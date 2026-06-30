<script>
  import axios from "axios";
  import page from "page";
  import Swal from "sweetalert2";
  import { token, user, isLoggedIn, cart, searchTerm } from "../store";

  const API_URL = "http://localhost:8000/api";

  let searchInput = "";

  // Reactive total items inside the cart
  $: totalCartItems = $cart.reduce((sum, item) => sum + item.quantity, 0);

  function handleSearch(e) {
    e.preventDefault();
    searchTerm.set(searchInput);
  }

  // Live search as the user types
  function handleInput() {
    searchTerm.set(searchInput);
  }

  async function handleLogout() {
    try {
      await axios.post(`${API_URL}/logout`);
    } catch (err) {
      console.error("Logout error in backend:", err);
    } finally {
      // Clear token, user and cart states regardless of backend success
      token.set(null);
      user.set(null);
      cart.set([]);

      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "info",
        title: "Anda telah keluar.",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      });

      page("/login");
    }
  }
</script>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
  <div class="container">
    <a class="navbar-brand text-warning font-weight-bold" href="/">
      <i class="bi bi-shop me-2"></i>Corndog Alle
    </a>
    
    <button
      class="navbar-toggler"
      type="button"
      data-bs-toggle="collapse"
      data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent"
      aria-expanded="false"
      aria-label="Toggle navigation"
    >
      <span class="navbar-toggler-icon" />
    </button>
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link active" href="/">Menu</a>
        </li>
        
        {#if $isLoggedIn}
          <li class="nav-item">
            <span class="nav-link text-light font-weight-normal ms-lg-2">
              <i class="bi bi-person-circle me-1"></i>Halo, {$user ? $user.name : "Pelanggan"}
            </span>
          </li>
          <li class="nav-item">
            <button class="btn btn-link nav-link text-danger" on:click={handleLogout}>
              <i class="bi bi-box-arrow-right me-1"></i>Keluar
            </button>
          </li>
        {:else}
          <li class="nav-item">
            <a class="nav-link" href="/login">Masuk</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/register">Daftar</a>
          </li>
        {/if}
      </ul>

      <div class="d-flex align-items-center mt-2 mt-lg-0">
        <!-- Search bar -->
        <form class="d-flex me-3" on:submit={handleSearch}>
          <div class="input-group">
            <input
              class="form-control"
              type="search"
              placeholder="Cari menu..."
              aria-label="Search"
              bind:value={searchInput}
              on:input={handleInput}
            />
            <button class="btn btn-warning" type="submit">
              <i class="bi bi-search"></i>
            </button>
          </div>
        </form>

        <!-- Cart Badge Icon -->
        <a href="/" class="btn btn-outline-warning position-relative px-3">
          <i class="bi bi-cart3"></i>
          {#if totalCartItems > 0}
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              {totalCartItems}
            </span>
          {/if}
        </a>
      </div>
    </div>
  </div>
</nav>

<style>
  .navbar-brand {
    font-family: "Caveat", cursive;
    font-size: 1.8rem;
  }
  a {
    text-decoration: none;
  }
  .btn-link {
    border: none;
    text-decoration: none;
  }
  .btn-link:hover {
    text-decoration: underline;
  }
</style>
