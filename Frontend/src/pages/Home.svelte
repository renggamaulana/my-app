<script>
  import Cart from "../Components/Cart.svelte";
  import Category from "../Components/Category.svelte";
  import Navbar from "../components/Navbar.svelte";
  import Products from "../Components/Products.svelte";
  import axios from "axios";

  const API_URL = "http://localhost:3000/products";
  let menus = [];
  const getData = () => {
    axios.get(API_URL).then((response) => {
      console.log(response.data);
    });
  };
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
<button on:click={getData}>GET DATA</button>
<!-- Akhir Main Page -->
