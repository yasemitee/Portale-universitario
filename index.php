<?php
ini_set("display_errors", "On");
ini_set("error_reporting", E_ALL);
include_once('lib/functions.php');

/** 
 * --- Logica per la gestione del login ---
 * 
 */

// variabile per l'utente loggato
$logged = null;

// inizio della sessione: PRIMA di qualunque output html!
session_start();

// controlla il login
$error_msg = '';

if (isset($_POST) && isset($_POST['email']) && isset($_POST['password'])) {
  $logged = login($_POST['email'], $_POST['password']);
  if (is_null($logged)) {
    $error_msg = 'Credenziali errate, ripetere il login';
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
  <link rel="stylesheet" href="style/style.css">
</head>

<body style="background-image: url(img/sfondo-login.svg);">
  <?php
  // se l'utente non e' loggato, mostra form autenticazione
  if (!isset($logged)) {

  ?>
    <div class="container d-flex justify-content-center align-items-center p-3">
      <div class="w-md-50 h-auto bg-white p-5 rounded-4 shadow-lg">
        <h1 class="text-center mb-3">Portale universitario</h1>
        <p class="fs-6 text-center text-secondary">
          Scegli una delle opzioni e completa i campi
        </p>
        <nav>
          <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-login-tab" data-bs-toggle="tab" data-bs-target="#nav-login" type="button" role="tab" aria-controls="nav-login" aria-selected="true">
              Login
            </button>
            <button class="nav-link" id="nav-registrazione-tab" data-bs-toggle="tab" data-bs-target="#nav-registrazione" type="button" role="tab" aria-controls="nav-registrazione" aria-selected="false">
              Registrazione
            </button>
          </div>
        </nav>

        <div class="tab-content" id="nav-tabContent">
          <div class="tab-pane fade show active" id="nav-login" role="tabpanel" aria-labelledby="nav-login-tab" tabindex="0">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
              <label class="form-label mt-4">Email</label>
              <input type="email" class="form-control mb-3" name="email" placeholder="name@example.com" />
              <label class="form-label">Password</label>
              <input type="password" class="form-control mb-3" name="password" placeholder="inserisci la password" />

              <button type="submit" class="btn w-100 mt-3">Accedi</button>
            </form>
          </div>
          <div class="tab-pane fade" id="nav-registrazione" role="tabpanel" aria-labelledby="nav-registrazione-tab" tabindex="0">
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
              <div class="row mt-4">
                <div class="col-md-6">
                  <label for="nome" class="form-label">Nome</label>
                  <input type="nome" class="form-control mb-3" placeholder="Inserisci il nome" id="nome" />
                </div>
                <div class="col-md-6">
                  <label for="cognome" class="form-label">Cognome</label>
                  <input type="cognome" class="form-control mb-3" placeholder="Inserisci il cognome" id="cognome" />
                </div>
              </div>
              <label class="form-label">Email</label>
              <input type="email" class="form-control mb-3" placeholder="name@example.com" />
              <label class="form-label">Password</label>
              <input type="password" class="form-control mb-3" placeholder="Inserisci la password" />
              <label class="form-label">Tipologia utente</label>
              <select class="form-select mb-3" id="tipologia-utente" aria-label="Default select example">
                <option selected>Scegliere la tipologia di utente</option>
                <option value="1">Segreteria</option>
                <option value="2">Docente</option>
                <option value="3">Studente</option>
              </select>

              <!-- Campi aggiuntivi -->
              <div id="campi-aggiuntivi">
                <!-- I campi aggiuntivi verranno aggiunti qui dinamicamente -->
              </div>

              <button type="submit" class="btn w-100 mt-4">Registrati</button>
            </form>
          </div>

          <script>
            // Funzione per gestire il cambio della tipologia di utente
            function onChangeTipologiaUtente() {
              var tipologiaUtente = document.getElementById('tipologia-utente').value;
              var campiAggiuntivi = document.getElementById('campi-aggiuntivi');

              // Rimuovi i campi aggiuntivi esistenti
              while (campiAggiuntivi.firstChild) {
                campiAggiuntivi.firstChild.remove();
              }

              // Aggiungi i campi aggiuntivi in base alla tipologia di utente selezionata
              if (tipologiaUtente === '3') {
                // Caso Studente
                var corsoStudiLabel = document.createElement('label');
                corsoStudiLabel.innerText = 'Corso di studi';
                var corsoStudiInput = document.createElement('input');
                corsoStudiInput.type = 'text';
                corsoStudiInput.className = 'form-control mb-3';
                corsoStudiInput.placeholder = 'Inserisci il corso di studi';

                campiAggiuntivi.appendChild(corsoStudiLabel);
                campiAggiuntivi.appendChild(corsoStudiInput);
              } else if (tipologiaUtente === '2') {
                // Caso Docente
                var specializzazioneLabel = document.createElement('label');
                specializzazioneLabel.innerText = 'Specializzazione';
                var specializzazioneInput = document.createElement('input');
                specializzazioneInput.type = 'text';
                specializzazioneInput.className = 'form-control mb-3';
                specializzazioneInput.placeholder = 'Inserisci la specializzazione';

                campiAggiuntivi.appendChild(specializzazioneLabel);
                campiAggiuntivi.appendChild(specializzazioneInput);
              }
            }

            // Aggiungi l'evento onChange alla select della tipologia di utente
            document.getElementById('tipologia-utente').addEventListener('change', onChangeTipologiaUtente);
          </script>
          <?php
          // se c'e' messaggio di errore, stampalo
          if (!empty($error_msg)) {

          ?>
            <div class="alert alert-danger mt-3">
              <p><?php echo $error_msg; ?></p>
            </div>
          <?php
          }
          ?>
        <?php
        //se l'utente è loggato
      } else if (isset($logged)) {
        header("Location: studente.php");
        exit();
      }
        ?>
        </div>
      </div>

      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>