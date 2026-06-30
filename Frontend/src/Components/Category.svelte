<script>
  import { onMount } from "svelte";
  import axios from "axios";
  import { categories, selectedCategory } from "../store";

  const API_URL = "http://localhost:8000/api";

  onMount(async () => {
    try {
      const res = await axios.get(`${API_URL}/categories`);
      categories.set(res.data);
    } catch (err) {
      console.error("Error fetching categories:", err);
    }
  });

  function selectCategory(id) {
    selectedCategory.set(id);
  }

  // Helper to return Bootstrap icons depending on category name
  function getCategoryIcon(name) {
    const n = name.toLowerCase();
    if (n.includes("corn")) return "bi-egg-fried";
    if (n.includes("drink") || n.includes("minum")) return "bi-cup-straw";
    if (n.includes("snack") || n.includes("cemil")) return "bi-bag";
    if (n.includes("food") || n.includes("makan")) return "bi-egg-fried";
    return "bi-folder";
  }
</script>

<div class="category-section">
  <h5 class="section-title">Kategori</h5>
  <hr class="title-hr" />
  
  <div class="list-group list-group-flush shadow-sm rounded">
    <button
      type="button"
      class="list-group-item list-group-item-action {$selectedCategory === null ? 'active bg-warning text-dark border-warning' : ''}"
      on:click={() => selectCategory(null)}
    >
      <i class="bi bi-grid me-2"></i>Semua Menu
    </button>
    
    {#each $categories as cat}
      <button
        type="button"
        class="list-group-item list-group-item-action {$selectedCategory === cat.id ? 'active bg-warning text-dark border-warning' : ''}"
        on:click={() => selectCategory(cat.id)}
      >
        <i class="bi {getCategoryIcon(cat.name)} me-2"></i>{cat.name}
      </button>
    {/each}
  </div>
</div>

<style>
  .section-title {
    font-weight: bold;
    color: #2c3e50;
  }
  .title-hr {
    border-top: 2px solid #ff9f43;
    opacity: 0.8;
  }
  .list-group-item {
    font-size: 0.95rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
  }
  .list-group-item:hover {
    background-color: #f8f9fa;
    border-left-color: #ff9f43;
    padding-left: 20px;
  }
  .list-group-item.active {
    font-weight: 600;
    border-left-color: #d35400;
  }
</style>
