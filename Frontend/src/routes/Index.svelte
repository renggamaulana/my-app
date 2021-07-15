<script>
  import router from "page";
  import Home from "../pages/Home.svelte";
  import Login from "../pages/Login.svelte";
  import Register from "../pages/Register.svelte";
  import Checkout from "../pages/Checkout.svelte";
  import Admin from "../pages/Admin.svelte";
  import Order from "../pages/Order.svelte";
  import ForgotPassword from "../pages/ForgotPassword.svelte";
  import Invoice from "../pages/Invoice.svelte";
  import Profile from "../pages/Profile.svelte";

  let page;

  router("/", () => (page = Home));
  router("/login", () => (page = Login));
  router("/register", () => (page = Register));
  router("/order", () => (page = Order));
  router("/checkout", () => (page = Checkout));
  router("/admin", () => (page = Admin));
  router("/forgot-password", () => (page = ForgotPassword));
  router("/invoice", () => (page = Invoice));
  router("/profile", () => (page = Profile));

  router.start();
</script>

<svelte:component this={page} />
