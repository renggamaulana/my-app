<script>
  import axios from "axios";
  import swal from "sweetalert";
  import { API_URL } from "../utils/utils.js";
  import { numberWithCommas } from "../utils/utils";
  import { beforeUpdate } from "svelte";
  import { push } from "svelte-history";
  import {
    Button,
    Modal,
    ModalBody,
    ModalFooter,
    ModalHeader,
  } from "sveltestrap";

  let cart = [];
  let totalPay = 0;

  let quantity = 0;
  let cartDetail = false;
  let note = "";
  let totalPrice = 0;

  let open = false;
  const toggle = (listCart) => {
    open = !open;
    cartDetail = listCart;
    (quantity = listCart.quantity), (note = listCart.note);
    totalPrice = listCart.total_price;
  };

  const plus = () => {
    quantity = quantity + 1;
    totalPrice = quantity * cartDetail.product.price;
  };

  const minus = () => {
    if (quantity !== 1) {
      quantity = quantity - 1;
      totalPrice = quantity * cartDetail.total_price;
    }
  };

  const changeHandler = (event) => {
    note = event.target.value;
  };

  const handleClose = () => {
    open = !open;
  };

  const handleSubmit = (event) => {
    event.preventDefault();
    handleClose();
    const data = {
      quantity: quantity,
      total_price: totalPrice,
      product: cartDetail.product,
      note: note,
    };

    axios
      .put(`${API_URL}carts/${cartDetail.id}`, data)
      .then((res) => {
        swal({
          title: "Update Pesanan!",
          text: "Sukses Update Pesanan" + data.product.name,
          icon: "success",
          button: false,
          timer: 1500,
        });
      })
      .catch((error) => {
        console.log(error);
      });
    console.log(note);
  };

  const deleteOrder = (id) => {
    handleClose();

    axios
      .delete(`${API_URL}carts/${id}`)
      .then((res) => {
        swal({
          title: "Hapus Pesanan!",
          text: "Sukses Hapus Pesanan" + cartDetail.product.name,
          icon: "error",
          button: false,
          timer: 1500,
        });
      })
      .catch((error) => {
        console.log(error);
      });
    console.log(note);
  };

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

  <div class="card overflow-auto result">
    <ul class="list-group list-group-flush">
      {#each cart as listCart}
        <li
          class="list-group-item d-flex justify-content-between align-items-start"
          on:click={() => toggle(listCart)}
        >
          <div class="ms-2 me-auto">
            <div class="fw-bold">{listCart.product.name}</div>
            Rp. {numberWithCommas(listCart.total_price)}
          </div>
          <span class="badge bg-primary rounded-pill">{listCart.quantity}</span>
        </li>
      {/each}
    </ul>
  </div>
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
        href="checkout"
        class="btn btn-warning"
        on:click={() => submitTotal(totalPay)}
        ><strong> <i class="bi bi-cart4 mx-1" />Checkout</strong></a
      >
    </div>
  </div>
</div>

{#if cartDetail}
  <div>
    <Modal isOpen={open} {toggle}>
      <ModalHeader {toggle}>
        {cartDetail.product.name}
        {" "}
        <strong>
          (Rp. {numberWithCommas(cartDetail.product.price)})
        </strong>
      </ModalHeader>
      <ModalBody>
        <!-- <form on:submit={handleSubmit}> -->
        <p class="form-label">Total Harga :</p>
        <p>
          <strong> Rp. {numberWithCommas(totalPrice)}</strong>
        </p>

        <p class="form-label">Jumlah :</p>
        <button class="btn btn-primary mx-1"
          ><i class="fas fa-minus" on:click={minus} />
        </button>
        {quantity}
        <button class="btn btn-primary mx-1"
          ><i class="fas fa-plus" on:click={plus} /></button
        >

        <div class="my-3">
          <label for="exampleFormControlTextarea1" class="form-label"
            >Keterangan</label
          >
          <textarea
            class="form-control"
            id="exampleFormControlTextarea1"
            rows="3"
            name="keterangan"
            placeholder="Contoh : Frozen, Extra Topping"
            on:change={(event) => changeHandler(event)}
          />
        </div>
        <button class="btn btn-primary" on:click={handleSubmit}>Simpan</button>
        <!-- </form> -->
      </ModalBody>
      <ModalFooter>
        <Button color="danger" on:click={() => deleteOrder(cartDetail.id)}
          ><i class="fas fa-trash me-2" />Hapus Pesanan</Button
        >
      </ModalFooter>
    </Modal>
  </div>
{:else}
  <div>
    <Modal isOpen={open} {toggle}>
      <ModalHeader {toggle}>Modal title</ModalHeader>
      <ModalBody>Kosong</ModalBody>
      <ModalFooter>
        <Button color="primary" on:click={toggle}>Do Something</Button>
        <Button color="secondary" on:click={toggle}>Cancel</Button>
      </ModalFooter>
    </Modal>
  </div>
{/if}

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

  .fas {
    width: 0.7em;
    height: 0.7em;
  }

  @media only screen and (max-height: 1000px) {
    .result {
      height: 407px;
    }
  }

  .overflow-auto::-webkit-scrollbar {
    display: none;
  }
</style>
