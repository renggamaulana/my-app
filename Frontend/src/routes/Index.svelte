<script>
  import page from "page";
  import Home from "../pages/Home.svelte";
  import Login from "../pages/Login.svelte";
  import Register from "../pages/Register.svelte";
  import Checkout from "../pages/Checkout.svelte";
  import AdminDashboard from "../pages/AdminDashboard.svelte";

  let activeComponent = Home;

  page("/", () => {
    activeComponent = Home;
  });

  page("/login", () => {
    activeComponent = Login;
  });

  page("/register", () => {
    activeComponent = Register;
  });

  page("/checkout", () => {
    activeComponent = Checkout;
  });

  page("/admin", () => {
    activeComponent = AdminDashboard;
  });

  page.start();
</script>

<svelte:component this={activeComponent} />

<style>
</style>
