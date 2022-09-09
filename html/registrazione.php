<?php
require_once('../php/config.php');
//inizializzo var errori 
$errori = [];
if (count($_POST) > 0) #quando clicco su bottone iscriviti
{
    // verifico che la password sia uguale a conferma password e poi eseguo una query per inserire i dati 
    if ($_POST['password'] == $_POST['passwordconferma']) {
        $nome = trim($db->escape($_POST['nome']));
        $cognome = trim($db->escape($_POST['cognome']));
        $email = trim($db->escape($_POST['email']));
        $telefono = trim($db->escape($_POST['telefono']));
        $username = trim($db->escape($_POST['username']));
        $password = trim($db->escape($_POST['password']));

        #controllo che l'username non sia già presente nel db
        $sqlcontrollousername = "SELECT * FROM utente_registrato WHERE username = '{$username}'";
        #eseguo la query
        $querycontrollousername = $db->query($sqlcontrollousername);
        #ottengo il risultato della query
        $queryrisultatousername = $db->fetchArray($querycontrollousername);
        #controllo che l'email non sia già presente nel db
        $sqlcontrolloemail = "SELECT * FROM utente_registrato WHERE email = '{$email}'";
        #eseguo la query
        $querycontrolloemail = $db->query($sqlcontrolloemail);
        #ottengo il risultato della query
        $queryrisultatoemail = $db->fetchArray($querycontrolloemail);
        #controllo che $queryrisultato sia vuoto, se non è vuoto c'è già quell'username o email nel db (metodo empty restituisce true o false, se true l'array è vuoto e esegue l'insert)
        if (empty($queryrisultatousername) && empty($queryrisultatoemail)) {
            //se l'email non rispetta il formato stabilito nella RE nel file config
            if (!preg_match($controllo_email, $email)) {
                $errori[] = "email non valida";
            }
            //se  il telefono non rispetta il formato stabilito nella RE nel file config
            if (!empty($telefono) && !preg_match($controllo_telefono, $telefono)) {
                $errori[] = "numero non valido. Formati validi per i mobili 0039, +39. Non usare spazi e punti";
            }
            //lo username deve essere di almeno 6 caratteri
            if (strlen($username) < 6) {
                $errori[] = "usa almeno 6 caratteri per lo username";
            }
            //la password deve rispettare il formato della RE per essere sicura
            if (!preg_match($controllo_password, $password)) {
                $errori[] = " Password: usa almeno un carattere speciale, un numero. La password deve contenere almeno 8 caratteri.";
            }
            //controllo che sia vuota la var errore (che i controlli siano andati a buon fine)
            if (empty($errori)) {
                //se la var errori è vuota(non ci sono errori) inserisco i dati nel db
                $sql = "INSERT INTO utente_registrato (nome, cognome, email, telefono, username, password) VALUES ('{$nome}','{$cognome}','{$email}','{$telefono}','{$username}',MD5('{$password}'))";
                #eseguo la query
                $db->query($sql);

                //ciclo files 
                //pr ogni elemento assegna l'indice alla prima variabile e il contenuto alla var successiva
                foreach ($_FILES as $tipoimmagine => $immagine) {
                    //funzione trim elimina gli spazi all'inizio e alla fine della stringa e l'if controlla che il campo nome dell'immagine sia diverso da vuoto per verificare che ho caricato un file 
                    if (trim($immagine['name']) != "") {
                        //la var info recupera le info dal file caricato (immagine di copertina o immagine da inserire e nome vero del file caricato), pathinfo è una funzione che recupera i dettagli del file (nome, estensione etc..)
                        $info = pathinfo($immagine['name']);
                        //salvo l'estensione del file caricato
                        $ext = $info['extension'];
                        //crea la stringa del nuovo nome mantendendo la sua estensione e aggiungndo l'id del post
                        $newname = $db->lastInsertID() . '_' . $tipoimmagine . '.' . $ext;
                        //stabilisco il percorso in cui salvare il file
                        $target = $percorsoimgutente . $newname;
                        //funzione prende il file e lo sposta nella cartella di destinazione, prendendolo tramite parametro tmp_name che è il nome che php ha dato temporaneamente al file caricato prima che lo salvi 
                        move_uploaded_file($immagine['tmp_name'], $target);
                    }
                }
                #redirect alla pagina index.php
                header("location: index.php");
            }
        } else { //gestisco gli errori (se sono entrambi già usati li stampo tutti e due, sennò stampo quello che è già usato)
            if (!empty($queryrisultatousername)) {
                $errori[] = "lo username è già usato";
            }
            if (!empty($queryrisultatoemail)) {
                $errori[] = "email già utilizzata";
            }
        }
    } else {
        //gestione degli errori 
        $errori[] = "le password non coincidono";
    }

    // svuota la variabile post
    unset($_POST);
}
?>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="../js/jQuery.js"> </script>
    <script src="../js/function.js"> </script>
    <title>Registrati</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.6.0/css/bulma.min.css"
        integrity="sha256-HEtF7HLJZSC3Le1HcsWbz1hDYFPZCqDhZa9QsCgVUdw=" crossorigin="anonymous" />
    <link rel="stylesheet" type="text/css" href="../css/login.css">
</head>

<body>
    <section class="hero is-fullheight">
        <style>
        .hero {
            background-color: black;
        }
        </style>
        <div class="hero-body" align="center">
            <div class="container has-text-centered">
                <h3 class="title" style="color:#f7eac1;">Registrati alla nostra piattaforma</h3>
                <p class=" has-text-grey"> Tutte le tue credenziali verranno utilizzate per rendere più semplice il tuo
                    accesso </p>
                <br>
                <br>
                <div class="column is-8 is-offset-2">
                    <div class="box" style="padding-left:5%;padding-right: 5%;">

                        <form action="" method="POST" enctype="multipart/form-data">


                            <div class="columns">

                                <div class="column is-half">

                                    <p style="font-weight: bold;"> DATI PERSONALI</p>
                                    <br>
                                    <div class="control has-text-centered">
                                        <p style="color:grey; font-family:helvetica;"> Nome </p>
                                        <div class="control">
                                            <input class="input is-rounded is-warning" maxlength="20" name="nome"
                                                required type="text">
                                        </div>
                                    </div>
                                    <br>



                                    <div class="control has-text-centered">
                                        <p style="color:grey; font-family:helvetica;"> Cognome </p>
                                        <div class="control is-center">
                                            <input class="input is-rounded is-warning" maxlength="20" name="cognome"
                                                required type="text">
                                        </div>
                                    </div>
                                    <br>

                                    <div class="control has-text-centered">
                                        <p style="color:grey; font-family:helvetica;"> E-mail </p>
                                        <div class="control is-center">
                                            <input class="input is-rounded is-warning" maxlength="100" required
                                                name="email" type="email">
                                        </div>
                                    </div>
                                    <br>

                                    <div class="control has-text-centered">
                                        <p style="color:grey; font-family:helvetica;"> Telefono </p>
                                        <div class="control is-center">
                                            <input class="input is-rounded is-warning" maxlength="10" type="int"
                                                name="telefono">
                                        </div>
                                    </div>
                                </div>
                                <div class="column is-half">
                                    <p style="font-weight: bold;"> CREDENZIALI</p>
                                    <br>
                                    <div class="control has-text-centered">
                                        <p style="color:grey; font-family:helvetica;"> Username </p>
                                        <input class="input is-rounded is-warning" maxlength="20" required
                                            name="username" type="text">
                                    </div>
                                    <br>
                                    <div class="control has-text-centered">
                                        <p style="color:grey; font-family:helvetica;"> Password </p>
                                        <input class="input is-rounded is-warning" required name="password"
                                            maxlength="20" type="password">
                                    </div>
                                    <br>
                                    <div class="control has-text-centered">
                                        <p style="color:grey; font-family:helvetica;"> Conferma password </p>
                                        <input class="input is-rounded is-warning" maxlength="20" required
                                            type="password" name="passwordconferma">
                                    </div>
                                    <br>

                                    <div class="control has-text-centered">
                                        <p style="color:grey; font-family:helvetica;"> Carica un'immagine del
                                            profilo </p>
                                       
                                        <input id="file-upload" type="file" accept="image/*" style="text-align:center;"
                                            class="input is-light" name="imgprofilo" />
                                    </div>


                                </div>

                            </div>
                            <input type="submit" class="button is-dark is-outlined is-medium is-fullheight" align="left"
                                value="  Iscriviti">
                        </form>
                    </div>




                </div>
                <p class="has-text-grey">
                    <a href="index.php" style="color:#f7eac1;">Home</a> &nbsp;·&nbsp;
                    <a href="login.php" style="color:#f7eac1;">Accedi</a>
                </p>
            </div>
        </div>
    </section>

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
                    <p>Errore</p>
                    <button class="delete" aria-label="delete" id="errore"
                        onclick="$('#e').removeClass('is-active');"></button>
                </div>
                <div class="message-body">
                    <p class="content has-text-justified">
                        <?php echo implode("<br/>", $errori); //stampo, con il metodo implode che trasforma gli elementi dell'array errori in un'unica stringa separati dal separatore <Br>, l'array
                        ?>
                    </p>

                </div>
            </article>

            <?php

            ?>




            <script async type="text/javascript" src="../js/bulma.js"></script>
</body>

</html>