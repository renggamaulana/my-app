<script>
  import axios from "axios";
  import { API_URL } from "../utils/utils.js";
  import { numberWithCommas } from "../utils/utils";
  import { onMount, onDestroy, beforeUpdate, afterUpdate } from "svelte";
  import { push, replace, go, goBack, goForward } from "svelte-history";

  let cart = [];
  let totalPay = 0;

  // const numbers = [75000, 34000];
  // const sum = numbers.reduce(function (result, item) {
  //   return result + item;
  // }, 0);

  beforeUpdate(() => {
    axios
      .get(`${API_URL}carts`)
      .then((res) => {
        cart = res.data;

        totalPay = cart.reduce(function (result, item) {
          return result + item.total_price;
        }, 0);
      })
      .catch((err) => {
        console.log(err);
      });
  });

  const submitTotal = (totalPay) => {
    const order = {
      total_pay: totalPay,
      menus: cart,
    };

    axios
      .post(`${API_URL}orders`, order)
      .then((res) => {
        push("/success");
      })
      .catch((err) => {
        console.log(err);
      });
  };
</script>

<div>
  <h4>Keranjang</h4>
  <hr />

  <ul class="list-group list-group-flush">
    {#each cart as listCart}
      <li
        class="list-group-item d-flex justify-content-between align-items-start"
      >
        <div class="ms-2 me-auto">
          <!-- <img
            src="./assets/img/{listCart.product.category.name}/{listCart.product
              .img}"
            alt={listCart.product.name}
          /><br /> -->
          <div class="fw-bold">{listCart.product.name}</div>
          Rp. {numberWithCommas(listCart.total_price)}
        </div>
        <span class="badge bg-primary rounded-pill">{listCart.quantity}</span>
      </li>
    {/each}
  </ul>
</div>

<div class="fixed-bottom">
  <div class="row">
    <div class="col md-3 offset-9 px-4 py-2">
      <h5>
        Total Harga: <strong class="float-right"
          >Rp. {numberWithCommas(totalPay)}</strong
        >
      </h5>
      <a
        href="success"
        class="btn btn-warning"
        on:click={() => submitTotal(totalPay)}
        ><strong> <i class="bi bi-cart4 mx-1" />Bayar</strong></a
      >
    </div>
  </div>
</div>

<style>
  /* img {
    width: 50px;
    height: 50px;
  } */
  .fixed-bottom {
    z-index: 0;
  }
  a {
    text-decoration: none;
    color: white;
    width: 100%;
  }
  a:hover {
    text-decoration: none;
    color: #eee;
  }
</style>
