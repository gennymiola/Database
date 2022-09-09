<?php
require_once('../php/config.php');
$utente = $_SESSION['logged_user'];
$errori = [];
//verifico che la variabile (id del blog) usata nella query string in area riservata sia settata: se torna true facciamo get di id blog, altrimenti false
$id_blog = (isset($_GET["id_blog"]) ? $db->escape($_GET["id_blog"]) : false);
#quando clicco su bottone crea
if (count($_POST) > 0) {
  $modifica = (isset($_POST['modifica']));
  //con escape se il testo cotiene un singolo apice la stringa non si interrompe e non genera un errore
  $nome = trim($db->escape($_POST['nome']));
  $descrizione = trim($db->escape($_POST['descrizione']));
  //controllo se esiste già un blog con il titolo inserito
  $controllonome = "SELECT * from blog WHERE nome='{$nome}'";
  #eseguo la query
  $querycontrollonome = $db->query($controllonome);
  #ottengo il risultato della query
  $queryrisultatonome = $db->fetchArray($querycontrollonome);
  //controllo che non ci sia un blog con lo stesso titolo inserito e che ho inserito almeno un carattere diverso dallo spazio nel titolo (funzione trim) oppure se sono in modifica(ho già fatto i controlli)
  if ((empty($queryrisultatonome) && trim($_POST['nome']) != "") || $modifica) {
    //la var argomento prende il valore settato dalla select
    $argomento = $db->escape($_POST['argomento']);
    //se in post è arrivato il nuovo campo controllo se è diverso da vuoto (allora c'è stato scritto qualcosa)
    if (isset($_POST['nuovoargomento']) && trim($_POST['nuovoargomento']) != "" && !$modifica) {
      $nuovoargomento = trim($db->escape($_POST['nuovoargomento']));
      //faccio una query x verificare che non sia presente già nel database l'argomento che sto inserendo
      $argomentidiversi = "SELECT codice FROM argomento WHERE tipo='{$nuovoargomento}' AND macro_argomento is NULL";
      #eseguo la query
      $eseguoqueryarg = $db->query($argomentidiversi);
      #ottengo il risultato della query
      $argdiversi = $db->fetchArray($eseguoqueryarg);
      #se argdiversi è vuoto, non abbiamo trovato argomenti uguali faccio una insert nuova e recupero l'id nuovo
      if (empty($argdiversi) && $nuovoargomento != "") {
        //siccome tipo macro argomento e id utente sono chiave unica della tab argomento, se un utente inserisse una stessa sottocategoria ci sarebbe un errore per duplicazione della chiave, con ignore invece l'errore viene ignorato e l'inserimento non effettuato comunque
        //inseriso il nuovo argomento nel db
        $inseriscoargomento = "INSERT ignore INTO argomento (tipo, id_utente) values ('{$nuovoargomento}', '{$utente['id']}')";
        #eseguo la query
        $queryinserimentoargomento = $db->query($inseriscoargomento);
        //assegno alla var argomento l'id del nuovo argomento appena inserito
        $argomento = $db->lastInsertID();
      } else {
        //se argdiversi non è vuoto, quindi ho già un argomento di questo tipo nel database, recupero il codice di quell'argomento
        $argomento = $argdiversi['codice'];
      }
    }
    //se non sono in fase di modifica
    if (!$modifica) {
      //controllo che il titolo abbia almeno 6 caratteri
      if (strlen($nome) < 3) {
        $errori[] = "Il titolo deve contenere almeno 3 caratteri";
      }
      //controllo che la descrizione sia almeno di 20 caratteri
      if (strlen($descrizione) < 20) {
        $errori[] = "Scrivi una descrizione di almeno 20 caratteri";
      }
    }

    //se la var errori è vuota, faccio l'inserimento dei dati nel database
    if (empty($errori)) {
      if (!$modifica) {
        //inserisco valori del blog nel database e ho un if in linea (.) e il coautore è vuoto mette null sennò mette il valore di coautore (se la condizione nella parentesi tonda è vera prende il primo valore prima dei due punti, sennò prende il secondo valore dopo i due punti)
        $inserimentoblog = "INSERT into blog (nome, descrizione, data_creazione, id_utente, coautore, argomento, grafica) values ('{$nome}','{$descrizione}',NOW(),'{$utente['id']}'," . ($_POST['coautore'] == "" ? "null" : $_POST["coautore"]) . ",'{$argomento}'," . ($_POST['grafica'] == "" ? "null" : $_POST["grafica"]) . ")";
        //now funzione di mysql che stampa data e ora di ora
        #eseguo la query
        $queryinserimentoblog = $db->query($inserimentoblog);
        //recupero l'id dell'ultimo blog (appena inserito)
        $idbloginserito = "SELECT MAX(codice) as id_blog FROM blog";
        //eseguo la query
        $eseguoqueryblog = $db->query($idbloginserito);
        //salvo l'id del blog
        $idblog = $db->fetchArray($eseguoqueryblog);

        $id_blog = $idblog['id_blog'];
      }
      //ciclo files (array associativo che recupera file inviati al server) così che tipoimmagine sarà l'immagine di copertina e immagine conterrà i rispettivi dati
      foreach ($_FILES as $tipoimmagine => $immagine) {
        //funzione trim elimina gli spazi all'inizio e alla fine della stringa e l'if controlla che il campo nome dell'immagine sia diverso da vuoto per verificare che ho caricato un file
        if (trim($immagine['name']) != "") {
          //la var info recupera le info dal file caricato (immagine di copertina o immagine da inserire e nome vero del file caricato), pathinfo è una funzione che recupera i dettagli del file (nome, estensione etc..)
          $info = pathinfo($immagine['name']);
          //salvo l'estensione del file caricato
          $ext = $info['extension'];
          //crea la stringa del nuovo nome mantendendo la sua estensione e aggiungndo l'id del blog
          $newname = $id_blog . '_' . $tipoimmagine . '.' . $ext;
          //stabilisco il percorso in cui salvare il file
          $target = $percorsoimmagineblog . $newname;
          //se esiste già l'immagine per il blog, la cancello e salvo la nuova
          $checkExist = glob($percorsoimmagineblog .  $id_blog . '_' . $tipoimmagine . ".*");

          if (file_exists($checkExist[0])) unlink($checkExist[0]);
          //funzione prende il file e lo sposta nella cartella di destinazione, prendendolo tramite parametro tmp_name che è il nome che php ha dato temporaneamente al file caricato prima che lo salvi
          //var_dump($immagine);exit();
          move_uploaded_file($immagine['tmp_name'], $target);
        }
      }
      #redirect alla pagina
      header("location: ricercablog.php?id_blog=" . $id_blog);
    }

    //gestione errori

  } else {
    if (!empty($queryrisultatonome)) {
      $errori[] = "Esiste già un blog con questo titolo";
    }
    if (trim($_POST['nome']) == "") {
      $errori[] = "Il campo titolo è obbligatorio.";
    }
  }
  // svuota la variabile post
  unset($_POST);
}
// dichiaro datiblog come array vuoto
$datiblog = [];
//controllo che id blog sia diverso da false e che quindi sia settato e recupero i dati per precompilare il form
if ($id_blog !== false) {
  $querydatiblog = "SELECT * FROM blog WHERE codice={$id_blog}";
  $eseguoquery = $db->query($querydatiblog);
  $datiblog = $db->fetchArray($eseguoquery);
}
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Il mio blog</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.2/css/bulma.min.css">
  <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
  <script src="../js/menu.js"></script>
  <script src="../js/jQuery.js">
  </script>
  <script src="../js/function.js">
  </script>
  <link id="blogfont" href="" rel="stylesheet">
</head>

<body>
  <?php
  include("nav.php");
  ?>
  <section class="hero is-small" style="background-color: black;">
    <div class="hero-body">
      <div class="container has-text-centered"> <img src="../img/index.jpg" width="200" height="40" />
        <h2 class="subtitle is-4" style="color: #f7eac1;">
          Crea il tuo blog</h2>
      </div>
    </div>
  </section>
  <section class="hero is-fullheight" style="background-color: #f7eac1;">
    <form id="form-blog" action="" method="post" enctype="multipart/form-data">
      <div class="hero-body">
        <div class="container has-text-centered">
          <div class="columns">
            <div class="column is-6">
              <input maxlength="25" class="input is-medium" id="nome" name="nome" style="margin-bottom: 10px;" type="text" placeholder="Scrivi qui il titolo che vuoi dare al tuo blog" value="<?php if (isset($datiblog['nome'])) echo $datiblog['nome']; ?>" />
              <div class="field">
                <div class="control">
                  <textarea maxlength="50" class="textarea is-large" id="descrizione" name="descrizione" placeholder="Scrivi qui una breve descrizione del tuo Blog"><?php if (isset($datiblog['descrizione'])) echo $datiblog['descrizione']; ?></textarea>
                </div>
              </div>
              <div class="file" style="margin-left: 25%;">
                <label class="file-label">
                  <input class="file-input" type="file" name="immagine_copertina" accept="image/*" /> <span class="file-cta">
                    <span class="file-icon">
                      <i class="fas fa-upload"></i>
                    </span> <span class="file-label">
                      Scegli un'immagine di copertina
                    </span> </span>
                </label>
              </div>
            </div>
            <div class="columns is-multiline" style="margin-left:20px;">
              <div class="column">
                <h2 class="subtitle is-5" style="text-align:left;"> Seleziona un argomento</h2>
                <div class="control">
                  <div class="select is-rounded is-normal">
                    <select id="argomento" name="argomento">
                      <?php
                      $where = "";
                      //se l'utente non è al livello 2 non mostro gli argomenti che sono stati creati dagli utenti del liv 3
                      if ($utente['livelloutente'] < 2) {
                        //se l'utente è al al liv 0 o 1 mostro i macroargomenti ma solo quelli in cui l'id dell'utente è null, quelli che non stati inseriti da altri utenti registrati
                        $where = " AND id_utente is null";
                      }
                      //recupero dinamicamente gli argomenti padre inseriti nel database
                      $argomenti = "SELECT * FROM argomento WHERE macro_argomento IS NULL{$where}";
                      #eseguo la query
                      $queryargomento = $db->query($argomenti);
                      #ottengo il risultato della query e scrivo nella var dati argomento i dati degli argomenti che stampo in pagina
                      $datiargomento = $db->fetchAll($queryargomento);
                      //faccio un ciclo con cui associare ogni array dell'array datiargomento alla variabile argomento
                      foreach ($datiargomento as $argomento) {
                        //per preimpostare la select in modo da modificare il blog faccio un controllo per vedere se l'argomento preimpostato è quello che è stato selezionato da datiblog
                        $selected = "";
                        if ($argomento['codice'] == $datiblog['argomento']) {
                          $selected = " selected='selected'";
                          //se l'argomento che sto stampando nella select è quello abbinato al blog che sto modificando, la var selected prenderà il valore selected='selected' e quindi dopo nella stampa, dopo il valore delle option stamperà l'attrbuto selected (visualizzo l'argomento selezionato in fase di creazione del blog)

                        }
                        //stampa il valore del campo codice inerente al record di argomento che sta stampando e il tipo di argomento
                        echo "<option value='{$argomento['codice']}'{$selected}>{$argomento['tipo']}</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
                <br>
                <div class="control">
                  <h2 class="subtitle is-5"> Seleziona un coautore </h2>
                  <div class="select is-rounded is-normal">
                    <select id="coautore" name="coautore">
                      <option value="">Nessun coautore</option>
                      <?php
                      //recupero dinamicamente gli utenti registrati dal db
                      $recuperoutenti = "SELECT * FROM utente_registrato";
                      #eseguo la query
                      $querycoautori = $db->query($recuperoutenti);
                      #ottengo il risultato della query e scrivo nella var utenti registrati i dati degli utenti che stampo in pagina
                      $utentiregistrati = $db->fetchAll($querycoautori);
                      //faccio un ciclo con cui associare ogni array dell'array utentiregistrati alla variabile coautore
                      foreach ($utentiregistrati as $coautore) {
                        //per preimpostare la select in modo da modificare il blog faccio un controllo per vedere se il coautore preimpostato è quello che è stato selezionato da datiblog
                        //se dati blog è vuoto va all'echo
                        $selected = "";
                        if ($coautore['id'] == $datiblog['coautore']) {
                          $selected = " selected='selected'";
                          //se il coautore che sto stampando nella select è quello abbinato al blog che sto modificando, la var selected prenderà il valore selected='selected e quindi dopo nella stampa, dopo il valore delle option stamperà l'attrbuto selected (visualizzo il coautore selezionato in fase di creazione del blog)

                        }
                        //stampa il valore dell'id inerente al record di utente_registrato che sta stampando e l'username
                        echo "<option value='{$coautore['id']}'{$selected}>{$coautore['username']}</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="column">
                <div class="field">
                  <div class="control">
                    <h2 class="subtitle is-5">
                      Seleziona una grafica</h2>
                    <div class="select is-rounded is-normal" id="a">
                      <select id="grafica" name="grafica" onchange="previewGrafica()">
                        <option value="">Predefinita</option>
                        <?php
                        //recupero dinamicamente la grafica dal db cercando solo quelle visibili per il livello attuale dell'utente
                        $recuperografica = "SELECT * FROM grafica WHERE livello_utente<='{$utente['livelloutente']}'";
                        #eseguo la query
                        $querygrafica = $db->query($recuperografica);
                        #ottengo il risultato della query e scrivo nella var grafiche i dati delle varie grafiche che stampo in pagina
                        $grafiche = $db->fetchAll($querygrafica);
                        //faccio un ciclo con cui associare ogni array dell'array grafiche alla variabile tipografica
                        foreach ($grafiche as $tipografica) {
                          //per preimpostare la select in modo da modificare il blog faccio un controllo per vedere se la grafica preimpostato è quello che è stato selezionato da datiblog
                          $selected = "";
                          if ($tipografica['codice'] == $datiblog['grafica']) {
                            $selected = " selected='selected'";
                            //se la grafica che sto stampando nella select è quello abbinato al blog che sto modificando, la var selected prenderà il valore selected='selected e quindi dopo nella stampa, dopo il valore delle option stamperà l'attrbuto selected (visualizzo la grafica selezionato in fase di creazione del blog)

                          }
                          //stampa il valore del codice inerente al record di grafica che sta stampando e il nome
                          echo "<option value='{$tipografica['codice']}'{$selected}>{$tipografica['nome']}</option>";
                        }
                        ?>
                      </select>
                    </div>
                    <div id="previewgrafica" style="background-color: white; border-radius: 25px; margin-top:5px; text-align: center;">Lorem Ipsum</div>
                  </div>
                </div>
              </div>
              <div class="column">
                <?php //mostro il campo x creare un nuovo argomento solo se l'utente è al livello3
                if ($utente['livelloutente'] == 3) {
                ?>
                  <div class="field">
                    <div class="control">
                      <h2 class="subtitle is-5">Inserisci un nuovo argomento</h2>
                      <input type="text" class="input is-normal" style="border-radius: 25px;" maxlength="20" name="nuovoargomento" placeholder="nuovo argomento" />
                      <br>
                    <?php
                  }
                    ?>
                    </div>
                  </div>
              </div>
            </div>
          </div>
          <?php
          //se il blog è nuovo faccio tutti gli inserimenti, altrimenti se riconosco che il blog non è nuovo ma già esistente, salvo l'id nel campo hidden, faccio le modifiche e poi rimando alla pag del blog
          if ($id_blog === false) {
          ?>
        </div>
      </div>
      <p class="has-text-centered">
        <input type="submit" class="button is-normal is-light" style="border-style: solid 1px; border-color: black;margin:auto; margin-bottom: 5%;" value="crea" />
      </p>
    <?php
          } else {
            // quando clicco su modifica chiama la funzione javascript
            //con type hidden faccio sì che l'id del blog venga inviato alla richiesta ajax

    ?>
      <p class="has-text-centered">
        <input type="hidden" id="id_blog" name="id_blog" value="<?php echo $id_blog ?>" />
        <input type="hidden" id="id_utente" name="id_utente" value="<?php echo $utente['id'] ?>" />
        <input type="hidden" id="modifica" name="modifica" value="true" />
        <a class="button is-normal is-light" style="border-style: solid 1px; border-color: black;margin:auto;margin-bottom: 5%;" onclick="modificablog();" href="javascript:void(0)">Modifica</a>
      <?php
          }
      ?>
      </p>
    </form>
    <?php //gestione errore
    //inizializzo var per visualizzare il pop ip di errore quano c'è un errore
    $popupvisibile = "";
    // se l'array errori non è vuoto (c'è almeno un errore) faccio comparire pop up
    if (!empty($errori)) {
      $popupvisibile = " is-active";
    }
    ?>
    <div class="modal<?php echo $popupvisibile; ?>" id="e">
      <div class="modal-background"></div>
      <div class="modal-content">
        <article class="message is-dark">
          <div class="message-header">
            <p>Attenzione</p>
            <a class="delete" aria-label="delete" id="errore" onclick="$('#e').removeClass('is-active');"></a>
          </div>
          <div class="message-body">
            <p class="content has-text-justified">
              <?php echo implode("<br/>", $errori); //stampo, con il metodo implode che trasforma gli elementi dell'array errori in un'unica stringa separati dal separatore <Br>, l'array
              ?>
            </p>

          </div>
        </article>
      </div>
    </div>
  </section>
</body>

</html>