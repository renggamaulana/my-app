<script>
  import Navbar from "./Components/Navbar.svelte";

  let name = "Yuko";
  let skills = {
    nameSkill: ["SvelteJs", "ReactJS", "VueJS", "RestfulAPI"],
    rating: 80,
  };

  let imageURL = "./img/saya.jpg";
</script>

<Navbar />
<main>
  <h2>Welcome {name}</h2>
  <p>
    Skills : {skills.nameSkill[0]} <br />
    Rating : {skills.rating}
  </p>
  <img src={imageURL} alt="saya" width="200" />
</main>

<style>
</style>
