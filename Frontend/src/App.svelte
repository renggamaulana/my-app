<script>
  let search = "";
  let loading = false;
  const API_URL = "https://jsonplaceholder.typicode.com/posts";

  async function formSubmitted() {
    console.log(search);
    loading = true;
    const url = `${API_URL} ${search}`;
    const response = await fetch(url);
    const json = await response.json();
    console.log(json);
  }
</script>

<form on:submit|preventDefault={formSubmitted}>
  <label for="search">Search</label>
  <input bind:value={search} id="search" name="search" />
  <button type="submit">GO!</button>
</form>
{#if loading}
  <img
    src="https://media.giphy.com/media/xT9DPldJHzZKtOnEn6/giphy.gif"
    alt="loading"
  />
{/if}

<style>
  img {
    width: 400px;
    margin: 20px auto;
    display: flex;
    justify-content: center;
  }
</style>
