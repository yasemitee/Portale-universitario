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

//INSERIMENTO UTENTE
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
//RICERCA UTENTE
if (isset($_POST['cerca_utente'])) {
  $email = $_POST['cerca_utente'];
  $info_utente = getInfoStudente($email);

  if (!isset($info_utente)) {
    $info_utente = getInfoDocente($email);
    $insegnamenti_docente = getInsegnamentiDocete($info_utente['id']);
  }
  $_SESSION['info_utente'] = $info_utente;
} elseif (!isset($_POST['carriera_valida']) && !isset($_POST['carriera_completa']) && !isset($_POST['carriera_completa_storico']) && !isset($_POST['carriera_valida_storico'])) {
  unset($_SESSION['info_utente']);
}


//ELIMINA STUDENTE
if (isset($_POST['elimina_studente'])) {
  $id_utente = $_POST['elimina_studente'];
  $rimozione = removeStudente($id_utente);
}
//ELIMINA DOCENTE
if (isset($_POST['elimina_docente'])) {
  $id_utente = $_POST['elimina_docente'];
  $rimozione = removeDocente($id_utente);
}

//INSERIMENTO CORSO/INSEGNAMENTO
if (isset($_POST['codice'], $_POST['nome'], $_POST['descrizione'], $_POST['inserimento'])) {
  $codice = $_POST['codice'];
  $nome = $_POST['nome'];
  $descrizione = $_POST['descrizione'];
  $tipo = $_POST['inserimento'];
  if ($tipo == "inserimento_corso") {
    if (isset($_POST['facoltà'], $_POST['durata'])) {
      $facoltà = $_POST['facoltà'];
      $durata = $_POST['durata'];
      $inserimento = inserisciCorso($codice, $nome, $descrizione, $facoltà, $durata);
    }
  } elseif ($tipo == 'inserimento_insegnamento') {
    if (isset($_POST['anno'], $_POST['corso_studi'], $_POST['docente_responsabile'])) {
      $anno = $_POST['anno'];
      $corso_studi = $_POST['corso_studi'];
      $docente_responsabile = $_POST['docente_responsabile'];
      $propedeuticità = $_POST['propedeuticità'];
      $inserimento = inserisciInsegnamento($codice, $nome, $corso_studi, $descrizione, $anno, $docente_responsabile, $propedeuticità);
    }
  }
}

//RICERCA CORSO
if (isset($_POST['cerca_corso'])) {
  $codice_corso = $_POST['cerca_corso'];
  $cc = strtoupper($codice_corso);
  $info_corso = getInfoCorso($cc);
}

//RICERCA INSEGNAMENTO
if (isset($_POST['cerca_insegnamento'])) {
  $codice_insegnamento = $_POST['cerca_insegnamento'];
  $ci = strtoupper($codice_insegnamento);
  $info_insegnamento = getInfoInsegnamento($ci);
}


//ELIMINA CORSO
if (isset($_POST['elimina_corso'])) {
  $codice_corso = $_POST['elimina_corso'];
  $rimozione = removeCorso($codice_corso);
}

//ELIMINA INSEGNAMENTO
if (isset($_POST['elimina_insegnamento'])) {
  $codice_insegnamento = $_POST['elimina_insegnamento'];
  $rimozione = removeInsegnamento($codice_insegnamento);
}

if ($_SESSION['tipo_utente'] != 'segreteria') {
  header('Location: ' . $_SESSION['tipo_utente'] . '.php');
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
        <div class="row mx-5 my-4 p-3 shadow rounded" id="informazioni_personali">
          <h6 class="mb-4 text-uppercase">Informazioni personali</h6>
          <?php
          $info = getInfoSegretario($_SESSION['user']);
          ?>
          <div class="d-flex mb-1">
            <div class="mb-1">
              <label class="fs-2"><strong><?php echo ($info['nome'] . ' ' . $info['cognome']); ?></strong></label>
            </div>
          </div>
          <div class="d-flex mb-1">
            <label class="fs-6 "><strong>E-mail: </strong><?php echo ($info['email']); ?></label>
          </div>
        </div>
        <div class="row mx-5 my-4 p-3 shadow rounded" id="sezione_utenti">
          <h3 class="mb-4 text-uppercase">Gestione utenti</h3>
          <!-- Iscrizione utenti -->
          <div class="row " id="inserimento_utente">
            <h6 class="mb-4 text-uppercase">Inserimento nuovo utente</h6>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#inserimento_utente" ?>">
              <input type="hidden" name="inserimento_utente" value="inserimento_studente">
              <button type="submit" class="btn btn-light my-1">> Inserisci studente</button>
            </form>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#inserimento_utente" ?>">
              <input type="hidden" name="inserimento_utente" value="inserimento_docente">
              <button type="submit" class="btn btn-light my-1">> Inserisci docente</button>
            </form>
            <?php
            if (isset($_POST) && isset($_POST['inserimento_utente'])) {
              $tipo = $_POST['inserimento_utente'];
              if ($tipo == 'inserimento_studente') {
            ?>
                <form class="border-top border-bottom py-4 mt-4" action="<?php echo $_SERVER['PHP_SELF'] . '#form_inserimento_utente'; ?>" id="form_inserimento_utente" method="POST">
                  <input type="hidden" name="inserimento_utente" value="inserimento_studente">
                  <h6 class="mb-2 text-uppercase">Inserimento studente</h6>
                  <h6 class="text-secondary">Completare i campi per inserire un nuovo studente</h6>
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
                <form class="border-top border-bottom py-4 mt-4" action="<?php echo $_SERVER['PHP_SELF'] . '#form_inserimento_utente'; ?>" id="form_inserimento_utente" method="POST">
                  <input type="hidden" name="inserimento_utente" value="inserimento_docente">
                  <h6 class="mb-2 text-uppercase">Inserimento docente</h6>
                  <h6 class="text-secondary">Completare i campi per inserire un nuovo docente</h6>
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
                <div class="alert alert-success alert-dismissible fade show mt-3">
                  Inserimento avvenuto con successo!
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              <?php
              } else {
              ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3">
                  Registrazione non riuscita, ti invitiamo a controllare i campi inseriti.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
              }
            }
            ?>
          </div>
          <!-- Gestione utenti -->
          <div class="row my-5" id="gestione_utente">
            <h6 class="mb-4 text-uppercase">Ricerca utente</h6>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#gestione_utente"; ?>">
              <div class="col-md-6">
                <label class="form-label">Email dell'utente</label>
                <input type="email" class="form-control mb-3" placeholder="name@example.com" name='cerca_utente' />
              </div>
              <button type="submit" class="btn custom-btn my-1">Cerca</button>
            </form>
            <?php
            if (isset($_POST['cerca_utente']) || isset($_POST['carriera_valida']) || isset($_POST['carriera_completa']) || isset($_POST['carriera_completa_storico']) || isset($_POST['carriera_valida_storico'])) {
              if (isset($_SESSION['info_utente'])) {
                $id_utente = $_SESSION['info_utente']['id'];
            ?> <div class="card p-4 my-3 table-responsive" id="info_utente">
                  <h5 class="mb-4 text-uppercase">Informazione sull'utente </h5>
                  <div class="d-flex mb-1">
                    <label class="fs-6"><strong>Nome: </strong><?php echo ($_SESSION['info_utente']['nome']); ?></label>
                  </div>
                  <div class="d-flex mb-1">
                    <label class="fs-6 "><strong>Cognome: </strong><?php echo ($_SESSION['info_utente']['cognome']); ?></label>
                  </div>
                  <div class="d-flex mb-1">
                    <label class="fs-6 "><strong>E-mail: </strong><?php echo ($_SESSION['info_utente']['email']); ?></label>
                  </div>
                  <?php
                  if (array_key_exists('matricola', $_SESSION['info_utente'])) {
                  ?>
                    <div class="d-flex mb-1">
                      <label class="fs-6 "><strong>Matricola: </strong><?php echo ($_SESSION['info_utente']['matricola']); ?></label>
                    </div>
                    <div class="d-flex mb-1">
                      <label class="fs-6 "><strong>Corso di studi: </strong><?php echo ($_SESSION['info_utente']['nome_corso']); ?></label>
                    </div>
                    <div class="d-flex mb-1">
                      <label class="fs-6 "><strong>Anno di iscrizione: </strong><?php echo ($_SESSION['info_utente']['anno_iscrizione']); ?></label>
                    </div>
                    <?php
                    if (isset($_SESSION['info_utente']['anno_frequenza'])) {
                    ?>
                      <div class="d-flex mb-1">
                        <label class="fs-6 "><strong>Anno di frequenza: </strong><?php echo ($_SESSION['info_utente']['anno_frequenza']); ?></label>
                      </div>
                    <?php
                    } else {
                      $storico = 1;
                    ?>
                      <div class="d-flex mb-1">
                        <label class="fs-6 text-danger"><strong>Lo studente non è attualmente iscritto</strong></label>
                      </div>
                    <?php
                    }
                    if (!isset($storico)) {
                    ?>
                      <div class="d-flex my-2">
                        <form class="" method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#info_utente" ?>">
                          <input type="hidden" name="carriera_valida" value="carriera_valida">
                          <button type="submit" class="btn custom-btn">Carriera valida</button>
                        </form>
                        <form class="mx-3" method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#info_utente" ?>">
                          <input type="hidden" name="carriera_completa" value="carriera_completa">
                          <button type="submit" class="btn custom-btn">Carriera completa</button>
                        </form>
                      </div>
                    <?php
                    } else {
                    ?>
                      <div class="d-flex my-2">
                        <form class="" method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#info_utente" ?>">
                          <input type="hidden" name="carriera_valida_storico" value="carriera_valida_storico">
                          <button type="submit" class="btn custom-btn">Carriera valida</button>
                        </form>
                        <form class="mx-3" method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#info_utente" ?>">
                          <input type="hidden" name="carriera_completa_storico" value="carriera_completa_storico">
                          <button type="submit" class="btn custom-btn">Carriera completa</button>
                        </form>
                      </div>
                    <?php
                    }
                    $voti = array();
                    if (isset($_POST['carriera_valida'])) {
                      $voti = getCarrieraValida($_SESSION['info_utente']['id']);
                    } elseif (isset($_POST['carriera_completa'])) {
                      $voti = getCarrieraCompleta($_SESSION['info_utente']['id']);
                    } elseif (isset($_POST['carriera_valida_storico'])) {
                      $voti = getCarrieraValidaStorico($_SESSION['info_utente']['id']);
                    } elseif (isset($_POST['carriera_completa_storico'])) {
                      $voti = getCarrieraCompletaStorico($_SESSION['info_utente']['id']);
                    }

                    if (count($voti) > 0) {
                    ?>
                      <table class="table align-middle " id="carriera">
                        <thead>
                          <tr>
                            <th scope="col">C.Esame</th>
                            <th scope="col">Esame</th>
                            <th scope="col">Valutazione</th>
                            <th scope="col">Data verbalizzazione</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($voti as $voto) { ?>
                            <tr>
                              <th scope="row"><?php echo $voto['codice']; ?></th>
                              <td><?php echo $voto['nome']; ?></td>
                              <td>&nbsp&nbsp&nbsp&nbsp<?php echo $voto['voto']; ?></td>
                              <td><?php echo $voto['data_verbalizzazione']; ?></td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    <?php
                    } elseif (isset($_POST['carriera_valida']) || isset($_POST['carriera_completa']) || isset($_POST['carriera_completa_storico']) || isset($_POST['carriera_valida_storico'])) { ?>
                      <div class="alert alert-primary my-3">Nessun esame disponibile.</div>
                    <?php }
                    if (!isset($storico)) {
                    ?>
                      <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#inserimento_utente" ?>">
                        <input type="hidden" name="elimina_studente" value="<?php echo $_SESSION['info_utente']['id'] ?>">
                        <button type="submit" class="btn btn-danger my-2">Elimina utente</button>
                      </form>
                    <?php
                    }
                  } else {
                    ?>
                    <div class="d-flex mb-1">
                      <label class="fs-6 "><strong>Specializzazione: </strong><?php echo ($info_utente['specializzazione']); ?></label>
                    </div>
                    <?php if (count($insegnamenti_docente) > 0) {
                    ?>
                      <div class="d-flex mb-1">
                        <label class="fs-6 "><strong>Insegnamenti: </strong>

                          <?php
                          foreach ($insegnamenti_docente as $insegnamento) {
                            echo $insegnamento['codice'] . " ";
                          ?>
                          <?php } ?>
                      </div>
                    <?php
                    } else {
                    ?>
                      <div class="d-flex mb-1">
                        <label class="fs-6 "><strong>Nessun insegnamento assegnato</strong></label>
                      </div>
                    <?php
                    }
                    ?>
                    </label>



                    <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#inserimento_utente" ?>">
                      <input type="hidden" name="elimina_docente" value="<?php echo $_SESSION['info_utente']['id'] ?>">
                      <button type="submit" class="btn btn-danger my-2">Elimina utente</button>
                    </form>
                  <?php
                  }
                  ?>

                </div>
              <?php
              } else {
              ?><div class="alert alert-danger alert-dismissible fade show mt-3">Utente non trovato<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
              <?php
              }
            }
            if (isset($_POST['elimina_studente']) && empty($rimozione)) {
              ?>
              <div class="alert alert-success alert-dismissible fade show mt-3">Studente rimosso con successo!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php
            } elseif (isset($_POST['elimina_studente'])) {
            ?>
              <div class="alert alert-danger alert-dismissible fade show mt-3"><?php echo $rimozione ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php
            }

            if (isset($_POST['elimina_docente']) && empty($rimozione)) {
            ?>
              <div class="alert alert-success alert-dismissible fade show mt-3">Docente rimosso con successo!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php
            } elseif (isset($_POST['elimina_docente'])) {
            ?>
              <div class="alert alert-danger alert-dismissible fade show mt-3"><?php echo $rimozione ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php
            }
            ?>

          </div>
        </div>
        <div class="row mx-5 my-4 p-3 shadow rounded" id="sezione_corsi">
          <h3 class="mb-4 text-uppercase">Gestione Corsi-Insegnamenti</h3>
          <!-- INSERIMENTO CORSI/INSEGNAMENTI -->
          <div class="row" id="inserimento_corso">
            <h6 class="mb-4 text-uppercase">Inserimento nuovo corso</h6>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#inserimento_corso" ?>">
              <input type="hidden" name="inserimento" value="inserimento_corso">
              <button type="submit" class="btn btn-light my-1">> Inserisci corso di studi</button>
            </form>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . "#inserimento_corso" ?>">
              <input type="hidden" name="inserimento" value="inserimento_insegnamento">
              <button type="submit" class="btn btn-light my-1">> Inserisci insegnamento</button>
            </form>
            <?php
            if (isset($_POST) && isset($_POST['inserimento'])) {
              $tipo = $_POST['inserimento'];
              if ($tipo == 'inserimento_corso') {
            ?>
                <form class="border-top border-bottom py-4 mt-4" action="<?php echo $_SERVER['PHP_SELF'] . '#inserimento_corso'; ?>" id="inserimento_corso" method="POST">
                  <h6 class="mb-2 text-uppercase">Inserimento corso</h6>
                  <h6 class="text-secondary">Completare i campi per inserire un nuovo corso</h6>
                  <input type="hidden" name="inserimento" value="inserimento_corso">
                  <div class="row mt-4">
                    <div class="col-md-6">
                      <label for="cognome" class="form-label">Codice</label>
                      <input type="text" class="form-control mb-3" placeholder="Inserisci il codice" name="codice" />
                    </div>
                    <div class="col-md-6">
                      <label for="nome" class="form-label">Nome</label>
                      <input type="text" class="form-control mb-3" placeholder="Inserisci il nome" name="nome" />
                    </div>
                    <div class="row">
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Facoltà</label>
                      <input type="text" class="form-control mb-3" placeholder="Inserisci la facoltà" name='facoltà' />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Durata</label>
                      <input type="number" class="form-control mb-3" placeholder="Inserisci la durata del corso" name='durata' />
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Descrizione</label>
                    <textarea type="text" class="form-control mb-3" placeholder="Una breve descrizione del corso" name="descrizione"> </textarea>
                  </div>
                  <button type="submit" class="btn custom-btn mt-4">Conferma</button>
                </form>
              <?php
              } elseif ($tipo == 'inserimento_insegnamento') {
              ?>
                <form class="border-top border-bottom py-4 mt-4" action="<?php echo $_SERVER['PHP_SELF'] . '#inserimento_insegnamento'; ?>" id="inserimento_insegnamento" method="POST">
                  <input type="hidden" name="inserimento" value="inserimento_insegnamento">
                  <h6 class="mb-2 text-uppercase">Inserimento insegnamento</h6>
                  <h6 class="text-secondary">Completare i campi per inserire un nuovo insegnamento</h6>
                  <div class="row mt-4">
                    <div class="col-md-6">
                      <label class="form-label">Codice</label>
                      <input type="text" class="form-control mb-3" placeholder="Inserisci il codice" name="codice" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Corso di studi</label>
                      <input type="text" class="form-control mb-3" placeholder="Inserisci il corso di studi" name="corso_studi" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Nome</label>
                      <input type="text" class="form-control mb-3" placeholder="Inserisci il nome" name="nome" />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Anno di insegnamento</label>
                      <input type="number" class="form-control mb-3" placeholder="Inserisci l'anno" name='anno' />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Docente responsabile</label>
                      <input type="text" class="form-control mb-3" placeholder="Inserisci l'id/codice del docente" name='docente_responsabile' />
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Insegnamento propedeutico</label>
                      <input type="text" class="form-control mb-3" placeholder="Inserisci il codice" name='propedeuticità' />
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Descrizione</label>
                      <textarea type="text" class="form-control mb-3" name='descrizione'>
                    </textarea>
                    </div>
                  </div>

                  <button type="submit" class="btn custom-btn mt-4">Conferma</button>
                </form>
            <?php
              }
            }
            ?>
            <?php
            if ((isset($inserimento))) {
              if ($inserimento == true) {
            ?>
                <div class="alert alert-success  alert-dismissible fade show mt-3">
                  Inserimento avvenuto con successo!
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              <?php
              } else {
              ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3">
                  Inserimento non riuscito, ti invitiamo a controllare i campi inseriti.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php
              }
            }
            ?>
          </div>
          <!-- Gestione corsi -->
          <div class="row my-5" id="gestione_corsi">
            <h6 class="mb-4 text-uppercase">Ricerca corso</h6>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '#gestione_corsi'; ?>">
              <div class="col-md-6">
                <label class="form-label">Codice del corso</label>
                <input type="text" class="form-control mb-3" placeholder="Inserire il codice del corso" name='cerca_corso' />
              </div>
              <button type="submit" class="btn custom-btn my-1">Cerca</button>
            </form>
            <?php
            if (isset($_POST['cerca_corso'], $info_corso)) {
            ?> <div class="card p-4 my-3 table-responsive">
                <h5 class="mb-4 text-uppercase">Informazioni sul corso </h5>
                <div class="d-flex mb-1">
                  <label class="fs-6"><strong>Nome: </strong><?php echo $info_corso['nome']; ?></label>
                </div>
                <div class="d-flex mb-1">
                  <label class="fs-6 "><strong>Codice: </strong><?php echo ($info_corso['codice']); ?></label>
                </div>
                <div class="d-flex mb-1">
                  <label class="fs-6 "><strong>Facoltà: </strong><?php echo ($info_corso['facoltà']); ?></label>
                </div>
                <div class="d-flex mb-1">
                  <label class="fs-6 "><strong>Durata: </strong><?php echo ($info_corso['durata']); ?></label>
                </div>
                <div class="d-flex mb-1">
                  <label class="fs-6 "><strong>Descrizione: </strong><?php echo ($info_corso['descrizione']); ?></label>
                </div>
                <div class="row my-3">
                  <h4 class="mb-4">Insegnamenti del corso di studi</h5>
                    <?php
                    $insegnamenti = getInsegnamentiCorso($info_corso['codice']);
                    if (!empty($insegnamenti)) {
                    ?>
                      <table class="table align-middle">
                        <thead>
                          <tr>
                            <th scope="col">Codice</th>
                            <th scope="col">Nome</th>
                            <th scope="col">Anno</th>
                            <th scope="col">Docente</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($insegnamenti as $insegnamento) { ?>
                            <tr>
                              <th scope="row"><?php echo $insegnamento['codice']; ?></th>
                              <td><?php echo $insegnamento['nome']; ?></td>
                              <td>&nbsp &nbsp<?php echo $insegnamento['anno']; ?></td>
                              <td><?php echo $insegnamento['nome_docente'] . " " . $insegnamento['cognome_docente']; ?></td>
                            </tr>
                          <?php } ?>
                        </tbody>
                      </table>
                    <?php } else { ?>
                      <div class="alert alert-danger alert-dismissible fade show mt-3">Nessun insegnamento disponibile.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
                    <?php } ?>
                </div>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '#gestione_corsi' ?>">
                  <input type="hidden" name="elimina_corso" value="<?php echo $info_corso['codice'] ?>">
                  <button type="submit" class="btn btn-danger my-2">Elimina corso</button>
                </form>
              </div>
            <?php
            } elseif (isset($_POST['cerca_corso'])) {
            ?><div class="alert alert-danger alert-dismissible fade show mt-3">Corso non trovato<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php
            }
            if (isset($_POST['elimina_corso']) && $rimozione == "") {
            ?>
              <div class="alert alert-success alert-dismissible fade show mt-3">Corso rimosso con successo!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php
            } elseif (isset($_POST['elimina_corso'])) {
            ?>
              <div class="alert alert-danger alert-dismissible fade show mt-3"><?php echo $rimozione ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php
            }
            ?>
          </div>
          <!-- Gestione Insegnamenti -->
          <div class="row" id="gestione_insegnamenti">
            <h6 class="mb-4 text-uppercase">Ricerca insegnamenti</h6>
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '#gestione_insegnamenti'; ?>">
              <div class="col-md-6">
                <label class="form-label">Codice dell'insegnamento </label>
                <input type="text" class="form-control mb-3" placeholder="Inserire il codice dell'insegnamento" name='cerca_insegnamento' />
              </div>
              <button type="submit" class="btn custom-btn my-1">Cerca</button>
            </form>
            <?php
            if (isset($_POST['cerca_insegnamento'], $info_insegnamento)) {
            ?> <div class="card p-4 my-3">
                <h5 class="mb-4 text-uppercase">Informazioni sull'insegnamento </h5>
                <div class="d-flex mb-1">
                  <label class="fs-6"><strong>Nome: </strong><?php echo $info_insegnamento['nome']; ?></label>
                </div>
                <div class="d-flex mb-1">
                  <label class="fs-6"><strong>Codice: </strong><label style="letter-spacing: 1px;"><?php echo ($info_insegnamento['codice']); ?></label></label>
                </div>
                <div class=" d-flex mb-1">
                  <label class="fs-6 "><strong>Anno: </strong><?php echo ($info_insegnamento['anno']); ?></label>
                </div>
                <div class="d-flex mb-1">
                  <label class="fs-6 "><strong>Corso di studi: </strong><?php echo ($info_insegnamento['corso_studi']); ?></label>
                </div>
                <div class="d-flex mb-1">
                  <label class="fs-6 "><strong>Docente responsabile: </strong><?php echo ($info_insegnamento['nome_docente'] . " " . $info_insegnamento['cognome_docente']); ?></label>
                </div>
                <div class="d-flex mb-1">
                  <label class="fs-6 "><strong>Descrizione: </strong><?php echo ($info_insegnamento['descrizione']); ?></label>
                </div>
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF'] . '#gestione_insegnamenti' ?>">
                  <input type="hidden" name="elimina_insegnamento" value="<?php echo $info_insegnamento['codice'] ?>">
                  <button type="submit" class="btn btn-danger my-2">Elimina insegnamento</button>
                </form>
              </div>
            <?php
            } elseif (isset($_POST['cerca_insegnamento'])) {
            ?><div class="alert alert-danger alert-dismissible fade show mt-3">Insegnamento non trovato<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php
            }
            if (isset($_POST['elimina_insegnamento']) && $rimozione == "") {
            ?>
              <div class="alert alert-success alert-dismissible fade show mt-3">Insegnamento rimosso con successo!<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
            <?php
            } elseif (isset($_POST['elimina_insegnamento'])) {
            ?>
              <div class="alert alert-danger alert-dismissible fade show mt-3"><?php echo $rimozione ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
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