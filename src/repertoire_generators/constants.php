<?php

$MAX_REPERTOIRE_CACHE_KEEP_TIME_SECONDS = 24 * 60 * 60; // 24 hours
$DB_FILE_NAME = "/var/databases/kino-app/cached_webpages.sqlite";
$POSTERS_DIR = "/var/www/html/posters";

$dayNumberToName = [
    0 => "Niedziela",
    1 => "Poniedziałek",
    2 => "Wtorek",
    3 => "Środa",
    4 => "Czwartek",
    5 => "Piątek",
    6 => "Sobota",
];

$dayNameToNumber = array_flip($dayNumberToName);

$monthNameToNumber = [
    "stycznia" => 1,
    "lutego" => 2,
    "marca" => 3,
    "kwietnia" => 4,
    "maja" => 5,
    "czerwca" => 6,
    "lipca" => 7,
    "sierpnia" => 8,
    "września" => 9,
    "października" => 10,
    "listopada" => 11,
    "grudnia" => 12,
];

$monthNumberToName = array_flip($monthNameToNumber);
