<?php
ini_set("display_errors", "Off");
ini_set("error_reporting", E_ALL);
include_once('lib/functions.php');

// inizio della sessione: PRIMA di qualunque output html!
session_start();

// imposta la variabile $logged se esiste una sessione aperta
if (isset($_SESSION['user'])) {
  $logged = $_SESSION['user'];
}

// aggiorna la variabile di sessione
if (isset($logged)) {
  $_SESSION['user'] = $logged;
}

// se l'utente fa logout, inizializza $logged
if (isset($_GET) && isset($_GET['log']) && $_GET['log'] == 'del') {
  unset($_SESSION['user']);
  $logged = null;
  session_unset();
  session_destroy();
  header("Location: index.php");
  exit();
}

$corsi = getCorsi();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="style/style-pagine.css?<?php echo time(); ?>" />
</head>

<body>
  <div class="container-fluid">
    <div class="row flex-nowrap">
      <!-- Sidebar -->
      <?php
      include_once('lib/sidebar-studente.php');
      ?>
      <!-- Contenuto di destra -->
      <div id="content" class="col py-3 offset-1 offset-md-2 offset-sm-3">
        <!-- Informazioni sui corsi -->
        <div class="row mx-3 my-4">
          <h4 class="mb-4 text-uppercase">Informazioni sui corsi di laurea</h2>
            <h5 class="text-secondary">
              Questa sezione ti permette di scoprire i diversi corsi di laurea
              offerti dall'ateneo
            </h5>
            <div class="d-flex flex-column">
              <form class="form-inline input-group w-25 my-4" method="POST" action="./infoCorso.php">
                <input class="form-control mr-sm-2" type="search" name='cerca_corso' placeholder="Codice del corso" aria-label="Search">
                <button type="submit" class="btn custom-btn">
                  <i class="fas fa-search"></i>
                </button>
              </form>
              <?php
              if (isset($_SESSION['error'])) {
                echo '<div class="alert alert-danger w-25" role="alert">' . $_SESSION['error'] . '</div>';
              }
              ?>
            </div>
        </div>
        <div class="row row-cols-xl-3 g-4">
          <?php

          foreach ($corsi as $corso) {
          ?>
            <div class="col">
              <div class="card mx-auto shadow rounded" style="width: 19rem; height:20rem; border-style: none;">
                <div class="card-body">
                  <h5 class="card-title"><?php echo $corso['nome']; ?></h5>
                  <h6 class="card-subtitle mb-2 text-body-secondary"> Facoltà:
                    <?php echo $corso['facoltà']; ?>
                    <p class="card-text">Durata corso: <?php echo $corso['durata']; ?></p>
                  </h6>
                  <p class="card-text h-50" style="display: -webkit-box;-webkit-line-clamp: 6; -webkit-box-orient: vertical; overflow: hidden;"><?php echo $corso['descrizione']; ?></p>
                  <form method="POST" action="./infoCorso.php">
                    <input type="hidden" name="corso" value="<?php echo htmlspecialchars(json_encode($corso)); ?>">
                    <button type="submit" class="btn btn-link px-0">Leggi di più</button>
                  </form>
                </div>
              </div>
            </div>
          <?php
          }
          ?>
        </div>
      </div>
    </div>
  </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>