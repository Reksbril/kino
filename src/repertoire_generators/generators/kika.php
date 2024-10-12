<?php

function kika_generator($html)
{
    global $monthNameToNumber;

    // Load the HTML into DOMDocument
    $dom = new DOMDocument;
    libxml_use_internal_errors(true); // Disable warnings for invalid HTML
    $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
    $dom->loadHTML($html);
    libxml_clear_errors();

    // Use DOMXPath to query the data
    $xpath = new DOMXPath($dom);

    // Find all the repertoire sections
    $repertoireContainer = $xpath->query('//div[@class="col-xs-12 col-sm-12 col-md-12 col-lg-10"]');

    // TODO report error
    // assert(sizeof($repertoireContainer) == 1);

    $repertoireElements = $repertoireContainer->item(0);

    // TODO report error
    // assert(sizeof($internalContainer) == 1);

    // Helper variables
    $currentItemDateDay = 0;
    $currentItemDateMonth = 0;

    foreach ($repertoireElements->childNodes as $repertoireElement) {

        if (!($repertoireElement instanceof DOMElement)) {
            continue;
        }

        $dateSeparatorQueryResult = $xpath->query('.//div[contains(@class, "date-separator")]', $repertoireElement);

        // day separator
        if (sizeof($dateSeparatorQueryResult) > 0) {
            $dateSeparatorElement = $dateSeparatorQueryResult->item(0);
            $dateFull = $dateSeparatorElement->nodeValue; // "niedziela, 15 września 2024"

            if ($dateFull == "Brak wydarzeń w wybranym dniu") {
                continue;
            }

            $datePieces = explode(" ", $dateFull);

            $currentItemDateDay = $datePieces[1];
            $currentItemDateMonth = $monthNameToNumber[$datePieces[2]];
            continue;
        }

        $titleLinkQuery = $xpath->query('.//a[starts-with(@title, "Kup bilet")]', $repertoireElement);

        if (sizeof($titleLinkQuery) > 0) {
            $titleLinkElement = $titleLinkQuery->item(0);

            $title = $titleLinkElement->nodeValue;
            $ticketLink = "https://bilety.kinokika.pl/" . $titleLinkElement->getAttribute("href");

            $timeLocationQuery = $xpath->query('.//div[contains(@class, "date")]', $repertoireElement);
            $timeLocation = $timeLocationQuery->item(0)->nodeValue; // "Kino Agrafka, Krowoderska 8 Agrafka niedziela, 15 września 2024 godz. 17:30"
            $timeLocationParts = explode(",", $timeLocation);

            $location = $timeLocationParts[0];

            $dateTime = trim($timeLocationParts[2]); // "15 września 2024 godz. 17:30"
            $dateTimeParts = explode(" ", $dateTime);
            $time = $dateTimeParts[sizeof($dateTimeParts) - 1];

            $timePieces = explode(":", $time);
            if (sizeof($timePieces) != 2) {
                //TODO message
                continue;
            }

            $hour = $timePieces[0];
            $minute = $timePieces[1];

            if (!is_numeric($hour) || !is_numeric($minute)) {
                continue;
            }

            $dateTime = new DateTime();
            $dateTime->setTime(hour: $hour, minute: $minute);
            # might work incorectly if actual date is in next year (for repertoire around end of year)
            $year = date('Y');
            $dateTime->setDate(year: $year, month: $currentItemDateMonth, day: $currentItemDateDay);

            yield new RepertoireItem(
                timestamp: $dateTime->getTimestamp(),
                title: trim($title),
                location: $location,
                ticketLink: $ticketLink,
            );
        }
    }
}
