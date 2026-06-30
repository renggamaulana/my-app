<script>
  import { onMount } from "svelte";
  import axios from "axios";
  import Index from "./routes/Index.svelte";
  import { token, user, cart } from "./store";

  const API_URL = "http://localhost:8000/api";

  onMount(async () => {
    const activeToken = localStorage.getItem("token");
    if (activeToken) {
      token.set(activeToken);
      axios.defaults.headers.common["Authorization"] = `Bearer ${activeToken}`;

      try {
        const userRes = await axios.get(`${API_URL}/user`);
        user.set(userRes.data);

        const cartRes = await axios.get(`${API_URL}/carts`);
        cart.set(cartRes.data);
      } catch (err) {
        console.error("Error fetching user data on boot:", err);
        // If unauthorized/expired, clear token and local states
        token.set(null);
        user.set(null);
        cart.set([]);
        delete axios.defaults.headers.common["Authorization"];
      }
    }
  });

  // Keep axios Auth header in sync with token store updates
  token.subscribe((val) => {
    if (val) {
      axios.defaults.headers.common["Authorization"] = `Bearer ${val}`;
    } else {
      delete axios.defaults.headers.common["Authorization"];
    }
  });
</script>

<Index />

<style>
</style>
