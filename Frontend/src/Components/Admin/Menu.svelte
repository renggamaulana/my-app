<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img
          src="dist/img/user2-160x160.jpg"
          class="img-circle elevation-2"
          alt="User"
        />
      </div>
      <div class="info">
        <a href="profile" class="d-block">Admin</a>
      </div>
    </div>

    <!-- SidebarSearch Form -->
    <!-- <div class="form-inline">
      <div class="input-group" data-widget="sidebar-search">
        <input
          class="form-control form-control-sidebar"
          type="search"
          placeholder="Search"
          aria-label="Search"
        />
        <div class="input-group-append">
          <button class="btn btn-sidebar">
            <i class="fas fa-search fa-fw" />
          </button>
        </div>
      </div>
    </div> -->

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul
        class="nav nav-pills nav-sidebar flex-column"
        data-widget="treeview"
        role="menu"
        data-accordion="false"
      >
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="admin" class="nav-link active">
            <i class="nav-icon fas fa-tachometer-alt" />
            <p>
              Dashboard
              <i class="right fas fa-angle-left" />
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="profile" class="nav-link ">
                <i class="fas fa-users nav-icon" />
                <p>Users</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="products" class="nav-link">
                <i class="fa fa-cubes nav-icon" />
                <p>Produk</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="orders" class="nav-link">
                <i class="fa fa-shopping-cart nav-icon" />
                <p>Pesanan</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="invoice" class="nav-link">
                <i class="fa fa-credit-card nav-icon" />
                <p>Invoice</p>
              </a>
            </li>
          </ul>
        </li>

        <!-- Data Customer -->
        <li class="nav-item">
          <a href="" class="nav-link">
            <i class="nav-icon fas fa-user-tag" />
            <p>
              Pelanggan
              <i class="fas fa-angle-left right" />
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="../mailbox/mailbox.html" class="nav-link">
                <i class="far fa-circle nav-icon" />
                <p>Data Pelanggan</p>
              </a>
            </li>
          </ul>
        </li>
        <!-- End of Data Customer -->

        <!-- Data Master -->
        <li class="nav-item">
          <a href="" class="nav-link">
            <i class="nav-icon fas fa-database" />
            <p>
              Data Master
              <i class="fas fa-angle-left right" />
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="products" class="nav-link">
                <i class="far fa-circle nav-icon" />
                <p>Produk</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../mailbox/compose.html" class="nav-link">
                <i class="far fa-circle nav-icon" />
                <p>Kategori</p>
              </a>
            </li>
          </ul>
        </li>
        <!-- End of Data Master -->

        <!-- Transaksi -->
        <li class="nav-item">
          <a href="" class="nav-link">
            <i class="nav-icon fas fa-expand-alt" />
            <p>
              Transaksi
              <i class="fas fa-angle-left right" />
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="invoice" class="nav-link">
                <i class="far fa-circle nav-icon" />
                <p>Pesanan Baru</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../mailbox/compose.html" class="nav-link">
                <i class="far fa-circle nav-icon" />
                <p>Perlu di cek</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../mailbox/compose.html" class="nav-link">
                <i class="far fa-circle nav-icon" />
                <p>Perlu di kirim</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../mailbox/compose.html" class="nav-link">
                <i class="far fa-circle nav-icon" />
                <p>Pesanan di kirim</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../mailbox/compose.html" class="nav-link">
                <i class="far fa-circle nav-icon" />
                <p>Pesanan selesai</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../mailbox/compose.html" class="nav-link">
                <i class="far fa-circle nav-icon" />
                <p>Pesanan di batalkan</p>
              </a>
            </li>
          </ul>
        </li>
        <!-- End of Transaksi -->

        <!-- Lainnya -->
        <li class="nav-item">
          <a href="" class="nav-link">
            <i class="nav-icon fas fa-file" />
            <p>
              Lainnya
              <i class="fas fa-angle-left right" />
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="address" class="nav-link">
                <i class="far fa-circle nav-icon" />
                <p>Alamat</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../mailbox/compose.html" class="nav-link">
                <i class="far fa-circle nav-icon" />
                <p>No Rekening</p>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
