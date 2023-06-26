<?php
ini_set("display_errors", "Off");
ini_set("error_reporting", E_ALL);
include_once('lib/functions.php');

// variabile per l'utente loggato
$logged = null;

// inizio della sessione: PRIMA di qualunque output html!
session_start();

// controlla il login
$error_msg = '';

if (isset($_POST) && isset($_POST['email_login']) && isset($_POST['password_login'])) {
  $logged = login($_POST['email_login'], $_POST['password_login']);
  if (is_null($logged)) {
    $error_msg = 'Credenziali non valide';
  }
}

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
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Portale universitario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous" />
  <link rel="stylesheet" href="style/style.css?<?php echo time(); ?>">
</head>

<body style="background-image: url(img/sfondo-scuro-1.svg);">
  <?php
  // se l'utente non e' loggato, mostra form autenticazione
  if (!isset($logged)) {

  ?>
    <div class="container d-flex justify-content-center align-items-center p-3">
      <div class="w-md-50 h-auto bg-white p-5 rounded-4 shadow-lg">
        <h1 class="text-center mb-3">Portale universitario</h1>
        <p class="fs-6 text-center text-secondary">
          Completa i campi per continuare
        </p>

        <div class="tab-content" id="nav-tabContent">
          <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <label class="form-label mt-4">Email</label>
            <input type="email" class="form-control mb-3" name="email_login" placeholder="nome@esempio.com" />
            <label class="form-label">Password</label>
            <input type="password" class="form-control mb-3" name="password_login" placeholder="inserisci la password" />

            <button type="submit" class="btn w-100 mt-3">Accedi</button>
          </form>
        </div>
        <?php
        // se c'e' messaggio di errore, stampalo
        if (!empty($error_msg)) {

        ?>
          <div class="alert alert-danger alert-dismissible fade show mt-3">
            <p><?php echo $error_msg; ?></p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
          </div>
        <?php
        }
        ?>
      <?php
      //se l'utente è loggato
    } else if (isset($logged)) {
      header('Location: ' . $_SESSION['tipo_utente'] . '.php');
      exit();
    }
      ?>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>