<div class="sidebar col-auto col-sm-3 col-md-3 col-lg-2 px-sm-2 px-0 position-fixed" id="sidebar">
  <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
    <a href="studente.php" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-5 d-none d-sm-inline mt-2" style="letter-spacing: 2px;"><strong>Portale UNI</strong></span>
    </a>
    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start">
      <li class="nav-item">
        <a href="studente.php" class="nav-link align-middle pt-3 px-0 pt-5 text-white hover-underline-animation">
          <i class="fa-solid fa-house"></i>
          <span class="ms-1 d-none d-sm-inline">Home</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="studente.php#carriera" class="nav-link align-middle px-0 pt-3 text-white hover-underline-animation">
          <i class="fa-solid fa-book"></i>
          <span class="ms-1 d-none d-sm-inline">Carriera</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="studente.php#esami" class="nav-link align-middle pt-3 px-0 text-white hover-underline-animation">
          <i class="fa-solid fa-calendar-days"></i>
          <span class="ms-1 d-none d-sm-inline">Esami</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="./corsi.php" class="nav-link align-middle pt-3 px-0 text-white hover-underline-animation">
          <i class="fa-solid fa-graduation-cap"></i>
          <span class="ms-1 d-none d-sm-inline">Corsi di laurea</span>
        </a>
      </li>
      <li class="nav-item">
      </li>
    </ul>
    <div class="dropdown pb-4 my-4 my-sm-0">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-user d-none d-sm-inline"></i>
        <span class="d-none d-sm-inline mx-sm-2 mx-md-3"><?php echo ($_SESSION['nome']) ?><br />
          <?php echo ($_SESSION['cognome']); ?></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-light text-small shadow">
        <li><a class="dropdown-item" href="./profile.php">Profilo</a></li>
        <li><a class="dropdown-item" href="./cambiaPassword.php">Cambia password</a></li>
        <li> <?php
              $logout_link = $_SERVER['PHP_SELF'] . "?log=del";
              ?>
          <a href="<?php echo ($logout_link); ?>" class="dropdown-item text-danger">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z" />
              <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z" />
            </svg>
            <span>Logout</span>
          </a>
          <?php
          ?>
        </li>
      </ul>
    </div>
  </div>
</div>