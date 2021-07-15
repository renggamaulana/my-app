<script>
  import axios from "axios";
  import { numberWithCommas } from "../utils/utils";
  import { API_URL } from "../utils/utils.js";
  import swal from "sweetalert";

  let menus = [];
  let cart = [];

  // Did Mount Component
  // Binding Category
  export function showCategory(value) {
    axios
      .get(`${API_URL}products?category.name=${value}`)
      .then((res) => {
        menus = res.data;
      })
      .catch((err) => {
        console.log(err);
      });
  }

  // Menu home(default)
  axios
    .get(`${API_URL}products?category.name=corndog`)
    .then((res) => {
      menus = res.data;
    })
    .catch((err) => {
      console.log(err);
    });

  // Cart

  axios
    .get(`${API_URL}cart`)
    .then((res) => {
      cart = res.data;
    })
    .catch((err) => {
      console.log(err);
    });

  // Add to Cart

  const addCart = (value) => {
    axios
      .get(`${API_URL}cart/product.id=${value.id}`)
      .then((res) => {
        if (res.data.length === 0) {
          const cart = {
            jumlah: 1,
            total_harga: value.price,
            product: value,
          };

          axios
            .post(`${API_URL}cart`, cart)
            .then((res) => {
              swal({
                title: "Sukses Masuk Keranjang",
                text: "Sukses Masuk Keranjang " + cart.product.name,
                icon: "success",
                button: false,
              });
            })
            .catch((err) => {
              console.log(err);
            });
        } else {
          const cart = {
            jumlah: res.data[0].jumlah + 1,
            total_harga: res.data[0].total_harga + value.price,
            product: value,
          };
          axios
            .put(API_URL + "cart/" + res.data[0].id, cart)
            .then((res) => {
              swal({
                title: "Sukses Masuk Keranjang",
                text: "Sukses Masuk Keranjang " + cart.product.name,
                icon: "success",
                button: false,
                timer: 1400,
              });
            })
            .catch((err) => {
              console.log(err);
            });
        }
      })
      .catch((err) => {
        console.log(err);
      });

    const cart = {
      jumlah: 1,
      total_harga: value.price,
      product: value,
    };

    axios
      .post(`${API_URL}cart`, cart)
      .then((res) => {
        swal({
          title: "Sukses Masuk Keranjang",
          text: "Sukses Masuk Keranjang " + cart.product.name,
          icon: "success",
          button: false,
          timer: 1400,
        });
      })
      .catch((err) => {
        console.log(err);
      });
  };
</script>

<h4>Daftar Menu</h4>
<hr />
<div class="container">
  <div class="row">
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
            <h6 class="card-title">{menu.name}</h6>
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
</style>
