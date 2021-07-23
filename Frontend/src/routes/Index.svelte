<script>
  import router from "page";
  import Home from "../pages/Home.svelte";
  import Login from "../pages/Login.svelte";
  import Register from "../pages/Register.svelte";
  import Checkout from "../pages/Checkout.svelte";
  import Admin from "../pages/Admin.svelte";
  import Success from "../pages/Success.svelte";
  import ForgotPassword from "../pages/ForgotPassword.svelte";
  import Invoice from "../pages/Invoice.svelte";
  import Profile from "../pages/Profile.svelte";
  import Orders from "../pages/Orders.svelte";
  import Products from "../pages/Products.svelte";

  let page;

  router("/", () => (page = Home));
  router("/login", () => (page = Login));
  router("/register", () => (page = Register));
  router("/success", () => (page = Success));
  router("/checkout", () => (page = Checkout));
  router("/admin", () => (page = Admin));
  router("/forgot-password", () => (page = ForgotPassword));
  router("/invoice", () => (page = Invoice));
  router("/profile", () => (page = Profile));
  router("/orders", () => (page = Orders));
  router("/products", () => (page = Products));

  router.start();
</script>

<svelte:component this={page} />
