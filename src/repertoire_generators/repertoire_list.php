<?php

include "repertoire_item.php";
include "postprocess.php";

include "generators/pod_baranami.php";
include "generators/mikro.php";
include "generators/kika.php";


enum Cinema: string
{
    case Mikro = "mikro";
    case PodBaranami = "pod_baranami";
    case Kika = "kika";
}

$CINEMA_TO_URL = [
    "mikro" => "https://kinomikro.pl/repertoire/?view=all",
    "pod_baranami" => "https://www.kinopodbaranami.pl/repertuar.php",
    "kika" => "https://bilety.kinokika.pl/",
];


function minRepertoireItem(array $repertoireItems): ?RepertoireItem
{
    if (empty($repertoireItems)) {
        return null; //raise error
    }

    $result = null;

    foreach ($repertoireItems as $item) {
        if ($result == null || $item->timestamp < $result->timestamp) {
            $result = $item;
        }
    }

    return $result;
}


// Merge generators in ascending order
function mergeGenerators(...$generators)
{
    $currentValues = [];

    // Initialize current values for each generator
    foreach ($generators as $index => $generator) {
        if ($generator->valid()) {
            $currentValues[$index] = $generator->current();
        }
    }

    while (!empty($currentValues)) {

        // Find the index of the generator with the smallest value
        $minIndex = array_keys($currentValues, minRepertoireItem($currentValues))[0];
        // print_r($minIndex);

        // Yield the smallest value
        yield $currentValues[$minIndex];

        // Move the generator forward
        $generators[$minIndex]->next();

        // Update the current value or remove it if the generator is exhausted
        if ($generators[$minIndex]->valid()) {
            $currentValues[$minIndex] = $generators[$minIndex]->current();
        } else {
            unset($currentValues[$minIndex]);
        }
    }
}

function get_html(Cinema $cinema, SQLite3 $database)
{
    global $CINEMA_TO_URL;
    global $MAX_REPERTOIRE_CACHE_KEEP_TIME_SECONDS;

    $cinemaName = $cinema->value;

    $cachedWebpageResult = get_webpage($cinemaName, $database);

    $currentTimestamp = time();

    if ($cachedWebpageResult == null) {
        $url = $CINEMA_TO_URL[$cinemaName];
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $html = curl_exec($curl);
        curl_close($curl);

        insert_webpage(base64_encode($html), $currentTimestamp, $cinemaName, $database);

        return $html;
    }

    if ($currentTimestamp - $cachedWebpageResult["timestamp"] > $MAX_REPERTOIRE_CACHE_KEEP_TIME_SECONDS) {
        delete_webpage($cinemaName, $database);

        $url = $CINEMA_TO_URL[$cinemaName];
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $html = curl_exec($curl);
        curl_close($curl);

        insert_webpage(base64_encode($html), $currentTimestamp, $cinemaName, $database);

        return $html;
    }

    return base64_decode($cachedWebpageResult["webpage"]);
}

function get_generator()
{
    $database = get_db();

    $html_pod_baranami = get_html(Cinema::PodBaranami, $database);
    $html_mikro = get_html(Cinema::Mikro, $database);
    $html_kika = get_html(Cinema::Kika, $database);

    return mergeGenerators(
        mikro_generator($html_mikro),
        pod_baranami_generator($html_pod_baranami),
        kika_generator($html_kika),
    );
}


function get_repertoire_list()
{
    $generator = get_generator();
    return postprocess($generator);
}
