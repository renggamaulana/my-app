<script>
</script>

<body class="hold-transition register-page">
  <div class="register-box">
    <div class="register-logo">
      <a href="../../index2.html"><b>Registrasi</b></a>
    </div>

    <div class="card">
      <div class="card-body register-card-body">
        <p class="login-box-msg">Daftar anggota baru</p>

        <form action="../../index.html" method="post">
          <div class="input-group mb-3">
            <input
              type="text"
              class="form-control"
              placeholder="Nama lengkap"
            />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user" />
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="email" class="form-control" placeholder="Email" />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope" />
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input
              type="password"
              class="form-control"
              placeholder="Password"
            />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock" />
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input
              type="password"
              class="form-control"
              placeholder="Ketik ulang password"
            />
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock" />
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input
                  type="checkbox"
                  id="agreeTerms"
                  name="terms"
                  value="agree"
                />
                <label for="agreeTerms">
                  Saya setuju dengan <a href="#">persyaratan </a>
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block"
                >Daftar</button
              >
            </div>
            <!-- /.col -->
          </div>
        </form>

        <a href="login" class="text-center">Saya sudah menjadi anggota</a>
      </div>
      <!-- /.form-box -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.register-box -->
</body>
