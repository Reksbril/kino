<?php

$MIKRO_BASE_ADDRESS = "https://kinomikro.pl/";

function mikro_generator($html)
{
    global $MIKRO_BASE_ADDRESS;

    // Load the HTML into DOMDocument
    $dom = new DOMDocument;
    libxml_use_internal_errors(true); // Disable warnings for invalid HTML
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    $dom->loadHTML($html);
    libxml_clear_errors();

    // Use DOMXPath to query the data
    $xpath = new DOMXPath($dom);

    // Find all the repertoire sections
    $repertoireSections = $xpath->query('//section[@class="row"]');

    // Helper variables
    $currentItemDateDay = 0;
    $currentItemDateMonth = 0;

    foreach ($repertoireSections as $section) {
        $date = $xpath->query('.//div[contains(@class, "repertoire-separator")]', $section)->item(0)->nodeValue ?? '';

        # Assumed date format
        # "<day of week> - DD/MM"
        if ($date) {
            if ($date == "Dzisiaj") {
                $currentItemDateDay = date('j');
                $currentItemDateMonth = date('n');
            } else {
                $dateSpacePieces = explode(" ", $date);
                $dayMonth = $dateSpacePieces[2];
                $dayMonthPieces = explode("/", $dayMonth);
                $currentItemDateDay = $dayMonthPieces[0];
                $currentItemDateMonth = $dayMonthPieces[1];
            }
        }

        $time = $xpath->query('.//p[contains(@class, "repertoire-item-hour")]', $section)->item(0)->nodeValue ?? '';
        $title = $xpath->query('.//a[contains(@class, "repertoire-item-title")]', $section)->item(1)->nodeValue ?? '';
        $location = $xpath->query('.//p[contains(@class, "repertoire-item-location")]', $section)->item(0)->nodeValue ?? '';
        if ($location === "Sala Mikro" || $location == "Sala Mikrofala") {
            $location = "Kino Mikro";
        } else {
            $location = "Kino Mikro - Galeria Bronowice";
        }


        $ticketLink = $xpath->query('.//a[contains(@class, "repertoire-item-button")]', $section)->item(0)->getAttribute('href') ?? '';

        $timePieces = explode(":", $time);
        $hour = $timePieces[0];
        $minute = $timePieces[1];

        $dateTime = new DateTime();
        $dateTime->setTime(hour: $hour, minute: $minute);
        # might work incorectly if actual date is in next year (for repertoire around end of year)
        $year = date('Y');
        $dateTime->setDate(year: $year, month: $currentItemDateMonth, day: $currentItemDateDay);

        $result = new RepertoireItem(
            timestamp: $dateTime->getTimestamp(),
            title: trim($title),
            location: trim($location),
            ticketLink: $MIKRO_BASE_ADDRESS . trim($ticketLink),
            locationLink: $MIKRO_BASE_ADDRESS,
        );

        yield $result;
    }
}
