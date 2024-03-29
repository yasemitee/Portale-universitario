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

if (isset($_POST['rimuovi_appello'])) {
  $id_appello = $_POST['rimuovi_appello'];
  $rimozione = removeAppello($id_appello);
}

if ($_SESSION['tipo_utente'] != 'docente') {
  header('Location: ' . $_SESSION['tipo_utente'] . '.php');
}

if (isset($_POST['codice_insegnamento']) && isset($_POST['codice_esame']) && isset($_POST['nome_esame']) && isset($_POST['corso_studi'])) {
  $codice_insegnamento = $_POST['codice_insegnamento'];
  $corso_studi = $_POST['corso_studi'];
  $nome = $_POST['nome_esame'];
  $codice_esame = $_POST['codice_esame'];
  $err_creazione = creaNuovoEsame($codice_esame, $nome, $codice_insegnamento, $corso_studi);
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
      include_once('lib/sidebar-docente.php');
      ?>
      <!-- Contenuto di destra -->
      <div id="content" class="col py-3 offset-1 offset-md-3 offset-lg-2 offset-sm-3">
        <!-- Informazioni generali -->
        <div class="row mx-3 mx-md-5 my-4 p-3 shadow rounded" id="informazioni_personali">
          <h6 class="mb-4 text-uppercase">Informazioni personali</h6>
          <?php
          $info = getInfoDocente($_SESSION['user']);
          ?>
          <div class="d-flex flex-column">
            <div class="mb-1">
              <label class="fs-2"><strong><?php echo ($info['nome'] . ' ' . $info['cognome']); ?></strong></label>
            </div>
            <div class="mb-1">
              <label class="fs-6 matricola"><strong>#<?php echo ($info['id']); ?></strong></label>
            </div>
          </div>
          <div class="d-flex mb-1 mt-4">
            <label class="fs-6 "><strong>Email: </strong><?php echo ($info['email']); ?></label>
          </div>
          <div class="d-flex mb-1">
            <label class="fs-6 "><strong>Specializzazione: </strong><?php echo ($info['specializzazione']); ?></label>
          </div>
        </div>
        <!-- Insegnamenti del docente -->
        <div class="row mx-3 mx-md-5 my-4 p-3 shadow rounded" id="insegnamenti">
          <h4 class="mb-4 text-uppercase">Insegnamenti di cui sei repsonsabile</h4>
          <?php
          $insegnamenti = getInsegnamentiDocete($_SESSION['id']);

          if (!empty($insegnamenti)) {
            foreach ($insegnamenti as $insegnamento) {
              $numero_studenti = numeroStudentiInsegnamento($insegnamento['codice'], $insegnamento['corso_studi'])
          ?>
              <div class="card my-4 border" style="max-width: 540px; border-style: none;">
                <div class="row g-0">
                  <div class="col-md-5 py-3 py-md-3 px-3">
                    <h5 class="card-title mb-3"><?php echo $insegnamento['nome'] ?></h5>
                    <h6 class="card-text"><strong>Corso di studi: </strong><?php echo $insegnamento['corso_studi'] ?></h6>
                    <h6 class="card-text"><strong>Anno: </strong><?php echo $insegnamento['anno'] ?></h6>
                    <h6 class="card-text"><strong>N.Studenti: </strong><?php echo $numero_studenti ?></h6>
                  </div>
                  <div class="col-md-7 mt-md-0 mt-3 py-md-5 pb-3 px-3">
                    <h5 class="card-title">Descrizione:</h5>
                    <h6 class="card-text"><?php echo $insegnamento['descrizione'] ?> </h6>
                  </div>
                </div>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#insegnamenti" ?>">
                  <input type="hidden" name="inserimento" value="<?php echo $insegnamento['codice'] ?>">
                  <button type="submit" class="btn btn-light mt-1 mb-3">> Inserisci esame</button>
                </form>
              </div>
              <?php
              if (isset($_POST) && isset($_POST['inserimento'])) {
                if ($_POST['inserimento'] == $insegnamento['codice']) {
              ?>
                  <form class="border-bottom pt-1 pb-4 mt-4" action="<?php echo $_SERVER['PHP_SELF'] . '#inserimenti'; ?>" id="inserimento_corso" method="POST">
                    <h6 class="mb-2 text-uppercase">Inserimento corso</h6>
                    <h6 class="text-secondary">Completare i campi per inserire un nuovo esame</h6>
                    <input type="hidden" name="inserimento" value="inserimento_esame">
                    <div class="row mt-4">
                      <div class="col-md-12">
                        <label for="cognome" class="form-label">Codice</label>
                        <input type="text" class="form-control mb-3" placeholder="Inserisci il codice" name="codice_esame" />
                      </div>
                      <div class="col-md-12">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control mb-3" placeholder="Inserisci il nome" name="nome_esame" />
                      </div>
                      <button type="submit" class="btn custom-btn mb-4">Conferma</button>
                      <input type="hidden" name="corso_studi" value="<?php echo $insegnamento['corso_studi'] ?>">
                      <input type="hidden" name="codice_insegnamento" value="<?php echo $insegnamento['codice'] ?>">
                    </div>
                  </form>

                <?php
                }
                ?>
            <?php
              }
            }
            if ($err_creazione == '' && $_POST['inserimento'] == 'inserimento_esame') {
              echo '<div class="alert alert-success mt-3">Esame inserito con successo</div>';
            } elseif (isset($_POST['codice_insegnamento'])) {
              echo '<div class="alert alert-danger mt-3">' . $err_creazione . '</div>';
            }
          } else {
            ?>
            <div class="alert alert-primary mt-3">Nessun insegnamento disponibile</div>
          <?php
          }
          ?>
        </div>
        <!-- Calendario esami -->
        <div class="row mx-3 mx-md-5 my-4 p-3 shadow rounded" id="gestione-esami">
          <h4 class="mb-4 text-uppercase">Calendario appelli</h4>
          <?php
          foreach ($insegnamenti as $insegnamento) {

            $esami = getEsameByInsegnamento($insegnamento['codice']);
            foreach ($esami as $esame) {
              $appelli = getAppelliEsame($esame);
          ?>
              <div class=" my-4 p-3" id="esami">
                <h6 class="mb-4 text-uppercase">Appelli di <strong><?php echo $insegnamento['nome'] ?></strong>
                  <br><strong class="text-secondary">C.esame: <?php echo $esame ?></strong>
                </h6>

                <?php
                if (!empty($appelli)) {
                ?>
                  <table class="table mx-2 table-borderless align-middle table-hover table-responsive">
                    <thead>
                      <tr>
                        <th scope="col">Nome esame</th>
                        <th scope="col">Data appello</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      foreach ($appelli as $appello) { ?>
                        <tr>
                          <td><?php echo $appello['nome']; ?></td>
                          <td><?php echo $appello['data']; ?></td>
                          <td>
                            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#gestione-esami" ?>">
                              <input type="hidden" name="rimuovi_appello" value="<?php echo $appello['id_appello']; ?>">
                              <button type="submit" class="btn btn-light ">Rimuovi appello</button>
                            </form>
                          </td>
                          <td>
                            <form method="GET" action="./registraVoti.php">
                              <input type="hidden" name="codice_esame" value="<?php echo $esame; ?>">
                              <input type="hidden" name="id_appello" value="<?php echo $appello['id_appello']; ?>">
                              <button type="submit" class="btn btn-light">Registra voti</button>
                            </form>
                          </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                  </table>
                <?php } else { ?>
                  <div class="alert alert-primary mt-3">Non è stato programmato nessun appello</div>
                <?php }
                ?>
                <form method="GET" action="./inserimentoAppello.php">
                  <input type="hidden" name="codice_esame" value="<?php echo $esame; ?>">
                  <input type="hidden" name="corso_studi" value="<?php echo $insegnamento['corso_studi']; ?>">
                  <button type="submit" class="btn custom-btn my-2 my-sm-0">Inserisci un nuovo appello</button>
                </form>
              </div>
            <?php
            }
          }

          if (isset($_POST['rimuovi_appello']) && empty($rimozione)) {
            ?>
            <div class="alert alert-success alert-dismissible fade show mt-3">Appello rimosso con successo!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
          <?php
          } elseif (isset($_POST['rimuovi_appello'])) {
          ?>
            <div class="alert alert-danger alert-dismissible fade show mt-3"><i class="fa-solid fa-triangle-exclamation me-2"></i>Qualcosa è andato storto nella rimozione<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
          <?php
          }
          ?>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
</body>

</html>