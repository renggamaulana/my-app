<script>
  import NavAdmin from "../components/Admin/Nav.svelte";
  import MenuAdmin from "../components/Admin/Menu.svelte";
  import FooterAdmin from "../components/Admin/Footer.svelte";
  import { API_URL, numberWithCommas } from "../utils/utils";
  import axios from "axios";

  let products = [];

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
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">
                DataTable with minimal features & hover style
              </h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table class="table">
                <thead>
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Gambar</th>
                    <th scope="col">Aksi</th>
                  </tr>
                </thead>
                {#each products as product, i}
                  <tbody>
                    <tr>
                      <th scope="row">{i + 1}</th>
                      <td>{product.name}</td>
                      <td>Rp. {numberWithCommas(product.price)}</td>
                      <td
                        ><img
                          src="./assets/img/{product.category
                            .name}/{product.img}"
                          class="card-img-top"
                          width="20"
                          alt="..."
                        /></td
                      >
                      <td>
                        <span class="badge bg-success">Edit</span>
                        <span class="badge bg-danger">Hapus</span>
                      </td>
                    </tr>
                  </tbody>
                {/each}
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </div>
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
  <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->

<!-- ./wrapper -->
<FooterAdmin />

<style>
  .card-img-top {
    height: 50px;
    width: 50px;
  }
</style>
