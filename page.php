<?php   
    
    if (isset($_GET['w'])) {
        $nSemaine = $_GET['w'];
    } else {
        $nSemaine = date('W');
    }
    $nSemaine = intval($nSemaine);

    $lMois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

    $date_ref = date('Y')."-01-01";

    $mLundi = date("m", strtotime("last Monday +$nSemaine weeks", strtotime($date_ref)));
    $mDimanche = date("m", strtotime("last Sunday +$nSemaine weeks", strtotime($date_ref)));

    if ($mDimanche == $mLundi) {
        $moisTxt = $lMois[intval($mLundi)-1];
    } else {
        $moisTxt = $lMois[intval($mLundi)-1].' - '.$lMois[intval($mDimanche)-1];
    }

    session_start();

    $files = ["http://ade.univ-tours.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?data=f998fbd46b339e409e6c6cbde361edb9c3489fd34f432173475bc090f1723f925bded9a340d88a4358f846a7a4472b4dfe28586de380bafe174e6e49b572b07d,1"/*, "http://ade.univ-tours.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?data=799faf6bf0b6a9fce5a794983d0c862d15dcab0f24b8913b5d144e852a5d9fe58a209302a3a57afb3553857383c37db83b35a62046be8482c8e9a74d112f972a,1"*/];

    $_SESSION["liens"]=$files;

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Emploi du temps</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<header>

<div id="autreSem">
    <a id="boutonAjd" href="page.php">Aujourd'hui</a>
    <a class="bF" href="page.php?w=<?php echo $nSemaine-1 ?>"><</a>
    <a class="bF" href="page.php?w=<?php echo $nSemaine+1 ?>">></a>
    <?php echo $moisTxt ;?>
</div>

</header>

<div id="contenu">

    <div id="gauche">
        <input type="date" name="date">
    </div>

	<?php include 'tableau.php' ?>
</div>

</body>
</html>