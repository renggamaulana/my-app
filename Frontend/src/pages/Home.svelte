<script>
  import Cart from "../Components/Cart.svelte";
  import Category from "../Components/Category.svelte";
  import Navbar from "../components/Navbar.svelte";
  import axios from "axios";
  import { numberWithCommas } from "../utils/utils";

  const API_URL = "http://localhost:3000/";

  let menus = [];

  axios
    .get(`${API_URL}products`)
    .then((res) => {
      menus = res.data;
      // console.log(menus);
    })
    .catch((err) => {
      console.log(err);
    });
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
      <h4>Daftar Menu</h4>
      <hr />
      <div class="row">
        {#each menus as menu}
          <div class="col-md-4 col-lg-4 col-xs-12 my-3 shadow">
            <div class="card">
              <img
                src="./assets/img/{menu.category.name}/{menu.img}"
                class="card-img-top"
                width="200"
                alt="..."
              />
              <div class="card-body">
                <h6 class="card-title">{menu.name}</h6>
                <p class="card-text">Rp. {numberWithCommas(menu.price)}</p>
                <!-- <a href="detail" class="btn btn-primary">Add to cart</a> -->
              </div>
            </div>
          </div>
        {/each}
      </div>
    </div>
    <div class="col-md-3">
      <Cart />
    </div>
  </div>
</div>

<!-- Akhir Main Page -->
<style>
  .card:hover {
    cursor: pointer;
  }
</style>
