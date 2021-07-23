<script>
  import axios from "axios";
  import { API_URL } from "../utils/utils.js";
  import { createEventDispatcher } from "svelte";

  const dispatch = createEventDispatcher();

  function changeCategory(value) {
    dispatch("messageCategory", {
      text: value,
    });
  }

  let categories = [];

  let isActive = false;

  axios
    .get(`${API_URL}categories`)
    .then((res) => {
      categories = res.data;
    })
    .catch((err) => {
      console.log(err);
    });
</script>

<h4>Kategori</h4>
<hr />
<ul class="list-group">
  {#each categories as category (category.id)}
    <li class="list-group-item" on:click={changeCategory(category.name)}>
      <h6 class:active={isActive}>
        {#if category.name === "Makanan"}
          <i class="fas fa-utensils mx-2" />
          {category.name}
        {:else if category.name === "Minuman"}
          <i class="fas fa-coffee mx-2" />
          {category.name}
        {:else if category.name === "Corndog"}
          <i class="fas fa-drumstick-bite mx-2" />
          {category.name}
        {:else if category.name === "Cemilan"}
          <i class="fas fa-bread-slice mx-2" />
          {category.name}
        {/if}
      </h6>
    </li>
  {/each}
</ul>

<style>
  .list-group:hover {
    cursor: pointer;
  }
  .active {
    background: rgb(216, 148, 22);
    color: white;
  }
</style>
