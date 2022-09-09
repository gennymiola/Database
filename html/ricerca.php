<?php
require_once('../php/config.php');
#quando clicco su bottone ricerca
if (count($_POST) > 0) {
    //controllo se la variabile genere (name della select in index) è settata e, nel caso, faccio una query sulla tabella blog per cercare i blog con valore dell'argomento quello selezionato
    if (isset($_POST["genere"]) && trim($_POST["genere"]) != '') {
        $genere = trim($db->escape($_POST["genere"]));
        $ricercapergenere = "SELECT b.*,a.tipo, g.nome_font, g.dimensione_font, g.colore FROM blog as b JOIN argomento as a ON b.argomento=a.codice  LEFT JOIN grafica AS g ON g.codice=b.grafica WHERE argomento='{$genere}'";
        //eseguo query
        $eseguoquery = $db->query($ricercapergenere);
        //recupero risultati
        $generitrovati = $db->fetchAll($eseguoquery);
    } elseif (isset($_POST["contenuto"]) && trim($_POST["contenuto"]) != '') {
        $contenuto = trim($db->escape($_POST["contenuto"]));
        //controllo se la variabile contenuto (name dell'input nela index) è settata cercando nei blog dove il nome contiene il contenuto passato preceduto e seguito da qualsiasi carattere e faccio lo stesso per i titoli dei post e nella seconda query faccio una join con blog e con argomento per recuperare l'argomento del blog da cui proviene il post
        $ricercapertitolo = "SELECT b.codice,'blog' as tipocontenuto, b.nome as titolo, descrizione as testo, a.tipo as argomento, g.nome_font, g.dimensione_font, g.colore FROM blog as b JOIN argomento as a ON b.argomento=a.codice LEFT JOIN grafica AS g ON g.codice=b.grafica WHERE b.nome LIKE '%{$contenuto}%'
  UNION ALL 
  SELECT p.codicepost as codice, 'post' as tipocontenuto, titolo,testo, a.tipo as argomento, g.nome_font, g.dimensione_font, g.colore FROM post_esempio as p JOIN blog as b ON p.codice_blog=b.codice JOIN argomento as a ON b.argomento=a.codice LEFT JOIN grafica AS g ON g.codice=b.grafica WHERE titolo LIKE '%{$contenuto}%'";
        //eseguo query
        $eseguo = $db->query($ricercapertitolo);
        $risultatotitoli = $db->fetchAll($eseguo);
    }
    // svuota la variabile post
    unset($_POST);
}
$recuperografica = "SELECT * FROM grafica";
$eseguoquery = $db->query($recuperografica);
$grafiche = $db->fetchAll($eseguoquery);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Il mio blog</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.2/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="../js/jQuery.js">
    </script>
    <script src="../js/function.js">
    </script>
    <script src="../js/menu.js"></script>
    <?php //importo tutti i link dei font delle varie grafiche dinamicamente
    foreach ($grafiche as $grafica) {
        echo "<link id='blogfont' href='{$grafica['font']}' rel='stylesheet'>";
    }
    ?>
</head>

<body>
    <?php
    include("nav.php");
    ?>
    <section class="hero is-small" style="background-color: black;">
        <div class="hero-body">
            <div class="container has-text-centered"> <img src="../img/index.jpg" width="200" height="40" />
                <h2 class="subtitle is-4" style="color: #f7eac1;">
                    Risultati per la tua ricerca:
                </h2>
            </div>
        </div>
    </section>
    <section style="padding:20px; background-color: #f7eac1;" class="hero is-fullheight">
        <div class="columns is-multiline">
            <?php
            if (isset($generitrovati)) {
                foreach ($generitrovati as $genere) {
                    //uso la funzione php glob per recuperare le immagini relative al blog o post che stampo e le cerco nella cartella images e con il nome id blog seguito immaginecopertina (perché voglio stampare solo l'immagine di copertina) seguita da qualsiasi carattere(* per l'estensione)
                    $immagineblog = glob($percorsoimmagineblog . $genere['codice'] . "_immagine_copertina.*");
            ?>
                    <div class="column post is-2">
                        <article class="columns is-multiline">
                            <div class="column is-12 post-img">
                                <div class="box">
                                    <style>
                                        .box {
                                            word-wrap: break-word;
                                        }

                                        .box:hover {
                                            box-shadow: 0px 10px 20px 5px rgba(0, 0, 0, 0.5);
                                            transform: translateY(-5px);
                                            cursor: pointer;
                                        }
                                    </style>
                                    <div class="card-image"> <img src="<?php //controllo se c'è elemento immagine e stampo l'immagine di background dell'elemento
                                                                        if (count($immagineblog) > 0) {
                                                                            echo $immagineblog[0];
                                                                        } else {
                                                                            echo "../img/blog.jpeg ";
                                                                        } ?>" /> </div>
                                    <div class="column is-12 featured-content buttoncentrale has-text-centered">
                                        <h3 class="heading post-category"><?php echo $genere['tipo']; ?></h3>
                                        <h1 class="title post-title"><?php echo $genere['nome']; ?></h1>
                                        <p class="post-excerpt"></p>
                                        <div class="button is-dark"> <a style="color:white;" href="ricercablog.php?id_blog=<?php echo $genere['codice'] ?>">Leggi</a> </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                    <style>
                        /* seleziono gli elementi che hanno la classe button contenuti nell'elemento con la classe button centrale
              */

                        .buttoncentrale .button {
                            width: 100%;
                            text-align: center;
                        }

                        .buttoncentrale .button a {
                            display: inline-block;
                        }
                    </style>
            <?php
                }
            }
            ?>
            <?php
            if (isset($risultatotitoli)) {
                foreach ($risultatotitoli as $titolo) {
                    //uso la funzione php glob per recuperare le immagini relative al blog o post che stampo e le cerco nella cartella images e con il nome id blog seguito immaginecopertina (perché voglio stampare solo l'immagine di copertina) seguita da qualsiasi carattere(* per l'estensione)
                    //ricerco l'immagine di copertina del blog o del post in base a ciòche sto stampando
                    if ($titolo['tipocontenuto'] == "blog") {
                        $immaginecontenuto = glob($percorsoimmagineblog . $titolo['codice'] . "_immagine_copertina.*");
                        $link = "ricercablog.php?id_blog=" . $titolo['codice'];
                    } else {
                        $immaginecontenuto = glob($percorsoimmagine . $titolo['codice'] . "_immagine_copertina.*");
                        $link = "pag.php?id_post=" . $titolo['codice'];
                    }
            ?>
                    <div class="column post is-2">
                        <article class="columns is-multiline">
                            <div class="column is-12 post-img">
                                <div class="box">
                                    <style>
                                        .box {
                                            display: inline-block;
                                            word-wrap: break-word;
                                        }

                                        .box:hover {
                                            box-shadow: 0px 10px 20px 5px rgba(0, 0, 0, 0.5);
                                            transform: translateY(-5px);
                                            cursor: pointer;
                                        }
                                    </style>
                                    <div class="card-image"> <img src="<?php //controllo se c'è elemento immagine e stampo l'immagine di background dell'elemento
                                                                        if (count($immaginecontenuto) > 0) {
                                                                            echo $immaginecontenuto[0];
                                                                        } else {
                                                                            echo "../img/blog.jpeg ";
                                                                        } ?>" /> </div>
                                    <div class="column is-12 featured-content buttoncentrale has-text-centered">
                                        <h3 class="heading post-category"><?php echo $titolo['argomento']; ?></h3>
                                        <h1 class="title post-title"><?php echo $titolo['titolo']; ?></h1>
                                        <p class="post-excerpt"></p>
                                        <div class="button is-dark"> <a style="color:white;" href="<?php echo $link ?>">Leggi</a>
                                            <style>
                                                a {
                                                    color: black;
                                                }
                                            </style>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                    <style>
                        /* seleziono gli elementi che hanno la classe button contenuti nell'elemento con la classe button centrale
              */

                        .buttoncentrale .button {
                            width: 100%;
                            text-align: center;
                        }

                        .buttoncentrale .button a {
                            display: inline-block;
                        }
                    </style>
                <?php
                }
            }
            if (empty($generitrovati) && empty($risultatotitoli)) {
                ?>
                <div class="column is-half is-offset-3" style="margin-top:10%;">
                    <div class="box">
                        <p style="text-align:center;">Nessun risultato per la tua ricerca</p>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </section>
</body>

</html>