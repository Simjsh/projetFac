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

$file = "http://ade.univ-tours.fr/jsp/custom/modules/plannings/anonymous_cal.jsp?data=f998fbd46b339e409e6c6cbde361edb9c3489fd34f432173475bc090f1723f925bded9a340d88a4358f846a7a4472b4dfe28586de380bafe174e6e49b572b07d,1";

$events = getIcsEvents($file);

function comparer_dates($a, $b) {
    $timestamp_a = $a['date']->getTimestamp();
    $timestamp_b = $b['date']->getTimestamp();
    if ($timestamp_a == $timestamp_b) {
        return 0;
    }
    return ($timestamp_a < $timestamp_b) ? -1 : 1;
}

usort($events, 'comparer_dates');

function duree($h1, $m1, $h2, $m2) {
    $heure1 = $h1 + ($m1)/60;
    $heure2 = $h2 + ($m2)/60;
    return $heure1 - $heure2;
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

echo '<div id="tableau">';

foreach($ljours as $jour) {
    echo '<div class="jour">';
    echo '<div class="nomJour"><h1>'.$jour.'</h1></div>';
    echo '<div class="dateJour">'.date("d", strtotime("last $days[$jour] +$nSemaine weeks", strtotime($date_ref))).'</div>';
    echo '<div class="eventsJour">';

    $heureDernier = 07;
    $minDernier = 0;

    foreach($eventsJour[$jour] as $e) {
        $taille = duree($e['heure2'], $e['minute2'], $e['heure1'], $e['minute1'])*50;
        $tempsAvant = duree($e['heure1'], $e['minute1'], $heureDernier, $minDernier)*50;

        echo '<div class="event" style="height: '.$taille.'px; margin-top: '.$tempsAvant.'px">';
           
        echo '<h2 class="event__title">' . $e['titre'] . '</h2>';
        
        /*echo '<div class="event__location">' . $e['location'] . '</div>';*/

        /*echo '<div class="event__description">' . str_replace('\n', '<br>', $e['description']) . '</div>';*/

        /*echo '<div class="event__date">'.'<p>'.$e['jour'].'/'.$e['mois'].'/'.$e['annee'].'</p>'.'<p>'.$e['heure1'].':'.$e['minute1'].' à '.$e['heure2'].':'.$e['minute2'].'</p></div>';*/

        echo '<p>'.$e['date']->format('H:i').'</p>';

        echo '</div>';

        $heureDernier = $e['heure2'];
        $minDernier = $e['minute2'];
    }
    echo '</div></div>';
}

echo '</div>';
?>