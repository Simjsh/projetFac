<?php

function getIcsEvents($file) {
    $icalString = file_get_contents($file);

    preg_match_all('/BEGIN:VEVENT(.*?)END:VEVENT/s', $icalString, $matches, PREG_SET_ORDER);

    $events = [];

    foreach ($matches as $match) {
        $eventString = $match[0];

        $title = getIcsValue($eventString, "SUMMARY");
        $description = getIcsValue($eventString, "DESCRIPTION");
        $start = getIcsValue($eventString, "DTSTART");
        $end = getIcsValue($eventString, "DTEND");
        $location = getIcsValue($eventString, "LOCATION");

        $dateTimeStart = DateTime::createFromFormat('Ymd\THis\Z', $start);
        $dateTimeEnd = DateTime::createFromFormat('Ymd\THis\Z', $end);

        $dateTimeStart->setTimezone(new DateTimeZone('Europe/Paris'));
        $dateTimeEnd->setTimezone(new DateTimeZone('Europe/Paris'));


        $jour = $dateTimeStart->format('d');
        $mois = $dateTimeStart->format('m');
        $heureStart = $dateTimeStart->format('H');
        $minuteStart = $dateTimeStart->format('i');
        $heureEnd = $dateTimeEnd->format('H');
        $minuteEnd = $dateTimeEnd->format('i');

        $event = array();
        $event['titre'] = $title;
        $event['description'] = $description;
        $event['location'] = $location;
        $event['jour'] = $jour;
        $event['mois'] = $mois;
        $event['heure1'] = $heureStart;
        $event['heure2'] = $heureEnd;
        $event['minute1'] = $minuteStart;
        $event['minute2'] = $minuteEnd;
        $event['date'] = $dateTimeStart;
        $event['date2'] = $dateTimeEnd;

        array_push($events, $event);
    }

    return $events;
}

function getIcsValue($eventString, $fieldName) {
    preg_match('/'.$fieldName.'(.*?):(.*?)\r\n/', $eventString, $matches);

    if (count($matches) == 0) {
        return "";
    }

    return trim($matches[2]);
}

$files = ["http://ade.univ-tours.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?data=f998fbd46b339e409e6c6cbde361edb9c3489fd34f432173475bc090f1723f925bded9a340d88a4358f846a7a4472b4dfe28586de380bafe174e6e49b572b07d,1"/*, "http://ade.univ-tours.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?data=799faf6bf0b6a9fce5a794983d0c862d15dcab0f24b8913b5d144e852a5d9fe58a209302a3a57afb3553857383c37db83b35a62046be8482c8e9a74d112f972a,1"*/];

$events = array();

foreach($_SESSION["liens"] as $file) {
    $events = array_merge($events, getIcsEvents($file));
}

function comparer_dates($a, $b) {
    $timestamp_a = $a['date']->getTimestamp();
    $timestamp_b = $b['date']->getTimestamp();
    if ($timestamp_a == $timestamp_b) {
        return 0;
    }
    return ($timestamp_a < $timestamp_b) ? -1 : 1;
}

$events = array_map('unserialize', array_unique(array_map('serialize', $events))); // On enlève les doublons
usort($events, 'comparer_dates');   // On trie les cours en fonction de leur date

function duree($h1, $m1, $h2, $m2) {
    $heure1 = $h1 + ($m1)/60;
    $heure2 = $h2 + ($m2)/60;
    return abs($heure1 - $heure2);
}

$ljours = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"];

$days = array(
    'Lundi' => 'Monday',
    'Mardi' => 'Tuesday',
    'Mercredi' => 'Wednesday',
    'Jeudi' => 'Thursday',
    'Vendredi' => 'Friday',
    'Samedi' => 'Saturday',
    'Dimanche' => 'Sunday'
);

$eventsJour = [
    "Lundi"=>[], 
    "Mardi"=>[], 
    "Mercredi"=>[], 
    "Jeudi"=>[], 
    "Vendredi"=>[], 
    "Samedi"=>[], 
    "Dimanche"=>[]
];

foreach ($events as $e) {
    if ($e['date']->format('W') == $nSemaine) {
        $jour = $ljours[$e['date']->format('N')-1];
        array_push($eventsJour[$jour], $e);  
    }
}

echo '<div id="tableau">';?>

<div id="heures">
    <?php for ($h = 7; $h<=21; $h++):?>
    <p style="top: <?php echo ($h-5)*50 - 25?>px"><?php echo $h ?>:00</p>
    <?php endfor ?>
</div>

<?php foreach($ljours as $jour) : ?>
<div class="jour">
    <div class="nomJour">
        <h1><?php echo $jour ?></h1>
        <div class="date"><?php echo date("d", strtotime("last $days[$jour] +$nSemaine weeks", strtotime($date_ref))) ?></div>
    </div>
    <div class="eventsJour">

    <?php $heureDernier = 07;
    $minDernier = 0; ?>
    <?php foreach($eventsJour[$jour] as $e) : ?>
        <?php $hauteur = duree($e['heure2'], $e['minute2'], $e['heure1'], $e['minute1'])*50;
        $tempsAvant = duree($e['heure1'], $e['minute1'], $heureDernier, $minDernier)*50;?>

        <div class="event" style=" <?php echo 'height:  '.$hauteur.'px; margin-top: '.$tempsAvant.'px' ?>">
           
            <h2 class="event__title"><?php echo $e['titre'] ?></h2>
            <div class="event__location"><?php echo $e['location'] ?></div>
            <div class="event__date"><p><?php echo $e['heure1'].':'.$e['minute1'].' à '.$e['heure2'].':'.$e['minute2'] ?></p></div>

        </div>
        <?php $heureDernier = $e['heure2'];
        $minDernier = $e['minute2']; ?>
    <?php endforeach?>
</div>
</div>

<?php endforeach ?>

</div>