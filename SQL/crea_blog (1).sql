-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Lug 15, 2022 alle 11:16
-- Versione del server: 10.4.11-MariaDB
-- Versione PHP: 7.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crea_blog`
--
CREATE DATABASE IF NOT EXISTS `crea_blog` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `crea_blog`;

-- --------------------------------------------------------

--
-- Struttura della tabella `argomento`
--

DROP TABLE IF EXISTS `argomento`;
CREATE TABLE `argomento` (
  `codice` int(10) NOT NULL,
  `tipo` varchar(250) NOT NULL,
  `macro_argomento` int(10) DEFAULT NULL,
  `id_utente` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `argomento`
--

INSERT INTO `argomento` (`codice`, `tipo`, `macro_argomento`, `id_utente`) VALUES
(0, 'generale', NULL, NULL),
(36, 'arte', NULL, NULL),
(37, 'mostre e musei', 36, NULL),
(38, 'convegni letterari', 36, NULL),
(39, 'cinema', NULL, NULL),
(40, 'moda', NULL, NULL),
(45, 'musica', NULL, NULL),
(46, 'concerti', 45, NULL),
(47, 'lifestyle', NULL, NULL),
(69, 'serie tv', 39, NULL),
(70, 'cultura', NULL, NULL),
(71, 'libri', 70, NULL),
(72, 'fumetti', 70, NULL),
(73, 'viaggi', 47, NULL),
(74, 'cantautori italiani', 45, 47),
(77, 'fitness', 47, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `blog`
--

DROP TABLE IF EXISTS `blog`;
CREATE TABLE `blog` (
  `nome` varchar(250) NOT NULL,
  `descrizione` varchar(250) NOT NULL,
  `data_creazione` datetime NOT NULL,
  `codice` int(10) NOT NULL,
  `id_utente` int(10) NOT NULL,
  `argomento` int(10) DEFAULT 0,
  `grafica` int(11) DEFAULT NULL,
  `coautore` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `blog`
--

INSERT INTO `blog` (`nome`, `descrizione`, `data_creazione`, `codice`, `id_utente`, `argomento`, `grafica`, `coautore`) VALUES
('Live music', 'storie di concerti a cui partecipo', '2022-07-13 10:28:15', 108, 32, 45, 1, NULL),
('Ristoranti in Toscana', 'piccola guida per i ristoranti', '2022-07-13 10:40:44', 109, 46, 47, NULL, NULL),
('Wine lover', '                         un blog sul mondo del vino                      ', '2022-07-13 10:57:33', 110, 47, 47, 2, NULL),
('Fashion Victim', 'Alcune tips per essere sempre alla moda  ', '2022-07-13 11:09:53', 111, 47, 40, 5, NULL),
('Mostre a Firenze', 'mostre imperdibili in città', '2022-07-14 13:09:19', 137, 53, 36, 3, NULL),
('cinema mania', 'curiosità sul mondo del cinema', '2022-07-14 13:52:13', 138, 53, 39, 2, 32),
('Libri rivoluzionari', 'Libri da leggere almeno una volta nella vita', '2022-07-14 14:23:28', 140, 32, 70, 4, NULL),
('Fumettologica', 'ressegna dei fumetti che hanno fatto la storia', '2022-07-14 14:41:29', 141, 47, 70, 1, 46),
('Viaggi', 'consigli su mete e cose da fare in viaggio', '2022-07-14 15:03:39', 143, 47, 47, 5, NULL),
('Mr. tamburino', 'I successi e le collaborazioni di Franco Battiato', '2022-07-14 15:18:37', 147, 47, 45, 6, NULL),
('I benefici della corsa', 'Iniziare e imparare a correre per migliorarsi', '2022-07-15 09:40:43', 155, 56, 47, 1, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `commento`
--

DROP TABLE IF EXISTS `commento`;
CREATE TABLE `commento` (
  `codicecomm` int(10) NOT NULL,
  `testo` text NOT NULL,
  `codice_post` int(10) NOT NULL,
  `id_utente` int(10) NOT NULL,
  `dataora` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `commento`
--

INSERT INTO `commento` (`codicecomm`, `testo`, `codice_post`, `id_utente`, `dataora`) VALUES
(164, 'C\'ero anch\'io! Esperienza indimenticabile!!!', 86, 46, '2022-07-13 10:51:02'),
(166, 'Da provare!!', 87, 47, '2022-07-13 11:04:57'),
(167, ' Il concerto più bello della mia vita!!!                              ', 86, 47, '2022-07-13 11:14:43'),
(169, 'vino molto interessante!', 89, 32, '2022-07-13 18:12:10'),
(170, 'Consigliata', 87, 32, '2022-07-13 18:12:46'),
(197, 'Le opere sembrano così realistiche da dare l\'impressione di poterle scoppiare con un ago, come un palloncino :D                        ', 111, 47, '2022-07-14 13:27:19'),
(216, 'Ho assaggiato questo vino in occasione dello Street Wine Festival, molto piacevole!', 89, 53, '2022-07-14 13:59:19'),
(217, 'Corpo denso, consistenza quasi mielosa. Al naso un\'esplosione di profumi di frutta matura e note tropicali.', 88, 32, '2022-07-14 14:17:02'),
(218, 'l\'ultima stagione è pazzesca', 116, 32, '2022-07-14 14:36:20'),
(244, 'Bellissimo post!', 130, 32, '2022-07-14 23:29:36'),
(280, 'Un grande fumettista, un grande artista.', 130, 56, '2022-07-15 09:43:40'),
(281, 'Genialità e follia', 129, 56, '2022-07-15 09:43:55');

-- --------------------------------------------------------

--
-- Struttura della tabella `grafica`
--

DROP TABLE IF EXISTS `grafica`;
CREATE TABLE `grafica` (
  `codice` int(10) NOT NULL,
  `nome` varchar(250) NOT NULL,
  `font` varchar(250) NOT NULL,
  `colore` varchar(250) NOT NULL,
  `dimensione_font` int(11) NOT NULL,
  `nome_font` varchar(250) NOT NULL,
  `livello_utente` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `grafica`
--

INSERT INTO `grafica` (`codice`, `nome`, `font`, `colore`, `dimensione_font`, `nome_font`, `livello_utente`) VALUES
(1, 'classico', 'https://fonts.googleapis.com/css2?family=IM+Fell+Great+Primer+SC&display=swap', '#404040', 20, 'IM Fell Great Primer SC', 0),
(2, 'macchina da scrivere', 'https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@200&display=swap', '#8b4513', 19, 'Roboto Mono', 0),
(3, 'italico', 'https://fonts.googleapis.com/css2?family=Edu+SA+Beginner:wght@500&display=swap', '#D2691E', 21, 'Edu SA Beginner', 0),
(4, 'livello1', 'https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap', '#FC6C85	', 30, 'Dancing Script', 1),
(5, 'livello2', 'https://fonts.googleapis.com/css2?family=Berkshire+Swash&display=swap', '#E97451', 22, 'Berkshire Swash', 2),
(6, 'livello3', 'https://fonts.googleapis.com/css2?family=Fascinate&display=swap', '#990066', 18, 'Fascinate', 3);

-- --------------------------------------------------------

--
-- Struttura della tabella `post_esempio`
--

DROP TABLE IF EXISTS `post_esempio`;
CREATE TABLE `post_esempio` (
  `codicepost` int(10) NOT NULL,
  `titolo` varchar(250) NOT NULL,
  `testo` text NOT NULL,
  `data` datetime NOT NULL,
  `codice_blog` int(10) NOT NULL,
  `codice_autore` int(11) NOT NULL,
  `font` int(10) DEFAULT NULL,
  `n_like` smallint(6) DEFAULT 0,
  `argomento` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `post_esempio`
--

INSERT INTO `post_esempio` (`codicepost`, `titolo`, `testo`, `data`, `codice_blog`, `codice_autore`, `font`, `n_like`, `argomento`) VALUES
(86, 'Bluvertigo + Duran Duran', 'Cuori agitati cantava Eros Ramazzotti proprio negli anni della massima fama dei Duran Duran in Italia. Per una notte, a Lido di Camaiore, sembrava essere tornati al 1985, niente apparentemente cambiato. Il quartetto inglese prossimo all’ingresso nella Rock n’ Roll Hall of Fame, definitiva consacrazione di una carriera ultra-quarantennale, si è presentato in ottima forma musicale al pubblico italiano. L’attesa era tanta per questo live de La Prima Estate, il festival che ha portato sullo stesso palco i Duran e i loro figli spirituali italiani, Bluvertigo. Non c’è stato incontro ravvicinato come in molti speravano, ma Morgan nel suo discorso alla folla (erano circa 6mila, di ogni generazione e provenienza) ha detto: ”Siamo come Dante e Virgilio: l’uno sprigionato dal fiume dell’altro, i Bluvertigo e i Duran Duran”.\r\nI due gruppi simbolo di uno stile musicale unico, ma di provenienze diverse, hanno catturato l’attenzione e l’entusiasmo del pubblico de “La Prima Estate”. Esperimento vinto.', '2022-07-13 10:32:26', 108, 32, NULL, 2, 46),
(87, 'Lo spela, Greve in Chianti', 'La miglior “Pizza-degustazione” dell’anno? Per Gambero Rosso è nel Chianti!\r\no Spela”, non una new entry in questa particolare classifica, ha raggiunto un punteggio di 91 guadagnandosi i “tre spicchi” e, addirittura, ha ottenuto il premio speciale di pizza dell’anno per la categoria “pizza a degustazione”: l’ambito  riconoscimento è arrivato grazie alla pizza Petto d’Anatra (petto d’anatra cotto a bassa temperatura, fiordilatte, spinacino fresco, salsa al whisky).\r\nComplimenti a “Lo Spela”, al suo staff e all’idea di ristorazione coltivata con estrema dedizione: nel Comune di Greve e in particolare al Ferrone si sforna una delle pizze migliori d’Italia…e, di conseguenza, del mondo!', '2022-07-13 10:50:18', 109, 46, NULL, 1, NULL),
(88, 'Franz Haas: Petit manseng', 'Si tratta di un vino particolarmente aromatico dal colore giallo paglierino. Il profumo è molto intenso e fruttato (frutta secca) con note minerali, mentre al palato si presenta sapido, con una buona acidità e struttura.\r\nLa fermentazione avviene in barrique dove giace per circa dieci mesi e poi viene messo in bottiglia a riposare per diversi mesi.\r\nServito freddo ad una temperatura di dodici gradi è molto accattivante e si abbina particolarmente bene alla cucina asiatica, a zuppe thailandesi, a zuppe di pesce, alla pasta con la bottarga o si può semplicemente gustare come aperitivo.', '2022-07-13 11:01:05', 110, 47, NULL, 0, NULL),
(89, 'Cantina Dainelli: La sbronza', '“La Sbronza” IGT Toscana Bianco 2020 – un’Ansonica ben ponderata nella vinificazione (10% di uve macerate 10gg) che ne esalta la solarità e la tessitura minerale decisamente marina. Consistenza, tensione e sale per un vino che è già la base per un percorso in bianco di tutto rispetto. A mio parere, una delle migliori espressioni del varietale e del territorio assaggiate.', '2022-07-13 11:08:09', 110, 47, NULL, 0, NULL),
(90, 'Cosa indossare in questa primavera-estate 2022 ?', 'Il ritorno degli anni Cinquanta e Sessanta sarà la gioia di tutte le amanti del vintage.\r\nPreparati al ritorno dei jeans a vita bassa, dopo un periodo nel quale i pantaloni erano diventati un po\' più alti.\r\nAzzurro e giallo sono i colori da prediligere in questa stagione calda.\r\nTremate, le minigonne sono tornate. E a volte sono decisamente mini, forse un po\' troppo mini. A ognuna la sua lunghezza preferita.\r\nSì alla schiena scoperta, da mettere in mostra in look da sera e da giorno, complici top e abiti con incroci vedo non vedo.\r\nLa moda crochet, poi, è un altro stile da non sottovalutare. Per un mood chic che non passa inosservato.\r\nSì agli occhiali da sole, non solo per proteggere gli occhi dai raggi nocivi del sole, ma anche per essere delle dive. A patto che siano oversize.\r\nCopri i capelli con foulard avvolti con lo stile delle più grandi attrici del passato e sarai bellissima.', '2022-07-13 11:13:16', 111, 47, NULL, 1, NULL),
(111, 'Jeff Koons ', 'Dal 2 ottobre 2021 al 30 gennaio 2022 Palazzo Strozzi ospita una nuova grande mostra dedicata a Jeff Koons, una delle figure più importanti e discusse dell’arte contemporanea a livello globale. A cura di Arturo Galansino e Joachim Pissarro, la mostra porta a Firenze una selezione delle più celebri opere di un artista che, dalla metà degli anni Settanta a oggi, ha rivoluzionato il sistema dell’arte internazionale.\r\nAutore di opere entrate nell’immaginario collettivo grazie alla capacità di unire cultura alta e popolare, dai raffinati riferimenti alla storia dell’arte alle citazioni del mondo del consumismo, Jeff Koons trova nell’idea di “lucentezza” (shine) un principio chiave delle sue innovative sculture e installazioni che mirano a mettere in discussione il nostro rapporto con la realtà ma anche il concetto stesso di opera d’arte. Le opere dell’artista americano pongono lo spettatore davanti a uno specchio in cui riflettersi e lo collocano al centro dell’ambiente che lo circonda.', '2022-07-14 13:17:12', 137, 53, NULL, 1, 37),
(114, 'Let\'s Get Digital!', 'Dal 18 maggio al 31 luglio 2022 Palazzo Strozzi presenta Let’s Get Digital!, nuovo progetto espositivo che porta negli spazi della Strozzina e del cortile la rivoluzione dell’arte degli NFT e delle nuove frontiere tra reale e digitale attraverso le opere di artisti internazionali quali Refik Anadol, Anyma, Daniel Arsham, Beeple, Krista Kim e Andrés Reisinger.\r\nLa mostra presenta un percorso tra installazioni digitali ed esperienze multimediali create da artisti che esprimono le nuove e poliedriche ricerche della Criptoarte, basata sul successo degli NFT, certificati di autenticità digitali che stanno ridefinendo i concetti di unicità e valore di un’opera d’arte. ', '2022-07-14 13:37:54', 137, 53, NULL, 0, 37),
(115, 'Eyes Wide Shut: curiosità', '1. Il film è basato su un romanzo del 1926: Eyes Wide Shut è liberamente ispirato al romanzo Doppio Sogno di Arthur Schnitzler, pubblicato nel 1926. Se consideriamo che il film si svolge negli anni ’90 a New York ci sarà facile capire le variazioni, ma i due prodotti si sovrappongono abbastanza nella trama e nei temi.\r\n2. Kubrick voleva una coppia di attori realmente sposati:\r\na Kubrick piaceva l’idea di scegliere una coppia realmente sposata di attori. Originariamente erano stati presi in considerazione Alec Baldwin e Kim Basinger, ma alla fine si optato per la coppia Tom Cruise e Nicole Kidman.\r\n3. Il film è entrato nel Guinnes dei primati per i tempi di riprese: Eyes Wide Shut ha un posto nel Guinness World Record per il più lungo film costante, con un totale di 400 giorni.\r\n4. Kubrick è morto la settimana dopo aver finito il suo montaggio.\r\n', '2022-07-14 13:58:11', 138, 53, NULL, 1, NULL),
(116, 'Stranger Things', '1. Quali sono le origini del laboratorio di Hawkins?\r\nNella prima stagione di Stranger Things non è mai stato chiarito cosa abbia dato inizio alla sperimentazione del Laboratorio Hawkins sui bambini. La quarta stagione spiega che Henry Creel è stato il primo bambino psichico che il dottor Brenner ha visto e che lo ha ispirato a cercare di crearne altri come lui. Poiché Uno era così difficile da controllare, Brenner sembrava credere che avrebbe avuto più successo se avesse iniziato a lavorare con i bambini in età più giovane. Sebbene non sia ancora certo che tutti gli altri bambini del laboratorio siano stati creati attraverso l’uso massiccio di psichedelici su donne incinte, è implicito che ciò sia avvenuto in molti casi, oltre a quello di Undici.\r\n2. Come ha fatto Undici ad aprire la porta del Sottosopra?\r\nNella sua lotta con Uno, Undici riesce a utilizzare alcuni consigli che lui le aveva dato prima di sconfiggerlo. Uno le spiega che i suoi poteri erano più forti quando erano alimentati alimentati da ricordi di dolore e rabbia. Quando scopre che questo non è sufficiente, Undici riesce ad accedere al ricordo di sua madre che le dice di amarla e questo le dà la spinta necessaria per aprire un varco nella realtà con la sua mente e bandire Uno nel Sottosopra prima che possa fare altri danni.\r\nIl modo in cui Undici ha aperto un portale verso il Sottosopra è rimasto inspiegato per le prime tre stagioni, a parte il fatto che ha scoperto la mente di un Demogorgone durante la visione a distanza. La quarta stagione spiega che una forte connessione psichica con una mente può servire come base per un portale verso il Sottosopra. Potrebbe non essere ancora chiaro come Undici abbia avuto accesso al Sottosopra per bandire Uno.', '2022-07-14 14:04:48', 138, 53, NULL, 1, 69),
(117, 'Achille Lauro all\'ippodromo', 'Fontane di scintille luminose, cannoni spara coriandoli, lingue di fuoco e fasci di luci multicolore. Sul palco di Rock in Roma, all\'Ippodromo delle Capannelle, martedì 12 luglio, Achille Lauro ha portato il suo imponente show. E\' la tappa romana del tour «Achille Lauro Superstar» partito domenica 3 da Torino e che proseguirà per tutta l\'estate e per tutta Italia fino al 12 settembre a Pisa. L\'artista, accompagnato dalla sua band, apre il live con il brano «Delinquente» e poi «Generazione x» e «Maleducata» e infiamma immediatamente l\'atmosfera dell\'arena rock. I circa ventimila presenti al suo concerto, cantano in coro e si scatenano nel ballo. Ospita sul palco l\'amico collega Gemitaiz, mentre racconta dal vivo la sua carriera musicale. Un concerto suddiviso in cinque quadri, in cui l\'artista ripercorre la sua storia, il suo percorso artistico fino ad oggi. Arrivano «Stripper», la canzone portata all’Eurovision Song Contest, «Sabato sera» e «Roma». Sul palco anche un\'orchestra di cinquantadue elementi, mentre in scaletta arrivano «Solo noi», «16 Marzo» e il grande successo sanremese «Rolls Royce». Un spettacolo che ha fatto ballare tutti, di forte empatia e grande impatto emotivo, che Achille Lauro chiude dopo circa due ore con «C\'est la vie». Pubblico in delirio, bellissimo spettacolo.', '2022-07-14 14:15:26', 108, 32, NULL, 0, 46),
(119, 'Delitto e Castigo', 'I classici sono tali perché sembrano in grado di sopravvivere al tempo che passa.  Delitto e castigo parla di oggi. Oggi siamo convinti che esistano categorie che valgono meno di altre. Il nostro protagonista non solo crede di aver compiuto un’azione che l’ha reso grande, ma è anche convinto di aver ucciso soltanto un pidocchio, una parassita e anche per questo non capisce perché dovrebbe essere punito. Ci sono esseri umani che valgono meno di altri, vi ricorda qualcosa?\r\nNon solo, la vicenda del nostro protagonista si svolge in mezzo a due incubi. Il primo riguarda il massacro di un cavallo da parte di un padrone ubriaco: una scena feroce e potentissima. Il secondo, quello finale, riguarda una pandemia che colpisce il mondo. No, il riferimento ad oggi non è tanto il fatto che questa folle pandemia si trasmette dall’Asia all’Europa o che assomiglia terribilmente al coronavirus.  Ad essere attuale è l’inquietante descrizione degli uomini destinati a sopravvivere:\r\n&quot;gli uomini che accoglievano (queste particelle nel corpo umano) dentro di sé diventavano subito indemoniati e pazzi, eppure, non si erano mai creduti così intelligenti e infallibili come dopo il contagio. Mai avevano ritenuto più giusti i loro giudizi, le loro conclusioni scientifiche, le loro categorie e convinzioni morali.&quot;', '2022-07-14 14:28:28', 140, 32, NULL, 0, 71),
(120, 'Sottomissione', 'Sottomissione è un romanzo fantapolitico dello scrittore francese Michel Houellebecq, pubblicato in Europa nel gennaio del 2015. È edito in Italia da Bompiani. Il romanzo, una satira politica, immagina un futuro nel quale un partito musulmano tradizionalista e patriarcale sia in grado di vincere le elezioni presidenziali del 2022 in Francia. Il libro divenne un caso editoriale non solo per le tematiche trattate, ma anche a causa di una macabra coincidenza: fu pubblicato nel giorno dell\'attentato alla sede di Charlie Hebdo a seguito del quale l\'autore sospese la promozione del romanzo in Francia.\r\nNel 2015 il New York Times inserì Sottomissione nella lista dei migliori 100 libri dell\'anno.\r\n&quot;È la sottomissione, l’idea sconvolgente e semplice, mai espressa con tanta forza prima di allora, che il culmine della felicità umana consista nella sottomissione più assoluta. (…) Per me c’è un rapporto tra la sottomissione della donna all’uomo come la descrive Histoire d’O e la sottomissione dell’uomo a Dio come la contempla l’Islam.&quot;', '2022-07-14 14:34:51', 140, 32, NULL, 0, 71),
(121, 'Dylan Dog: GOLCONDA', 'La storia si apre con una telefonata della bella Amber… all’Inferno. Dall’altra parte della cornetta, invece del gruppo musicale I Demoni risponde un vero e proprio demone, svegliato di pessimo umore dal trillo del telefono. Intanto due ragazzi si appartano in un bosco vicino a Londra, ma un occhio gigantesco li sorprende, li fa a brandelli e ruba il loro tandem. Poi un esercito di uomini in bombetta piove sulla città, come nel celebre quadro di Magritte a cui si ispira l’albo, trucidando in maniere particolarmente truculente persone di ogni ceto. Dylan indaga su questi fenomeni e intanto frequenta Amber, il cui locale prende fuoco dopo un concerto dei Demoni (non la band, un vero complesso infernale che canta in latino). Con lei partirà per Golconda, città dell’India, da cui potrà arrivare al centro della terra, proprio dal demone che si è svegliato nelle prime pagine. Guest star dell’episodio, il professor Philip Mortimer di Edgar P. Jacobs, disegnato da Piccatto in perfetta linea chiara.\r\n\r\n“Golconda!” è una delle storie più splatter e surreali dell’intera produzione di Sclavi. Ma, come in tutte le grandi storie dello sceneggiatore, alla base c’è un’idea geniale, motore della storia. Come mai le entità infernali si scatenano proprio in quel momento? I due ragazzi, che si sono appartati nel bosco, hanno profanato un luogo ancora inesplorato, aprendo un varco per le forze infernali. Non si trattava di un posto remoto, ma semplicemente di qualche centimetro quadrato di terra vicino a Londra su cui nessun uomo, per puro caso, aveva mai messo piede', '2022-07-14 14:45:49', 141, 47, NULL, 1, 72),
(122, ' Corte sconta detta arcana', 'Corto Maltese si trova a Hong Kong con Rasputin e viene ingaggiato dalle Lanterne Rosse per dare la caccia a un treno carico d’oro diretto in Russia ma molti altri sono interessati a quel treno carico d’oro e di morte…\r\nCapolavoro assoluto e inarrivabile del Maestro di Malamocco che ancora una volta si dimostra il migliore in assoluto in quello che fa.\r\nIn questa avventura esoterismo e avventura vanno mano nella mano con un tratto in perfetto equilibrio trai suoi due stili dell’autore.', '2022-07-14 14:49:57', 141, 47, NULL, 1, 72),
(124, 'L’arresto di Diabolik', 'Esce il 1 marzo 1963, è il terzo episodio della collana, e vede lady Eva Kant fare la sua entrata in scena. In questa storia con classe, astuzia e freddezza Eva salverà Diabolik dalla ghigliottina, e Il Re del Terrore capirà all’istante di aver trovato l’anima gemella, l’unica donna degna di stargli al fianco. Eva si presenta come la vedova di Lord Anthony Kant, morto in un misterioso incidente di caccia, anche se aleggia su Eva il sospetto di non essere estranea alla morte del marito. Solo più di quarant’anni dopo, nello speciale EVA KANT – QUANDO DIABOLIK NON C’ERA, scopriremo come erano andate realmente le cose.', '2022-07-14 14:54:05', 141, 47, NULL, 0, 72),
(126, 'i 10 luoghi più belli da vedere in Tibet', '1. Palazzo Potala\r\n2. Tempio Jokhang\r\n3. Lago Namtso\r\n4. Lago Yamdrok\r\n5. Campo base del Monte Everest\r\n6. Palazzo Norbulingka\r\n7. Monastero Tashilhunpo\r\n8. Grand Canyon Yarlung Tsangpo\r\n9. Monte Kailash\r\n10. Foresta Lulang\r\n', '2022-07-14 15:04:17', 143, 47, NULL, 2, 73),
(129, 'Morgan-Battiato: collaborazioni', 'Le dieci volte insieme di Morgan e Battiato:\r\n\r\n1.&quot;Prospettiva Nevsky&quot; era la b-side del singolo &quot;Cieli neri&quot; dei Bluvertigo e venne inclusa nella compilation &quot;Battiato non Battiato&quot;, 1998.\r\n2. Battiato ospitò Morgan in qualità di bassista, chitarrista, corista nel suo album &quot;Gommalacca&quot;,1998.\r\n3. I Bluvertigo lo abbiamo ospitarono Battiato in &quot;Zero&quot; nel pezzo &quot;Soprappensiero&quot;. Battiato chiude anche l’album dicendo: &quot;Dove sono arrivato?&quot;, 1999.\r\n4. Insieme hanno prodotto &quot;Arcano Enigma&quot; di Juri Camisasca, che è il disco antesignano di &quot;Zero&quot;. Battiato suonò al posto di Andy e il riff su &quot;Zodiaco&quot; è il padre di quello del pezzo &quot;Zero&quot;, 1999.\r\n5. Una sera, ad Ancona, al festival de &quot;Il violino e la selce&quot; hanno fatto un concerto insieme di cover di Battiato con lui alla voce. L\'hanno registrato, ma non è mai uscito,1999.\r\n6. I Bluvertigo hanno reinterpretato insieme ad Alice &quot;Chanson egocentrique&quot; di Battiato, 2000.\r\n7. Ospite in un suo playback da &quot;Quelli che il calcio…&quot;, Morgan faceva la parte di Jim Kerr in &quot;Running against the grain&quot;, 2001.\r\n8. Battiato partecipa al video de &quot;L\'assenzio&quot; dei Bluvertigo nel ruolo di un matematico alchimista,2001.\r\n9. Morgan è stato attore nel film di Battiato &quot;Perduto Amor&quot;. Nel film fa il bassista della band di Battiato, 2003.\r\n10. &quot;L\'oceano di silenzio&quot; rifatta da Morgan in &quot;Voli imprevedibili&quot;, seconda raccolta tributo a Battiato, 2004.', '2022-07-14 15:24:29', 147, 47, NULL, 2, 74),
(130, 'Pazienza: romantico ribelle', '1. La sua conclusione ultima è che nulla ha senso:\r\nCi piace pensare che, se fosse vivo, Paz avrebbe molto da dire sulla società odierna. Sempre con la stessa carica esplosiva della gioventù, o forse con la ponderazione che viene dall\'età. Di certo però la sua conclusione non cambierebbe: alla fine, nulla ha senso, se non forse il disegno. O, per dirla con le sue parole: &quot;Bah! La realtà!&quot;.\r\n2. Ha unito provocazione e riflessioni sull\'amore e la vita: Eppure, in tutto il suo nichilismo, Pazienza non riesce a distruggere tutto. Restano le riflessioni sulla vita, sull\'amore; resta in qualche modo la bellezza, spesso personificata in volti e corpi di donne (e di uomini) che resistono a qualsiasi umiliazione. E c\'è anche il Paz pensatore,  che mette in bocca a professori tromboni citazioni memorabili come questa: &quot;È assurdo pensare di ritrovarsi un giorno colti, quando non si è letto un libro, o rispettati, se ci si è sempre comportati ingiustamente. Questi sono miracoli che non possono succedere, così come dal giallo con l’azzurro nascerà sempre il verde, non il rosa o il marrone: è verde. Verde matematico&quot;.\r\n3. Ha vissuto una vita ai margini, eppure al centro di tutto: Pazienza sapeva, era convinto di essere un genio. Eppure, per la vita che ha vissuto, sempre ai margini e sempre &quot;contro&quot;, avrebbe potuto fare la fine di molti geni ribelli: scoperto troppo tardi, o forse ignorato. E invece no. Pazienza era corteggiato da cantanti come Vecchioni e Red Ronnie, da registi teatrali, dalla Rai, da registi come Fellini, per realizzare locandine, copertine di dischi, corti animati, videoclip. L\'incontro tra il successo e la ribellione si concluse, fatalmente, con l\'autodistruzione, e la morte per overdose il 16 giugno 1988, a 32 anni.', '2022-07-14 15:33:11', 141, 46, NULL, 2, 72),
(138, 'Iniziare a correre in 10 settimane', 'Settimana 1:\r\n 1 minuto di corsa leggera alternati a 2 minuti di camminata per 9 volte (tot. 27 minuti) \r\nSettimana 2:\r\n 2 minuti di corsa leggera alternati a 3 minuti di camminata per 6 volte (tot. 30 minuti) \r\nSettimana 3:\r\n 4 minuti di corsa leggera alternati a 3 minuti di camminata per 5 volte (tot. 35 minuti) Al termine di queste prime settimane si inizierà a sentirsi più vitali, attivi e reattivi. Attenzione, non esagerate comunque, le prossime settimane saranno ancora più fighe! \r\nSettimana 4:\r\n 6 minuti di corsa leggera alternati a 3 minuti di camminata per 5 volte (tot. 45 minuti) \r\nSettimana 5:\r\n 10 minuti di corsa leggera alternati a 3 minuti di camminata per 4 volte (tot. 52 minuti) \r\nSettimana 6:\r\n 15 minuti di corsa leggera alternati a 3 minuti di camminata per 3 volte (tot. 54 minuti). Adesso dovresti cominciare a sentirti in forma, stai iniziando a percorrere qualche chilometro. Poco più di un mese fa eri un bradipo da divano e adesso ti senti più vitale e attivo. E pure sul piano estetico inizi a migliorare. Che vuoi di più? Continuare, ovviamente. Settimana 7:\r\n 25 minuti di corsa leggera alternati a 3 minuti di camminata per 2 volte (tot. 56 minuti) \r\nSettimana 8:\r\n 40 minuti di corsa leggera (uscita 1) 40 minuti di corsa leggera (uscita 2) 45 minuti di corsa leggera (uscita 3) Settimana 9:\r\n 45 minuti di corsa leggera (uscita 1) 50 minuti di corsa leggera (uscita 2) 50 minuti di corsa leggera (uscita 3) Settimana 10:\r\n 1 ora di corsa leggera oppure 10 km (uscita unica) \r\nAl termine di questa fase riposa qualche giorno, stai imparando a correre e il tuo organismo inizia ad adattarsi. Prenditi sempre i tuoi tempi, se senti la necessità di riposare un giorno in più non farti problemi: è sempre bene ascoltare i segnali del nostro corpo.', '2022-07-15 09:42:56', 155, 56, NULL, 0, 77);

-- --------------------------------------------------------

--
-- Struttura della tabella `utente_like`
--

DROP TABLE IF EXISTS `utente_like`;
CREATE TABLE `utente_like` (
  `id` int(11) NOT NULL,
  `id_utente` int(11) NOT NULL,
  `id_post` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `utente_like`
--

INSERT INTO `utente_like` (`id`, `id_utente`, `id_post`) VALUES
(57, 46, 86),
(60, 47, 86),
(61, 47, 87),
(63, 32, 90),
(65, 47, 111),
(66, 32, 116),
(67, 32, 126),
(73, 32, 129),
(79, 32, 130),
(81, 32, 115);

-- --------------------------------------------------------

--
-- Struttura della tabella `utente_registrato`
--

DROP TABLE IF EXISTS `utente_registrato`;
CREATE TABLE `utente_registrato` (
  `id` int(10) NOT NULL,
  `nome` varchar(250) NOT NULL,
  `cognome` varchar(250) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `telefono` varchar(250) NOT NULL,
  `immagine_profilo` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `utente_registrato`
--

INSERT INTO `utente_registrato` (`id`, `nome`, `cognome`, `username`, `password`, `email`, `telefono`, `immagine_profilo`) VALUES
(32, 'genny', 'miola', 'genny99', '64e1b3832773f9f257e392f9c31428ec', 'gennymiola99@gmail.com', '3345203825', NULL),
(46, 'bob', 'sailor', 'bobsailor', '54db2d81c7fbe6e4d6269f66a039d76d', 'bobsailor1990@icloud.com', '0583581123', NULL),
(47, 'monika', 'lamberti', 'monika', 'c609d244e037c89183bebb11d5fccc1c', 'monikal@gmail.com', '', NULL),
(53, 'asia', 'fiori', 'asiafiori', '719cfa98553bae446d8b276f194898e3', 'asiafiori@gmail.com', '', NULL),
(56, 'maria', 'rossi', 'mariarossi', '0b9f724ce52169e7a4575ddd6ed8207c', 'mariarossi@gmail.com', '', NULL);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `argomento`
--
ALTER TABLE `argomento`
  ADD PRIMARY KEY (`codice`),
  ADD UNIQUE KEY `tipo` (`tipo`,`macro_argomento`) USING BTREE,
  ADD KEY `macroargomento` (`macro_argomento`),
  ADD KEY `utente_proprietario` (`id_utente`);

--
-- Indici per le tabelle `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`codice`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD KEY `argomento` (`argomento`),
  ADD KEY `coautore` (`coautore`),
  ADD KEY `grafica` (`grafica`),
  ADD KEY `id_utente` (`id_utente`);

--
-- Indici per le tabelle `commento`
--
ALTER TABLE `commento`
  ADD PRIMARY KEY (`codicecomm`),
  ADD KEY `post_relativo` (`codice_post`),
  ADD KEY `utente_inserisceCommento` (`id_utente`);

--
-- Indici per le tabelle `grafica`
--
ALTER TABLE `grafica`
  ADD PRIMARY KEY (`codice`);

--
-- Indici per le tabelle `post_esempio`
--
ALTER TABLE `post_esempio`
  ADD PRIMARY KEY (`codicepost`),
  ADD UNIQUE KEY `titolo` (`titolo`),
  ADD KEY `vincoloblog` (`codice_blog`),
  ADD KEY `vincolo_autore` (`codice_autore`),
  ADD KEY `argomento_post` (`argomento`),
  ADD KEY `vincolofont` (`font`);

--
-- Indici per le tabelle `utente_like`
--
ALTER TABLE `utente_like`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post` (`id_post`),
  ADD KEY `utente` (`id_utente`);

--
-- Indici per le tabelle `utente_registrato`
--
ALTER TABLE `utente_registrato`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unica` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `argomento`
--
ALTER TABLE `argomento`
  MODIFY `codice` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT per la tabella `blog`
--
ALTER TABLE `blog`
  MODIFY `codice` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT per la tabella `commento`
--
ALTER TABLE `commento`
  MODIFY `codicecomm` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=286;

--
-- AUTO_INCREMENT per la tabella `grafica`
--
ALTER TABLE `grafica`
  MODIFY `codice` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `post_esempio`
--
ALTER TABLE `post_esempio`
  MODIFY `codicepost` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT per la tabella `utente_like`
--
ALTER TABLE `utente_like`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT per la tabella `utente_registrato`
--
ALTER TABLE `utente_registrato`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `argomento`
--
ALTER TABLE `argomento`
  ADD CONSTRAINT `macroargomento` FOREIGN KEY (`macro_argomento`) REFERENCES `argomento` (`codice`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `utente_proprietario` FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Limiti per la tabella `blog`
--
ALTER TABLE `blog`
  ADD CONSTRAINT `argomento` FOREIGN KEY (`argomento`) REFERENCES `argomento` (`codice`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `coautore` FOREIGN KEY (`coautore`) REFERENCES `utente_registrato` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `grafica` FOREIGN KEY (`grafica`) REFERENCES `grafica` (`codice`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `id_utente` FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Limiti per la tabella `commento`
--
ALTER TABLE `commento`
  ADD CONSTRAINT `post_relativo` FOREIGN KEY (`codice_post`) REFERENCES `post_esempio` (`codicepost`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `utente_inserisceCommento` FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Limiti per la tabella `post_esempio`
--
ALTER TABLE `post_esempio`
  ADD CONSTRAINT `argomento_post` FOREIGN KEY (`argomento`) REFERENCES `argomento` (`codice`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `vincolo_autore` FOREIGN KEY (`codice_autore`) REFERENCES `utente_registrato` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `vincoloblog` FOREIGN KEY (`codice_blog`) REFERENCES `blog` (`codice`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `vincolofont` FOREIGN KEY (`font`) REFERENCES `grafica` (`codice`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Limiti per la tabella `utente_like`
--
ALTER TABLE `utente_like`
  ADD CONSTRAINT `post` FOREIGN KEY (`id_post`) REFERENCES `post_esempio` (`codicepost`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `utente` FOREIGN KEY (`id_utente`) REFERENCES `utente_registrato` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
