<?php
require_once('../php/config.php');
$datiblog = $_POST;
$risultato = [];
$errori = [];
//verifico cge gli sia arrivato l'id del blog che sto modificando
if (isset($datiblog['id_blog']) && $datiblog['id_blog'] != "") {
    $nome = trim($db->escape($datiblog['nome']));
    $descrizione = trim($db->escape($datiblog['descrizione']));
    //faccio i controlli
    if (strlen($nome) < 3) {
        $errori[] = "Il titolo deve contenere almeno 3 caratteri";
    }
    //controllo che la descrizione sia almeno di 20 caratteri
    if (strlen($descrizione) < 20) {
        $errori[] = "Scrivi una descrizione di almeno 20 caratteri";
    }

    if ($datiblog['argomento'] == '') $argomento = '0';
    else $argomento = $datiblog['argomento'];


    if (isset($datiblog['nuovoargomento']) && trim($datiblog['nuovoargomento']) != "") {
        $nuovoargomento = trim($db->escape($datiblog['nuovoargomento']));
        //faccio una query x verificare che non sia presente già nel database l'argomento che sto inserendo
        $argomentidiversi = "SELECT codice FROM argomento WHERE tipo='{$nuovoargomento}' AND macro_argomento is NULL";
        #eseguo la query
        $eseguoqueryarg = $db->query($argomentidiversi);
        #ottengo il risultato della query
        $argdiversi = $db->fetchArray($eseguoqueryarg);
        #se argdiversi è vuoto, non abbiamo trovato argomenti uguali faccio una insert nuova e recupero l'id nuovo
        if (empty($argdiversi)) {
            //siccome tipo macro argomento e id utente sono chiave unica della tab argomento, se un utente inserisse una stessa sottocategoria ci sarebbe un errore per duplicazione della chiave, con ignore invece l'errore viene ignorato e l'inserimento non effettuato comunque
            //inseriso il nuovo argomento nel db
            $inseriscoargomento = "INSERT ignore INTO argomento (tipo, id_utente) values ('{$nuovoargomento}', '{$datiblog['id_utente']}')";
            #eseguo la query
            $queryinserimentoargomento = $db->query($inseriscoargomento);
            //assegno alla var argomento l'id del nuovo argomento appena inserito
            $argomento = $db->lastInsertID();
        } else {
            //se argdiversi non è vuoto, quindi ho già un argomento di questo tipo nel database, recupero il codice di quell'argomento
            $argomento = $argdiversi['codice'];
        }
    }
    //se non ci sono errori aggiorno i dati nel db
    if (empty($errori)) {
        //se il campo coautore non è stato selezionato, rimane null, altrimenti prende il valore selezionato dall'utente
        if ($datiblog['coautore'] == '') $coautore = 'NULL';
        else $coautore = $datiblog['coautore'];
        //se il campo grafica non è stato selezionato, rimane null, altrimenti prende il valore selezionato dall'utente
        if ($datiblog['grafica'] == '') $grafica = 'NULL';
        else $grafica = $datiblog['grafica'];

        //aggiorno dati del blog
        $datimodifica = "UPDATE blog SET nome='{$nome}', descrizione='{$descrizione}', argomento='{$argomento}', grafica={$grafica},coautore={$coautore}  WHERE codice='{$datiblog['id_blog']}'";
        $db->query($datimodifica);
        $risultato['risultato'] = 'ok';
    } else {
        $risultato['risultato'] = 'errore';
        $risultato['elencoerrori'] = implode("<br/>", $errori);
    }
} else {
    $risultato['risultato'] = 'errore';
    $risultato['elencoerrori'] = 'Errore nella modifica del blog';
}
//trasforma in formato json un array e lo ritorna alla richiesta ajax dove lo gestirò come array
echo json_encode($risultato);
