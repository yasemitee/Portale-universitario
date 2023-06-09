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

    $logged = null;
    $db = open_pg_connection();
    $sql = "SELECT id, email, nome, cognome FROM portale_uni.studente WHERE email = $1 AND password = $2";
    $params = array(
        $email,
        $password
    );


    $result = pg_prepare($db, "check_user", $sql);
    $result = pg_execute($db, "check_user", $params);

    if ($row = pg_fetch_assoc($result)) {
        $id = $row['id'];
        $logged = $row['email'];
        $nome = $row['nome'];
        $cognome = $row['cognome'];
    }

    close_pg_connection($db);

    $_SESSION['id'] = $id;
    $_SESSION['nome'] = $nome;
    $_SESSION['cognome'] = $cognome;
    return $logged;
}

function register($nome, $cognome, $email, $password, $tipo)
{
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
function getInfoStudente($id)
{
    $db = open_pg_connection();
    $sql = "SELECT s.nome as nome_studente, s.cognome as cognome_studente, s.matricola, c.codice as codice_corso, c.nome, c.durata, s.anno_frequenza  
    FROM portale_uni.studente s inner join portale_uni.corso c on c.codice = s.corso_studi 
    WHERE s.id  = $1;";
    $params = array(
        $id
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
    $sql = "SELECT i.codice, i.nome, i.anno, i.propedeuticità, d.nome as nome_docente, d.cognome as cognome_docente
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
    $sql = "SELECT e.codice, e.nome, e.appello
    from portale_uni.iscrizione i inner join portale_uni.esame e
    on i.esame = e.codice
    where i.id_studente = $1
    order by e.appello;";

    $params = array(
        $id_studente
    );

    $result = pg_prepare($db, "get_iscrizioni", $sql);
    $result = pg_execute($db, "get_iscrizioni", $params);



    $iscrizioni = array();
    while ($row = pg_fetch_assoc($result)) {
        $iscrizioni[$row['codice']] = $row;
    }
    return $iscrizioni;
    close_pg_connection($db);
}

function getEsamiCorso($corso)
{
    $db = open_pg_connection();
    $sql = "SELECT e.codice, e.nome, e.appello, d.nome as nome_docente, d.cognome as cognome_docente
    FROM portale_uni.insegnamento i INNER JOIN portale_uni.docente d 
    ON i.docente_responsabile = d.id inner join portale_uni.esame e
    on e.insegnamento = i.id
    WHERE i.corso_studi  = $1 
    order by i.anno;";

    $params = array(
        $corso
    );

    $result = pg_prepare($db, "get_esami_corso", $sql);
    $result = pg_execute($db, "get_esami_corso", $params);



    $esami = array();
    while ($row = pg_fetch_assoc($result)) {
        $esami[$row['codice']] = $row;
    }
    return $esami;
    close_pg_connection($db);
}

function getEsameByInsegnamento($id_insegnamento)
{
    $db = open_pg_connection();
    $sql = "SELECT e.codice
    FROM portale_uni.esame e INNER JOIN portale_uni.insegnamento i
    ON e.insegnamento = i.id
    where i.id = $1";
    $params = array(
        $id_insegnamento
    );

    $result = pg_prepare($db, "get_esame_by_insegnamento", $sql);
    $result = pg_execute($db, "get_esame_by_insegnamento", $params);

    $codice_esame = pg_fetch_assoc($result)['codice'];
    return $codice_esame;
    close_pg_connection($db);
}

function iscriviEsame($id_studente, $codice_esame, $codice_corso, $carriera)
{
    $err = '';
    $success = '';
    $db = open_pg_connection();

    $iscrizioni = getIscrizioni($id_studente);

    if (array_key_exists($codice_esame, $iscrizioni)) {
        $err = "L'iscrizione per l'esame " . $codice_esame . " è già stata effettuata";
        return array($success, $err);
    }

    // Verifica se l'insegnamento è previsto dal corso di studi dello studente
    $sql1 = "SELECT codice_insegnamento 
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

    $result1 = pg_prepare($db, "verifica_insegnamento", $sql1);
    $result1 = pg_execute($db, "verifica_insegnamento", $params);


    $count = pg_num_rows($result1);

    if ($count == 0) {
        $err = "L'insegnamento non è previsto nel tuo corso di studi";
        return array($success, $err);
    }

    $codice_insegnamento = pg_fetch_assoc($result1)['codice_insegnamento'];



    // $info = getInfoStudente($id_studente);
    // $codice_corso = $info['codice_corso'];


    // Verifica se l'insegnamento ha insegnamenti propedeutici
    $insegnamenti = getInsegnamentiCorso($codice_corso);
    $infoInsegnamento = $insegnamenti[$codice_insegnamento];
    $propedeuticità = $infoInsegnamento['propedeuticità'];
    if (!is_null($propedeuticità)) {
        // Controlla se gli esami propedeutici sono presenti nella carriera dello studente
        // $carriera = getCarrieraValida($id_studente);
        $esame_propedeutico = getEsameByInsegnamento($propedeuticità);

        if (!array_key_exists($esame_propedeutico, $carriera)) {
            // L'esame propedeutico manca nella carriera dello studente
            $err =  "Non hai superato tutti gli esami propedeutici necessari per iscriverti a questo insegnamento.";
            return array($success, $err);
        }
    }

    $sql2 = "INSERT INTO portale_uni.iscrizione (id_studente, esame) VALUES ($1, $2)";
    $params = array(
        $id_studente,
        $codice_esame
    );
    $result = pg_prepare($db, "iscrivi_esame", $sql2);
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

?>