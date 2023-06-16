<div class="sidebar col-auto col-sm-3 col-md-3 col-lg-2 px-sm-2 px-0 position-fixed" id="sidebar">
  <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
    <a href="#" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
      <span class="fs-5 d-none d-sm-inline">Portale UNI</span>
    </a>
    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start">
      <li class="nav-item">
        <a href="docente.php" class="nav-link align-middle pt-3 px-0 pt-5 text-white hover-underline-animation">
          <i class="fa-solid fa-house"></i>
          <span class="ms-1 d-none d-sm-inline">Home</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="docente.php#insegnamenti" class="nav-link align-middle px-0 pt-3 text-white hover-underline-animation">
          <i class="fa-solid fa-book"></i>
          <span class="ms-1 d-none d-sm-inline">Insegnamenti</span>
        </a>
      </li>
      <li class="nav-item">
        <a href="docente.php#gestione-esami" class="nav-link align-middle pt-3 px-0 text-white hover-underline-animation">
          <i class="fa-solid fa-toolbox"></i>
          <span class="ms-1 d-none d-sm-inline">Gestione esami</span>
        </a>
      </li>
      <li class="nav-item">
        <?php
        $logout_link = $_SERVER['PHP_SELF'] . "?log=del";
        ?>
        <a href="<?php echo ($logout_link); ?>" class="nav-link align-middle px-0 pt-5 ">
          <i class="fs-4 bi-house"></i>
          <span class="ms-1 d-none d-sm-inline text-danger ">Logout</span>
        </a>
      </li>
    </ul>
    <div class="dropdown pb-4">
      <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa-solid fa-user px-2"></i>
        <span class="d-none d-sm-inline mx-1"><?php echo ($_SESSION['nome']) ?><br />
          <?php echo ($_SESSION['cognome']); ?></span>
      </a>
      <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
        <li><a class="dropdown-item" href="./profile.php">Profilo</a></li>
        <li><a class="dropdown-item" href="./cambiaPassword.php">Cambia password</a></li>
      </ul>
    </div>
  </div>
</div>