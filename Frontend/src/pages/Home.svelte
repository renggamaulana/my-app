<script>
  import Cart from "../Components/Cart.svelte";
  import Category from "../Components/Category.svelte";
  import Navbar from "../components/Navbar.svelte";
  import Products from "../Components/Products.svelte";
</script>

<!-- Navbar -->
<Navbar />
<!-- Akhir Navbar -->
<!-- Main Page -->
<div class="container-fluid">
  <div class="row mt-4">
    <div class="col-md-2">
      <Category />
    </div>
    <div class="col-md-7">
      <Products />
    </div>
    <div class="col-md-3">
      <Cart />
    </div>
  </div>
</div>
<!-- Akhir Main Page -->
