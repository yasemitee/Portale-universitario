<?php
ini_set("display_errors", "On");
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


if (isset($_POST['nome'], $_POST['cognome'], $_POST['email'], $_POST['password'], $_POST['inserimento_utente'])) {
  $nome = $_POST['nome'];
  $cognome = $_POST['cognome'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $tipo = $_POST['inserimento_utente'];

  if ($tipo == "inserimento_studente") {
    if (isset($_POST['matricola'], $_POST['corso_studi'])) {
      $matricola = $_POST['matricola'];
      $corso_studi = $_POST['corso_studi'];
      $registrazione = registraStudente($nome, $cognome, $email, $password, $matricola, $corso_studi);
    }
  } elseif ($tipo == 'inserimento_docente') {
    if (isset($_POST['specializzazione'])) {
      $specializzazione = $_POST['specializzazione'];
      $registrazione = registraDocente($nome, $cognome, $email, $password, $specializzazione);
    }
  }
}


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
      include_once('lib/sidebar-segreteria.php');
      ?>
      <!-- Contenuto di destra -->
      <div id="content" class="col py-3 offset-1 offset-md-3 offset-lg-2 offset-sm-3">
        <!-- Informazioni generali -->
        <div class="row mx-5 my-4 p-3 shadow rounded" id="informazioni">
          <h2 class="mb-4">Informazioni personali</h2>
          <?php
          $info = getInfoSegretario($_SESSION['id']);
          ?>
          <div class="d-flex mb-1">
            <label class="fs-6"><strong>Nome: </strong><?php echo ($info['nome']); ?></label>
          </div>
          <div class="d-flex mb-1">
            <label class="fs-6 "><strong>Cognome: </strong><?php echo ($info['cognome']); ?></label>
          </div>
          <div class="d-flex mb-1">
            <label class="fs-6 "><strong>E-mail: </strong><?php echo ($info['email']); ?></label>
          </div>
        </div>
        <!-- Iscrizione utenti -->
        <div class="row mx-5 my-4 p-3 shadow rounded" id="inserimento_utente">
          <h2 class="mb-4">Inserimento nuovo utente</h2>
          <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#inserimento_utente" ?>">
            <input type="hidden" name="inserimento_utente" value="inserimento_studente">
            <button type="submit" class="btn btn-light my-1">Inserisci studente</button>
          </form>
          <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#inserimento_utente" ?>">
            <input type="hidden" name="inserimento_utente" value="inserimento_docente">
            <button type="submit" class="btn btn-light my-1">Inserisci docente</button>
          </form>
          <?php
          if (isset($_POST) && isset($_POST['inserimento_utente'])) {
            $tipo = $_POST['inserimento_utente'];
            if ($tipo == 'inserimento_studente') {
          ?>
              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <input type="hidden" name="inserimento_utente" value="inserimento_studente">
                <div class="row mt-4">
                  <div class="col-md-6">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control mb-3" placeholder="Inserisci il nome" name="nome" />
                  </div>
                  <div class="col-md-6">
                    <label for="cognome" class="form-label">Cognome</label>
                    <input type="text" class="form-control mb-3" placeholder="Inserisci il cognome" name="cognome" />
                  </div>
                  <div class="row">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control mb-3" placeholder="name@example.com" name='email' />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control mb-3" placeholder="Inserisci la password" name='password' />
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Matricola</label>
                  <input type="text" class="form-control mb-3" placeholder="Inserisci la matricola" name="matricola" />
                </div>
                <div class="col-md-6">
                  <label class="form-label">Corso di studi</label>
                  <input type="text" class="form-control mb-3" placeholder="Inserisci il corso di studi" name="corso_studi" />
                </div>
                <button type="submit" class="btn custom-btn mt-4">Conferma</button>
              </form>
            <?php
            } elseif ($tipo == 'inserimento_docente') {
            ?>
              <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                <input type="hidden" name="inserimento_utente" value="inserimento_docente">
                <div class="row mt-4">
                  <div class="col-md-6">
                    <label for="nome" class="form-label">Nome</label>
                    <input type="text" class="form-control mb-3" placeholder="Inserisci il nome" name="nome" />
                  </div>
                  <div class="col-md-6">
                    <label for="cognome" class="form-label">Cognome</label>
                    <input type="text" class="form-control mb-3" placeholder="Inserisci il cognome" name="cognome" />
                  </div>
                  <div class="row">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control mb-3" placeholder="name@example.com" name='email' />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control mb-3" placeholder="Inserisci la password" name='password' />
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Specializzazione</label>
                  <input type="text" class="form-control mb-3" placeholder="Inserisci la matricola" name="specializzazione" />
                </div>
                <button type="submit" class="btn custom-btn mt-4">Conferma</button>
              </form>
          <?php
            }
          }
          ?>
          <?php
          if ((isset($registrazione))) {
            if ($registrazione == true) {
          ?>
              <div class="alert alert-success mt-3">
                <p>Inserimento avvenuto con successo!</p>
              </div>
            <?php
            } else {
            ?>
              <div class="alert alert-danger mt-3">
                <p>Registrazione non riuscita, ti invitiamo a controllare i campi</p>
              </div>
          <?php
            }
          }
          ?>
        </div>
        <!-- Gestione utenti -->
        <div class="row mx-5 my-4 p-3 shadow rounded" id="informazioni">
          <h2 class="mb-4">Gestione utenti</h2>
          <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <button type="submit" class="btn custom-btn my-1">Gestione studenti</button>
          </form>
          <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <button type="submit" class="btn custom-btn my-1">Gestione docenti</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>