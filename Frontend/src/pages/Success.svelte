<script>
  import axios from "axios";
  import { onMount, onDestroy, beforeUpdate, afterUpdate } from "svelte";
  import { API_URL } from "../utils/utils.js";

  // onMount(() => {
  //   axios
  //     .get(`${API_URL}carts`)
  //     .then((res) => {
  //       carts = res.data;
  //       carts.map(function (item) {
  //         return axios
  //           .delete(`${API_URL}carts/${item.id}`)
  //           .then((res) => console.log(res))
  //           .catch((error) => console.log(error));
  //       });
  //     })
  //     .catch((err) => {
  //       console.log(err);
  //     });
  // });
</script>

<div class="mt-5 text-center">
  <img src="../assets/img/success1.png" alt="order comfirmed" width="500" />
  <h2>Pesanan Sukses</h2>
  <p>Terimakasih telah memesan!</p>
  <a href="/" class="btn btn-warning" on:click={onMount}>Kembali</a>
</div>

<style>
  a {
    text-decoration: none;
    color: white;
  }
  a:hover {
    text-decoration: none;
    color: #eee;
  }
  p {
    color: #777;
  }
  h2 {
    color: #777;
  }
</style>
