<?php

include "webpage.php";
include "repertoire_generators/constants.php";
include "repertoire_generators/repertoire_list.php";



$repertoire_list = get_repertoire_list();

$repertoire = [];
$currentTimestamp = time();

foreach ($repertoire_list as $item) {
    $itemTimestamp = $item->timestamp;

    if ($itemTimestamp < $currentTimestamp) {
        continue;
    }

    $itemDayOfWeek = $dayNumberToName[date("w", $itemTimestamp)];
    $itemDayOfMonth = date("j", $itemTimestamp);
    $itemMonthName = $monthNumberToName[date("n", $itemTimestamp)];

    $itemDateString = "$itemDayOfWeek $itemDayOfMonth $itemMonthName";

    if (!isset($repertoire[$itemDateString])) {
        $repertoire[$itemDateString] = [];
    }

    $filmwebLink = 'https://www.filmweb.pl/search#/?query=' . urlencode($item->title);

    $repertoire[$itemDateString][] = [
        "time" => date("G:i", $itemTimestamp),
        "title" => $item->title,
        "ticketLink" => $item->ticketLink,
        "location" => $item->location,
        "filmwebLink" => $filmwebLink,
        "locationLink" => $item->locationLink,
    ];

    get_poster($item->title);
}



require 'index.view.php';
