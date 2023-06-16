<?php
define("myhost", "localhost");
define("myuser", "yassine");
define("mypsw", "admin");
define("mydb", "portale_uni");
?>

<?php

function open_pg_connection()
{

    $connection = "host=" . myhost . " dbname=" . mydb . " user=" . myuser . " password=" . mypsw;

    return pg_connect($connection);
}


function login($email, $password)
{

    $params = array(
        $email,
        $password
    );

    $logged = null;
    $header = null;

    $db = open_pg_connection();

    $sql_studente = "SELECT id, email, nome, cognome FROM portale_uni.studente
     WHERE email = $1 AND password = $2 AND matricola IS NOT NULL;";
    $result_studente = pg_prepare($db, "check_studente", $sql_studente);
    $result_studente = pg_execute($db, "check_studente", $params);

    $sql_docente = "SELECT id, email, nome, cognome FROM portale_uni.docente
    WHERE email = $1 AND password = $2 AND specializzazione IS NOT NULL;";
    $result_docente = pg_prepare($db, "check_docente", $sql_docente);
    $result_docente = pg_execute($db, "check_docente", $params);

    $sql_segreteria = "SELECT id, email, nome, cognome FROM portale_uni.segreteria
                      WHERE email = $1 AND password = $2;";
    $result_segreteria = pg_prepare($db, "check_segreteria", $sql_segreteria);
    $result_segreteria = pg_execute($db, "check_segreteria", $params);

    if ($row = pg_fetch_assoc($result_studente)) {
        $id = $row['id'];
        $logged = $row['email'];
        $nome = $row['nome'];
        $cognome = $row['cognome'];
        $_SESSION['id'] = $id;
        $_SESSION['nome'] = $nome;
        $_SESSION['cognome'] = $cognome;
        $header = 'Location: studente.php';
    } elseif ($row = pg_fetch_assoc($result_docente)) {
        $id = $row['id'];
        $logged = $row['email'];
        $nome = $row['nome'];
        $cognome = $row['cognome'];
        $_SESSION['id'] = $id;
        $_SESSION['nome'] = $nome;
        $_SESSION['cognome'] = $cognome;
        $header = 'Location: docente.php';
    } elseif ($row = pg_fetch_assoc($result_segreteria)) {
        $id = $row['id'];
        $logged = $row['email'];
        $nome = $row['nome'];
        $cognome = $row['cognome'];
        $_SESSION['id'] = $id;
        $_SESSION['nome'] = $nome;
        $_SESSION['cognome'] = $cognome;
        $header = 'Location: segreteria.php';
    }

    close_pg_connection($db);

    return array($logged, $header);
}

function registrazione($nome, $cognome, $email, $password, $tipo_utente)
{
    if (empty($nome) || empty($cognome) || empty($password) || empty($tipo_utente)) {
        return false;
    }

    $db = open_pg_connection();

    switch ($tipo_utente) {
        case 'studente':
            $sql = "SELECT COUNT(*) FROM portale_uni.studente WHERE email = $1;";
            break;

        case 'docente':
            $sql = "SELECT COUNT(*) FROM portale_uni.docente WHERE email = $1;";
            break;

        case 'segreteria':
            $sql = "SELECT COUNT(*) FROM portale_uni.segreteria WHERE email = $1;";
            break;

        default:
            close_pg_connection($db);
            return false;
    }

    $params = array(
        $email
    );

    $result = pg_prepare($db, "controllo_esistenza", $sql);
    $result = pg_execute($db, "controllo_esistenza", $params);

    $count = pg_fetch_result($result, 0, 0);
    if ($count > 0) {
        close_pg_connection($db);
        return false;
    }

    switch ($tipo_utente) {
        case 'studente':
            $sql = "INSERT INTO portale_uni.studente (email, password, nome, cognome) 
            VALUES ($3, $4, $1, $2)";
            break;

        case 'docente':
            $sql = "INSERT INTO portale_uni.docente (email, password, nome, cognome) 
            VALUES ($3, $4, $1, $2)";
            break;

        case 'segreteria':
            $sql = "INSERT INTO portale_uni.segreteria (email, password, nome, cognome) 
            VALUES ($3, $4, $1, $2)";
            break;

        default:
            close_pg_connection($db);
            return false;
    }

    $params2 = array(
        $nome,
        $cognome,
        $email,
        $password,
    );

    $result = pg_prepare($db, "registrazione", $sql);
    $result = pg_execute($db, "registrazione", $params2);

    close_pg_connection($db);
    return $result !== false;
}



/*
Close connection with PostgreSQL server
*/
function close_pg_connection($db)
{

    return pg_close($db);
}

function cambiaPassword($email, $vecchia_password, $nuova_password)
{
    $db = open_pg_connection();
    $sql = "UPDATE portale_uni.studente
    SET password = $3 WHERE email = $1 AND password = $2;";

    $params = array(
        $email,
        $vecchia_password,
        $nuova_password
    );

    $result = pg_prepare($db, "change_password", $sql);
    $result = pg_execute($db, "change_password", $params);

    $err = '';

    if (pg_affected_rows($result) === 0) {
        $err = "Non è stato possibile cambiare la password, verificare i dati inseriti e riprovare.";
    }


    return $err;

    close_pg_connection($db);
}

/*
* Prende le info di uno studente e le resituisce in un array
*/
function getInfoStudente($id_studente)
{
    $db = open_pg_connection();
    $sql = "SELECT s.nome as nome_studente, s.cognome as cognome_studente, s.matricola, c.codice as codice_corso, c.nome, c.durata, s.anno_frequenza  
    FROM portale_uni.studente s inner join portale_uni.corso c on c.codice = s.corso_studi 
    WHERE s.id  = $1;";
    $params = array(
        $id_studente
    );

    $result = pg_prepare($db, "get_info", $sql);
    $result = pg_execute($db, "get_info", $params);

    if ($row = pg_fetch_assoc($result)) {
        return $row;
    }

    close_pg_connection($db);
}

function getInfoDocente($id_docente)
{
    $db = open_pg_connection();
    $sql = "SELECT d.nome as nome_docente, d.cognome as cognome_docente, d.specializzazione
    FROM portale_uni.docente d
    WHERE d.id  = $1;";
    $params = array(
        $id_docente
    );

    $result = pg_prepare($db, "get_info", $sql);
    $result = pg_execute($db, "get_info", $params);

    if ($row = pg_fetch_assoc($result)) {
        return $row;
    }

    close_pg_connection($db);
}

function getInsegnamentiCorso($corso)
{
    $db = open_pg_connection();
    $sql = "SELECT i.codice, i.nome, i.anno, i.propedeuticità, i.descrizione, d.nome as nome_docente, d.cognome as cognome_docente
    FROM portale_uni.insegnamento i INNER JOIN portale_uni.docente d 
    ON i.docente_responsabile = d.id 
    WHERE i.corso_studi  = $1 
    ORDER BY i.anno;";
    $params = array(
        $corso
    );

    $result = pg_prepare($db, "get_info_corso", $sql);
    $result = pg_execute($db, "get_info_corso", $params);

    $insegnamenti = array();
    while ($row = pg_fetch_assoc($result)) {
        $insegnamenti[$row['codice']] = $row;
    }
    return $insegnamenti;
    close_pg_connection($db);
}

function getInsegnamentiDocete($id_docente)
{
    $db = open_pg_connection();
    $sql = "SELECT i.codice, i.nome, i.anno, i.propedeuticità, i.descrizione, i.corso_studi
    FROM portale_uni.insegnamento i INNER JOIN portale_uni.docente d 
    ON i.docente_responsabile = d.id
    WHERE d.id = $1
    ORDER BY i.anno;";
    $params = array(
        $id_docente
    );

    $result = pg_prepare($db, "get_insegnamenti_docente", $sql);
    $result = pg_execute($db, "get_insegnamenti_docente", $params);

    $insegnamenti = array();
    while ($row = pg_fetch_assoc($result)) {
        $insegnamenti[$row['codice']] = $row;
    }
    close_pg_connection($db);
    return $insegnamenti;
}

function numeroStudentiInsegnamento($codice_insegnamento, $corso_studi)
{
    $db = open_pg_connection();

    $sql = "SELECT COUNT(*)
    FROM portale_uni.vista_studente_insegnamento 
    WHERE codice_insegnamento = $1 AND corso_studi = $2";

    $params = array(
        $codice_insegnamento,
        $corso_studi
    );

    $result = pg_prepare($db, "get_numero_studenti", $sql);
    $result = pg_execute($db, "get_numero_studenti", $params);

    $numero = pg_fetch_assoc($result)['count'];
    close_pg_connection($db);
    return $numero;
}

function getCarrieraCompleta($id)
{
    $db = open_pg_connection();
    $sql = "SELECT c.esame as codice, e.nome, c.voto, c.data_verbalizzazione 
    from portale_uni.carriera c inner join portale_uni.esame e 
    on c.esame = e.codice 
    where c.studente = $1
    order by data_verbalizzazione;";
    $params = array(
        $id
    );

    $result = pg_prepare($db, "get_info_carriera_completa", $sql);
    $result = pg_execute($db, "get_info_carriera_completa", $params);

    //è diverso da getCarrieraValid perché sennò mi elimina delle chiavi (non possono esserci duplicati)
    $voti = array();
    $i = 0;
    while ($row = pg_fetch_assoc($result)) {
        $voti[$i] = $row;
        $i++;
    }
    return $voti;
    close_pg_connection($db);
}

function getCarrieraValida($id)
{
    $db = open_pg_connection();
    $sql = "SELECT c.esame as codice, e.nome, c.voto, c.data_verbalizzazione 
    from portale_uni.carriera c inner join portale_uni.esame e 
    on c.esame = e.codice 
    where c.studente = $1 AND c.voto >= 18
    order by data_verbalizzazione;";
    $params = array(
        $id
    );

    $result = pg_prepare($db, "get_info_carriera_valida", $sql);
    $result = pg_execute($db, "get_info_carriera_valida", $params);

    $voti = array();
    while ($row = pg_fetch_assoc($result)) {
        $voti[$row['codice']] = $row;
    }
    return $voti;
    close_pg_connection($db);
}

function getCorsi()
{
    $db = open_pg_connection();
    $sql = "SELECT * FROM portale_uni.corso";

    $params = array();

    $result = pg_prepare($db, "get_info_corso", $sql);
    $result = pg_execute($db, "get_info_corso", $params);



    $corsi = array();
    while ($row = pg_fetch_assoc($result)) {
        $corsi[$row['codice']] = $row;
    }
    return $corsi;
    close_pg_connection($db);
}

function getIscrizioni($id_studente)
{
    $db = open_pg_connection();
    $sql = "SELECT i.id ,e.codice, e.nome, a.data, a.id as id_appello
    from portale_uni.iscrizione i inner join portale_uni.esame e
    on i.esame = e.codice
    inner join portale_uni.appello a
    on i.appello = a.id
    where i.id_studente = $1
    order by a.data;";

    $params = array(
        $id_studente
    );

    $result = pg_prepare($db, "get_iscrizioni", $sql);
    $result = pg_execute($db, "get_iscrizioni", $params);



    $iscrizioni = array();
    while ($row = pg_fetch_assoc($result)) {
        $iscrizioni[$row['id']] = $row;
    }
    return $iscrizioni;
    close_pg_connection($db);
}

function getEsamiCorso($corso)
{
    $db = open_pg_connection();
    $sql = "SELECT e.codice, e.nome, a.id as id_appello, a.data, d.nome as nome_docente, d.cognome as cognome_docente
    FROM portale_uni.insegnamento i INNER JOIN portale_uni.docente d 
    ON i.docente_responsabile = d.id inner join portale_uni.esame e
    on e.insegnamento = i.id
    inner join portale_uni.appello a
    on a.esame = e.codice
    WHERE i.corso_studi  = $1
    order by a.data;";

    $params = array(
        $corso
    );

    $result = pg_prepare($db, "get_esami_corso", $sql);
    $result = pg_execute($db, "get_esami_corso", $params);



    $esami = array();
    $i = 0;
    while ($row = pg_fetch_assoc($result)) {
        $esami[$i++] = $row;
    }
    return $esami;
    close_pg_connection($db);
}

function getEsameByInsegnamento($codice_insegnamento)
{
    $db = open_pg_connection();
    $sql = "SELECT e.codice
    FROM portale_uni.esame e INNER JOIN portale_uni.insegnamento i
    ON e.insegnamento = i.id
    where i.id = (
        SELECT i.id
        from portale_uni.insegnamento i
        where i.codice = $1
    );";
    $params = array(
        $codice_insegnamento
    );

    $result = pg_prepare($db, "get_esame_by_insegnamento", $sql);
    $result = pg_execute($db, "get_esame_by_insegnamento", $params);

    $codice_esame = '';

    if ($row = pg_fetch_assoc($result)) {
        $codice_esame = $row['codice'];
    }

    return $codice_esame;
    close_pg_connection($db);
}

function getAppelliEsame($codice_esame)
{
    $db = open_pg_connection();
    $sql = "SELECT a.id as id_appello, e.codice, e.nome, a.data
    FROM portale_uni.esame e INNER JOIN portale_uni.appello a
    ON e.codice = a.esame
    where a.esame = $1";
    $params = array(
        $codice_esame
    );

    $result = pg_prepare($db, "get_appelli", $sql);
    $result = pg_execute($db, "get_appelli", $params);

    $appelli = array();
    while ($row = pg_fetch_assoc($result)) {
        $appelli[$row['id_appello']] = $row;
    }
    close_pg_connection($db);
    return $appelli;
}

function inserimentoAppello($codice_esame, $data_appello, $corso_studi)
{
    $db = open_pg_connection();
    $err = "";

    // Recupera l'anno del corso di laurea associato all'esame
    $sql = "SELECT anno FROM portale_uni.insegnamento WHERE id = (SELECT insegnamento FROM portale_uni.esame WHERE codice = $1)";
    $params = array($codice_esame);
    $result = pg_prepare($db, "get_anno_esame", $sql);
    $result = pg_execute($db, "get_anno_esame", $params);

    if ($row = pg_fetch_assoc($result)) {
        $anno = $row['anno'];

        // Verifica se esistono appelli per altri esami dello stesso anno e corso, nella stessa giornata
        $sql = "SELECT COUNT(*) 
              FROM portale_uni.appello a INNER JOIN portale_uni.esame e 
              ON a.esame = e.codice
              INNER JOIN portale_uni.insegnamento i 
              ON e.insegnamento = i.id
              WHERE i.anno = $1 AND a.data = $2 AND corso_studi = $3";
        $params = array($anno, $data_appello, $corso_studi);
        $result = pg_prepare($db, "check_nuovo_appello", $sql);
        $result = pg_execute($db, "check_nuovo_appello", $params);

        if ($result) {
            $row = pg_fetch_row($result);
            $count = $row[0];

            if ($count > 0) {
                $err = "Non è possibile programmare nella stessa giornata, appelli per più esami dello stesso anno del corso di laurea " . $corso_studi;
            } else {
                $sql = "INSERT INTO portale_uni.appello (data, esame)
                VALUES ($1,$2)";
                $params = array($data_appello, $codice_esame);
                $result = pg_prepare($db, "inserisci_appello", $sql);
                $result = pg_execute($db, "inserisci_appello", $params);
            }
        } else {
            $err = "Qualcosa è andato storto";
        }
    }
    return $err;
}

function registraVoto($matricola, $codice_esame, $valutazione, $id_appello)
{
    $db = open_pg_connection();
    $err = "";


    // Recupera l'id dello studente
    $sql = "SELECT id
    from portale_uni.studente
    where matricola = $1;";
    $params = array($matricola);
    $result = pg_prepare($db, "get_id_studente", $sql);
    $result = pg_execute($db, "get_id_studente", $params);

    if ($row = pg_fetch_assoc($result)) {
        $id_studente = $row['id'];

        // Verifica se lo studente si è iscritto all'esame
        $sql = "SELECT COUNT(*)
        from portale_uni.iscrizione i 
        where i.id_studente = $1 AND i.appello = $2";
        $params = array($id_studente, $id_appello);
        $result = pg_prepare($db, "check_iscrizione", $sql);
        $result = pg_execute($db, "check_iscrizione", $params);

        if ($row = pg_fetch_row($result)) {
            $count = $row[0];

            if ($count != 0) {
                $sql = "INSERT INTO portale_uni.carriera (studente, esame, voto, data_verbalizzazione)
                VALUES ($1,$2,$3, current_timestamp);";
                $params = array($id_studente, $codice_esame, $valutazione);
                $result = pg_prepare($db, "inserisci_appello", $sql);
                $result = pg_execute($db, "inserisci_appello", $params);
            } else {
                $err = "Lo studente non si è iscritto all'esame";
            }
        } else {
            $err = "Studente non esistente";
        }
    } else {
        $err = "Studente non esistente";
    }
    return $err;
}


function iscriviEsame($id_studente, $codice_esame, $codice_corso, $id_appello, $carriera)
{
    $err = '';
    $success = '';
    $db = open_pg_connection();

    // Verifica se è già presente l'iscrizione
    $sql = "SELECT COUNT(*)
    from portale_uni.iscrizione
    where id_studente = $1 AND esame = $2 AND appello = $3";

    $params = array(
        $id_studente,
        $codice_esame,
        $id_appello
    );

    $result = pg_prepare($db, "verifica_iscrizione", $sql);
    $result = pg_execute($db, "verifica_iscrizione", $params);
    $count = pg_fetch_assoc($result)['count'];
    if ($count != 0) {
        $err = "L'iscrizione per l'esame è già stata effettuata";
        return array($success, $err);
    }

    // Verifica se l'insegnamento è previsto dal corso di studi dello studente
    $sql = "SELECT codice_insegnamento 
    FROM portale_uni.vista_studente_insegnamento 
    WHERE id = $1 AND codice_insegnamento = 
    (SELECT i.codice 
    from portale_uni.insegnamento i inner join portale_uni.esame e 
    on i.id = e.insegnamento
    where e.codice = $2)";

    $params = array(
        $id_studente,
        $codice_esame
    );

    $result = pg_prepare($db, "verifica_insegnamento", $sql);
    $result = pg_execute($db, "verifica_insegnamento", $params);


    $count = pg_num_rows($result);

    if ($count == 0) {
        $err = "L'insegnamento non è previsto nel tuo corso di studi";
        return array($success, $err);
    }

    $codice_insegnamento = pg_fetch_assoc($result)['codice_insegnamento'];


    // Verifica se l'insegnamento ha insegnamenti propedeutici
    $insegnamenti = getInsegnamentiCorso($codice_corso);
    $infoInsegnamento = $insegnamenti[$codice_insegnamento];
    $propedeuticità = $infoInsegnamento['propedeuticità'];

    if (!is_null($propedeuticità)) {
        $esame_propedeutico = getEsameByInsegnamento($propedeuticità);

        if (!array_key_exists($esame_propedeutico, $carriera)) {
            $err =  "Non hai superato tutti gli esami propedeutici necessari per iscriverti a questo esame.";
            return array($success, $err);
        }
    }

    $sql = "INSERT INTO portale_uni.iscrizione (id_studente, esame, appello) VALUES ($1, $2, $3)";
    $params = array(
        $id_studente,
        $codice_esame,
        $id_appello
    );
    $result = pg_prepare($db, "iscrivi_esame", $sql);
    $result = pg_execute($db, "iscrivi_esame", $params);
    close_pg_connection($db);

    if ($result) {
        $success = "Iscrizione effettuata con successo!";
        return array($success, $err);
    } else {
        $err = "Si è verificato un errore durante l'iscrizione.";
        return array($success, $err);
    }
}

function removeIscrizione($id_iscrizione)
{
    $err = '';
    $db = open_pg_connection();
    $sql = "DELETE FROM portale_uni.iscrizione WHERE id = $1;";
    $params = array(
        $id_iscrizione
    );

    $result = pg_prepare($db, "remove_iscrizione", $sql);
    $result = pg_execute($db, "remove_iscrizione", $params);

    if (pg_affected_rows($result) > 0) {
        $err = "";
    } else {
        $err = "Si è verificato un errore durante la rimozione dell'iscrizione.";
    }

    close_pg_connection($db);

    return $err;
}

function removeAppello($id_appello)
{
    $err = '';
    $db = open_pg_connection();
    $sql = "DELETE FROM portale_uni.appello WHERE id = $1;";
    $params = array(
        $id_appello
    );

    $result = pg_prepare($db, "remove_appello", $sql);
    $result = pg_execute($db, "remove_appello", $params);

    if (pg_affected_rows($result) > 0) {
        $err = "";
    } else {
        $err = "Si è verificato un errore durante la rimozione dell'iscrizione.";
    }

    close_pg_connection($db);

    return $err;
}

?>