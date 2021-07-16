<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="admin" class="brand-link">
    <img
      src="dist/img/AdminLTELogo.png"
      alt="AdminLTE Logo"
      class="brand-image img-circle elevation-3"
      style="opacity: .8"
    />
    <span class="brand-text font-weight-light">AdminLTE 3</span>
  </a>

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
        <a href="profile" class="d-block">Alexander Pierce</a>
      </div>
    </div>

    <!-- SidebarSearch Form -->
    <div class="form-inline">
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
    </div>

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
        <li class="nav-item menu-open">
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
                <i class="fa fa-user nav-icon" />
                <p>Profil</p>
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
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>
