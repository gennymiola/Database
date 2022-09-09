<?php
require_once('../php/config.php');
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
  <link rel="stylesheet" href="../fontawesome/css/all.min.css">
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
  <section class="hero is-medium">
    <figure class="image"> <img src="../img/index.jpg" /> </figure>
    <?php
    //recupero i post dei blog e mostro gli ultimi 8 pubblicati. Metto in relazione la tabella post con la tabella blog e argomento e grafica per recuperare l'argomento del blog e del post relativo e la grafica selezionata dall'utente.
    $ultimipost = "SELECT p.titolo, p.testo, p.codicepost, a.tipo, g.* FROM post_esempio as p JOIN blog as b ON p.codice_blog=b.codice JOIN argomento as a ON b.argomento=a.codice LEFT JOIN grafica AS g ON g.codice=b.grafica ORDER BY data desc LIMIT 8 ";
    #eseguo la query
    $queryultimipost = $db->query($ultimipost);
    #ottengo il risultato della query e scrivo nella var i dati del post relativo al blog che stampo in pagina (fetchall perché ho più post per il blog (ultimitre è un array 3 elementi, di cui ognuno a sua volta è un array))
    $ultimitre = $db->fetchAll($queryultimipost);
    ?>
  </section>
  <section style="padding-left:20px;padding-right:20px; background-color: #f7eac1;" class="hero is-light">
    <div class="container has-text-centered">
      <h1 class="subtitle is-4" style="padding-top: 20px;">ULTIME PUBBLICAZIONI</h1>
    </div>
    <br>
    <br>
    <div class="columns is-multiline">
      <?php
      foreach ($ultimitre as $post) { //con un ciclo stampo il titolo degli ultimi 3 post inseriti
        //uso la funzione php glob per recuperare le immagini relative al post che stampo e le cerco nella cartella images (la variabile $percorsoimmagine è definita in config.php) e con il nome id post seguito immaginecopertina (perché voglio stampare solo l'immagine di copertina) seguita da qualsiasi carattere(* per l'estensione)
        $imgcopertina = glob($percorsoimmagine . $post['codicepost'] . "_immagine_copertina.*");
        //se l'immagine di copertina è presente, la stampo
        if (count($imgcopertina) > 0) {
          $linkimmagine = $imgcopertina[0];
        } //altrimenti stampo un'immagine di default
        else {
          $linkimmagine = "../img/blog.jpeg";
        }
      ?>
        <div class="column post is-3">
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
                <div class="card-image">
                  <figure class="image is-4by3" style="background-image:url(<?php echo $linkimmagine ?>); background-size: cover; background-repeat: no-repeat; background-position: center; border-radius: 15px; position:relative;"> </figure>
                </div>
                <div class="column buttoncentrale">
                  <h3 class="heading post-category" style="text-align: center;"><?php echo $post['tipo']; ?></h3>
                  <h1 class="title post-title" style="text-align:center;"><?php echo $post['titolo']; ?></h1>
                  <p class="post-excerpt" style="text-align:center;">
                    <?php echo substr($post['testo'], 0, 50); ?>
                  </p>
                  <br>
                  <div class="button is-dark is-outlined"> <a href="pag.php?id_post=<?php echo $post['codicepost'] ?>">Leggi</a>
                    <style>
                      /* seleziono gli elementi che hanno la classe button contenuti nell'elemento con la classe button centrale
              */

                      .buttoncentrale .button {
                        width: 100%;
                        text-align: center;
                        margin: auto;
                      }

                      .buttoncentrale .button a {
                        display: inline-block;
                      }
                    </style>
                  </div>
                </div>
              </div>
            </div>
          </article>
        </div>
      <?php
      }
      ?>
    </div>
    <section class="section" style="font-family:Helvetica; text-align: center;margin-top: 5%">
      <div class="container">
        <h2 class="subtitle">RICERCA AVANZATA </h2>
      </div>
      <section class="section">
        <div class="columns">
          <div class="column is-10 is-offset-1">
            <div class="container has-text-centered">
              <div class="hero is-light">
                <div class="hero-body">
                  <div class="columns is-multiline">
                    <div class="column is-half">
                      <h2 class="subtitle has-text-grey">Seleziona un argomento</strong></h2>
                      <form action="ricerca.php" method="POST">
                        <div class="select is-dark">
                          <select name="genere">
                            <?php
                            //recupero dinamicamente gli argomenti dalla tab argomento
                            $generi = "SELECT * FROM argomento WHERE macro_argomento is NULL";
                            $eseguoquery = $db->query($generi);
                            $risultatogeneri = $db->fetchAll($eseguoquery);
                            //ciclo i risultati e stampo le opzioni della select per la ricerca generi dalla tabella argomento
                            foreach ($risultatogeneri as $genere) {
                              echo " <option value='{$genere['codice']}'>{$genere['tipo']}</option>";
                            }
                            ?>
                          </select>
                        </div>
                        <input type="submit" class="button is-dark is-outlined is-small is-fullheight" style="margin-left: 2%;margin-top: 0.5%" value="  Ricerca" />
                      </form>
                    </div>
                    <div class="column is-half">
                      <form action="ricerca.php" method="POST">
                        <h2 class="subtitle has-text-grey" style="margin-right: 40%" ;>Cerca per nome</strong></h2>
                        <article class="columns is-multiline">
                          <div class="panel-block">
                            <p class="control has-icons-left">
                              <input class="input is-dark" name="contenuto" type="text" maxlength="50" placeholder="Search" /> <span class="icon is-left">
                                <i class="fas fa-search" aria-hidden="true"></i>
                              </span>
                            </p>
                            <input type="submit" maxlegth="20" class="button is-dark is-outlined is-small is-fullheight" style="margin-left: 2%;margin-top: 0.5%" value="Ricerca" />
                          </div>
                        </article>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </section>
    <footer class="footer" style="background-color: black;">
      <div class="content has-text-centered">
        <div class="container">
          <div class="columns">
            <div class="column is-7">
              <h2 class="title is-3 " style="color:white;">Professione blogger</h2>
              <hr class="content-divider">
              <h3 class="subtitle is-5" style="color:white;">È un sito web realizzato da Genny Miola, studentessa della triennale di Informatica Umanistica, per il progetto di Basi di Dati.
                Il sito è stato progettato sfruttando alcuni template del framework Bulma, che sono stati ampiamenti modificati e personalizzati.
              </h3>
            </div>
            <div class="column is-6">
              <div class="social-media"> <a href="https://www.facebook.com/genny.miola99/" target="_blank" class="button is-light is-large"><i class="fa-brands fa-facebook-square"></i></a> <a href="https://www.instagram.com/gennymiola/" target="_blank" class="button is-light is-large"><i class="fa-brands fa-instagram-square"></i></a> <a href="https://it.linkedin.com/in/genny-miola-a05a72196" target="_blank" class="button is-light is-large"><i class="fa-brands fa-linkedin"></i></a> </div>
              <style>
                .social-media {
                  margin-top: 30px;
                }

                .social-media a {
                  margin-right: 10px;
                }

                .field:not(:last-child) {
                  margin-bottom: 20px;
                }

                @media screen and (min-width: 768px) {
                  body {
                    padding-top: calc(3.25rem + 20px);
                  }

                  .navbar {
                    padding: 10px 0;
                    position: fixed;
                    top: 0;
                    width: 100%;
                  }

                  .hero.is-fullheight {
                    min-height: calc(100vh - 6.5rem - 40px);
                  }
                }
              </style>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <p style="text-align: center;"> <strong>Bulma</strong> by <a href="https://jgthms.com">Jeremy Thomas</a>. The source code is licensed <a href="http://opensource.org/licenses/mit-license.php">MIT</a>. The website content is licensed <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/">CC BY NC SA 4.0</a>. </p>
  </section>
</body>

</html>