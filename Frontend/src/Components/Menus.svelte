<script>
    import Home from "../pages/Home.svelte"
</script>


<h4>Daftar Menu</h4>
<hr />
<div class="row">
  {#each menus as menu}
    <h3>{menu.name}</h3>
  {/each}
</div>
</div>