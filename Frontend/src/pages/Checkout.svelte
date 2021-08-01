<script>
  import axios from "axios";
  import { API_URL } from "../utils/utils";
  import { numberWithCommas } from "../utils/utils";
  // let display = block;
  let metodePembayaran = "";

  const handlePayment = (value) => {
    console.log(value);
  };

  let orders = [];
  let total = 0;
  let cart = 0;
  let jumlahCart = 0;

  // Jumlah cart is  axios
  axios
    .get(`${API_URL}carts`)
    .then((res) => {
      jumlahCart = res.data.length;
    })
    .catch((err) => {
      console.log(err);
    });

  //

  axios
    .get(`${API_URL}orders`)
    .then((res) => {
      orders = res.data[0]["menus"];
      total = res.data[0]["total_pay"];
      cart = res.data;
      // console.log(total);
    })
    .catch((err) => {
      console.log(err);
    });

  const checkoutOrder = () => {
    axios
      .get(`${API_URL}carts`)
      .then((res) => {
        cart = res.data;
        cart.map(function (item) {
          return axios
            .delete(`${API_URL}carts/${item.id}`)
            .then((res) => console.log(res))
            .catch((error) => console.log(error));
        });
        console.log(cart);
      })
      .catch((err) => {
        console.log(err);
      });
  };
</script>

<a href="/"> Home</a>
<div class="container">
  <main>
    <div class="py-5 text-center">
      <h2>CHECKOUT</h2>
      <p class="lead" />
    </div>

    <!-- Cart summary -->
    <div class="row g-5">
      <div class="col-md-5 col-lg-4 order-md-last">
        <h4 class="d-flex justify-content-between align-items-center mb-3">
          <span class="text-primary">Ringkasan Belanja</span>
          <span class="badge bg-primary rounded-pill">{jumlahCart}</span>
        </h4>
        <ul class="list-group mb-3">
          {#each orders as order}
            <li class="list-group-item d-flex justify-content-between lh-sm">
              <div>
                <h6 class="my-0">{order.product.name}</h6>
                <small class="text-muted"
                  >Harga : Rp. {numberWithCommas(order.product.price)}</small
                > <br />
                <small class="text-muted">Jumlah : {order.quantity}</small>
              </div>
              <span class="text-muted"
                >Rp. {numberWithCommas(order.total_price)}</span
              >
            </li>
          {/each}

          <!-- <li class="list-group-item d-flex justify-content-between bg-light">
            <div class="text-success">
              <h6 class="my-0">Promo code</h6>
              <small>EXAMPLECODE</small>
            </div>
            <span class="text-success">−Rp.5</span>
          </li> -->
          <li class="list-group-item d-flex justify-content-between">
            <span>Total (IDR)</span>
            <strong>Rp. {numberWithCommas(total)}</strong>
          </li>
        </ul>

        <form class="card p-2">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Promo code" />
            <button type="submit" class="btn btn-secondary">Redeem</button>
          </div>
        </form>
      </div>
      <div class="col-md-7 col-lg-8">
        <h4 class="mb-3">Alamat Tagihan</h4>
        <form class="needs-validation" novalidate>
          <div class="row g-3">
            <div class="col-sm-6">
              <label for="firstName" class="form-label">Nama depan</label>
              <input
                type="text"
                class="form-control"
                id="firstName"
                placeholder=""
                value=""
                required
              />
              <div class="invalid-feedback">Valid first name is required.</div>
            </div>

            <div class="col-sm-6">
              <label for="lastName" class="form-label">Nama belakang</label>
              <input
                type="text"
                class="form-control"
                id="lastName"
                placeholder=""
                value=""
                required
              />
              <div class="invalid-feedback">Valid last name is required.</div>
            </div>

            <div class="col-12">
              <label for="username" class="form-label">No. Telepon</label>
              <div class="input-group has-validation">
                <span class="input-group-text">+62</span>
                <input
                  type="text"
                  class="form-control"
                  id="username"
                  required
                />
                <div class="invalid-feedback">Your username is required.</div>
              </div>
            </div>

            <div class="col-12">
              <label for="email" class="form-label"
                >Email <span class="text-muted">(Optional)</span></label
              >
              <input type="email" class="form-control" id="email" />
              <div class="invalid-feedback">
                Please enter a valid email address for shipping updates.
              </div>
            </div>

            <div class="col-12">
              <label for="address" class="form-label">Alamat</label>
              <input type="text" class="form-control" id="address" required />
              <div class="invalid-feedback">
                Silahkan masukkan alamat pengiriman Anda.
              </div>
            </div>

            <div class="col-12">
              <label for="address2" class="form-label"
                >Alamat lain <span class="text-muted">(Optional)</span></label
              >
              <input type="text" class="form-control" id="address2" />
            </div>

            <div class="col-md-5">
              <label for="country" class="form-label">Provinsi</label>
              <select class="form-select" id="country" required>
                <option value="">Pilih...</option>
                <option>Aceh</option>
                <option>DKI Jakarta</option>
                <option>Jawa Barat</option>
                <option>Jawa Tengah</option>
                <option>Jawa Timur</option>
              </select>
              <div class="invalid-feedback">
                Mohon masukkan provinsi yang valid.
              </div>
            </div>

            <div class="col-md-4">
              <label for="state" class="form-label">Kota</label>
              <select class="form-select" id="state" required>
                <option value="">Pilih...</option>
                <option>Kota Bogor</option>
                <option>Kota Bandung</option>
                <option>Kota Jakarta</option>
                <option>Kota Semarang</option>
              </select>
              <div class="invalid-feedback">Please provide a valid state.</div>
            </div>

            <div class="col-md-3">
              <label for="zip" class="form-label">Kode Pos</label>
              <input
                type="text"
                class="form-control"
                id="zip"
                placeholder=""
                required
              />
              <div class="invalid-feedback">Zip code required.</div>
            </div>
          </div>

          <hr class="my-4" />

          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="same-address" />
            <label class="form-check-label" for="same-address"
              >Alamat pengiriman sama dengan alamat penagihan</label
            >
          </div>

          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="save-info" />
            <label class="form-check-label" for="save-info">Ingat saya</label>
          </div>

          <hr class="my-4" />

          <h4 class="mb-3">Payment</h4>

          <div class="col-md-4">
            <label for="state" class="form-label">Pilih metode pembayaran</label
            >
            <select
              class="form-select"
              id="state"
              bind:value={metodePembayaran}
              required
            >
              <option value="">Pilih...</option>
              <option value="cod">Bayar ditempat</option>
              <option value="pembayaranVirtual">Pembayaran Virtual</option>
            </select>
            <div class="invalid-feedback">Please provide a valid state.</div>
          </div>

          <div
            class="my-3"
            style="display: {metodePembayaran == 'cod' ? 'none' : 'block'}"
          >
            <div class="form-check">
              <input
                id="credit"
                name="paymentMethod"
                type="radio"
                class="form-check-input"
                checked
                required
              />
              <label class="form-check-label" for="credit">Credit card</label>
            </div>
            <div class="form-check">
              <input
                id="debit"
                name="paymentMethod"
                type="radio"
                class="form-check-input"
                required
              />
              <label class="form-check-label" for="debit">Debit card</label>
            </div>
            <div class="form-check">
              <input
                id="paypal"
                name="paymentMethod"
                type="radio"
                class="form-check-input"
                required
              />
              <label class="form-check-label" for="paypal">PayPal</label>
            </div>
          </div>
          <div class="row gy-3">
            <div class="col-md-6">
              <label for="cc-name" class="form-label">Name on card</label>
              <input
                type="text"
                class="form-control"
                id="cc-name"
                placeholder=""
                required
              />
              <small class="text-muted">Full name as displayed on card</small>
              <div class="invalid-feedback">Name on card is required</div>
            </div>

            <div class="col-md-6">
              <label for="cc-number" class="form-label"
                >Credit card number</label
              >
              <input
                type="text"
                class="form-control"
                id="cc-number"
                placeholder=""
                required
              />
              <div class="invalid-feedback">Credit card number is required</div>
            </div>

            <div class="col-md-3">
              <label for="cc-expiration" class="form-label">Expiration</label>
              <input
                type="text"
                class="form-control"
                id="cc-expiration"
                placeholder=""
                required
              />
              <div class="invalid-feedback">Expiration date required</div>
            </div>

            <div class="col-md-3">
              <label for="cc-cvv" class="form-label">CVV</label>
              <input
                type="text"
                class="form-control"
                id="cc-cvv"
                placeholder=""
                required
              />
              <div class="invalid-feedback">Security code required</div>
            </div>
          </div>

          <hr class="my-4" />

          <a
            href="/success"
            class="w-100 btn btn-primary btn-lg"
            on:click={() => checkoutOrder()}
            type="submit">Bayar</a
          >
        </form>
      </div>
    </div>
  </main>

  <footer class="my-5 pt-5 text-muted text-center text-small">
    <p class="mb-1">&copy; 2017–2021 Company Name</p>
    <ul class="list-inline">
      <li class="list-inline-item"><a href="privacy">Privacy</a></li>
      <li class="list-inline-item"><a href="terms">Terms</a></li>
      <li class="list-inline-item"><a href="support">Support</a></li>
    </ul>
  </footer>
</div>

<style>
  .container {
    max-width: 960px;
  }
  a {
    color: rgb(92, 91, 91);
    cursor: pointer;
    text-decoration: none;
    position: relative;
    float: left;
    top: 50px;
    left: 60px;
    font-size: larger;
  }
  a.btn {
    color: white;
  }
</style>
