<script>
  import axios from "axios";
  import { numberWithCommas } from "../utils/utils";
  import { API_URL } from "../utils/utils.js";
  import swal from "sweetalert";

  let menus = [];
  let carts = [];

  // Did Mount Component
  // Binding Category
  export function showCategory(value) {
    axios
      .get(`${API_URL}products?category.name=${value}`)
      .then((res) => {
        menus = res.data;
        console.log(menus);
      })
      .catch((err) => {
        console.log(err);
      });
  }

  // Menu home(default)
  axios
    .get(`${API_URL}products?category.name=Corndog`) // laravel_api -> category_name
    .then((res) => {
      menus = res.data;
      console.log(res.data);
      // menus = res.data.data; //response menggunakan laravel api
      // console.log(1, res.data.data);
    })
    .catch((err) => {
      console.log(err);
    });

  // Cart

  axios
    .get(`${API_URL}carts`)
    .then((res) => {
      carts = res.data.data;
    })
    .catch((err) => {
      console.log(err);
    });

  // Add to Cart

  const addCart = (menu) => {
    axios
      .get(`${API_URL}carts?product.id=${menu.id}`)
      .then((res) => {
        console.log(menu, res);
        if (res.data.length === 0) {
          const cart = {
            quantity: 1,
            total_price: menu.price,
            product: menu,
          };

          axios
            .post(`${API_URL}carts`, cart)
            .then((res) => {
              swal({
                title: "Sukses Masuk Keranjang",
                text: "Sukses Masuk Keranjang" + cart.product.name,
                icon: "success",
                button: false,
                timer: 1500,
              });
            })
            .catch((error) => {
              console.log(error);
            });
        } else {
          const cart = {
            quantity: res.data[0].quantity + 1,
            total_price: res.data[0].total_price + menu.price,
            product: menu,
          };
          console.log(cart, res.data[0].total_price + menu.price);
          axios
            .put(`${API_URL}carts/${res.data[0].id}`, cart)
            .then((res) => {
              swal({
                title: "Sukses Masuk Keranjang",
                text: "Sukses Masuk Keranjang" + cart.product.name,
                icon: "success",
                button: false,
                timer: 1500,
              });
            })
            .catch((error) => {
              console.log(error);
            });
        }
      })
      .catch((err) => {
        console.log(err);
      });
  };
</script>

<h4>Daftar Menu</h4>
<hr />
<div class="container">
  <div class="row overflow-auto menu">
    {#each menus as menu}
      <div
        class="col-xl-4 col-lg-3 col-md-6 col-lg-4 col-sm-6 col-6 my-3 shadow"
      >
        <div class="card">
          <img
            src="./assets/img/{menu.category.name}/{menu.img}"
            class="card-img-top"
            width="200"
            alt="..."
          />
          <div class="card-body">
            <h5 class="card-title">{menu.name}</h5>
            <p class="card-text">Rp. {numberWithCommas(menu.price)}</p>
            <button
              class="btn btn-warning"
              on:click={() => {
                addCart(menu);
              }}><i class="bi bi-cart3" /></button
            >
          </div>
        </div>
      </div>
    {/each}
  </div>
</div>

<style>
  .card {
    z-index: 1;
  }
  .card:hover {
    cursor: pointer;
  }
  .btn-warning {
    color: white;
  }
  .btn-warning:hover {
    color: rgb(59, 59, 59);
  }
  .btn-warning:hover {
    text-decoration: none;
    color: #eee;
  }
  /* .btn-cart {
    background: rgb(216, 148, 22);
    color: white;
  } */
  /* .card img {
    width: 15vw;
    height: 30vh;
    background: cover;
  } */
  .menu {
    height: 500px;
  }
  .overflow-auto::-webkit-scrollbar {
    display: none;
  }
</style>
