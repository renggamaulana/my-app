<script>
  import NavAdmin from "../components/Admin/Nav.svelte";
  import MenuAdmin from "../components/Admin/Menu.svelte";
  import FooterAdmin from "../components/Admin/Footer.svelte";
  import { API_URL, numberWithCommas } from "../utils/utils";
  import axios from "axios";

  let products = [];
  i = 0;

  axios
    .get(`${API_URL}products`)
    .then((res) => {
      products = res.data;
      console.log(products);
    })
    .catch((err) => {
      console.log(err);
    });
</script>

<NavAdmin />
<MenuAdmin />
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Data Produk</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="admin">Home</a></li>
            <li class="breadcrumb-item active">DataTables</li>
          </ol>
        </div>
      </div>
    </div>
    <!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <div class="card">
    <div class="card-body">Jl. Cilandak Timur No.30</div>
  </div>
</div>
<!-- /.content-wrapper -->

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->

<!-- ./wrapper -->
<FooterAdmin />
