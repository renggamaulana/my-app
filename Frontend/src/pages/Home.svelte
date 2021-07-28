<script>
  import Cart from "../Components/Cart.svelte";
  import Category from "../Components/Category.svelte";
  import Navbar from "../components/Navbar.svelte";
  import Menus from "../components/Menus.svelte";

  let child;

  function handleCategory(event) {
    child.showCategory(event.detail.text);
  }

  function handleCart(event) {
    child.showCart(event.detail.text);
  }
</script>

<!-- Navbar -->
<Navbar />
<!-- Akhir Navbar -->
<!-- Main Page -->
<div class="container-fluid">
  <div class="row mt-2 overflow-auto home">
    <div class="col-md-2">
      <Category on:messageCategory={handleCategory} />
    </div>
    <div class="col">
      <Menus bind:this={child} />
    </div>
    <div class="col-md-3">
      <Cart />
    </div>
  </div>
</div>

<!-- Akhir Main Page -->
<style>
  .home {
    height: 530px;
  }
  .overflow-auto::-webkit-scrollbar {
    display: none;
  }
</style>
