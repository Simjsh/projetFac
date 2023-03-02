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

<div id="autreSem">
    <a href="page.php?w=<?php echo $nSemaine-1 ?>"><</a>
    <a href="page.php">Cette semaine</a>
    <a href="page.php?w=<?php echo $nSemaine+1 ?>">></a>
    <?php echo $moisTxt ;?>
</div>

<div >
	<?php include 'tableau.php' ?>
</div>

</body>
</html>