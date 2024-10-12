<?php

function pod_baranami_generator($html)
{
    global $monthNameToNumber;

    // Load the HTML into DOMDocument
    $dom = new DOMDocument;
    libxml_use_internal_errors(true); // Disable warnings for invalid HTML

    $dom->loadHTML($html);

    libxml_clear_errors();

    // Use DOMXPath to query the data
    $xpath = new DOMXPath($dom);

    // Find all the repertoire sections
    $repertoireSection = $xpath->query('//div[@id="column_wide"]');

    // TODO report error
    // assert(sizeof($repertoireSection) == 1);

    $repertoireElements = $repertoireSection->item(0);

    // Helper variables
    $currentItemDateDay = 0;
    $currentItemDateMonth = 0;

    foreach ($repertoireElements->childNodes as $repertoireElement) {
        // print_r($repertoireElement);

        if (!($repertoireElement instanceof DOMElement)) {
            continue;
        }

        // day separator
        if ($repertoireElement->className == "rep_date") {
            $dateFull = $repertoireElement->nodeValue; // "Sobota 14 września // Saturday, September 14"

            $datePolish = trim(explode("//", $dateFull)[0]); // "Sobota 14 września"
            $datePolishPieces = explode(" ", $datePolish);

            $currentItemDateDay = $datePolishPieces[1];
            $currentItemDateMonth = $monthNameToNumber[$datePolishPieces[2]];
            continue;
        }

        if ($repertoireElement->className == "program_list") {
            $currentDayRepetoire = $xpath->query('.//li', $repertoireElement);

            foreach ($currentDayRepetoire as $movie) {
                if (!($movie instanceof DOMElement)) {
                    // raise error
                    continue;
                }

                $location = trim($movie->className) == "mos-warning" ? "Pod Baranami (MOS)" : "Pod Baranami";

                $titleAndHour = $xpath->query('.//a', $movie);

                if ((sizeof($titleAndHour) != 2 && trim($movie->className) != "mos-warning") || (sizeof($titleAndHour) != 3 && trim($movie->className) == "mos-warning")) {
                    // TODO message
                    continue;
                }

                $title = $titleAndHour->item(0)->nodeValue;
                $title = trim(str_replace("(seans w MOS)", "", $title));
                $timeIndex = sizeof($titleAndHour) - 1;

                $time = $titleAndHour->item($timeIndex)->nodeValue;
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

                $indirectTicketLink = $titleAndHour->item($timeIndex)->getAttribute('href');


                // Parse the query string
                parse_str(parse_url($indirectTicketLink, PHP_URL_QUERY), $queryParams);

                // Get the event_id
                $eventId = $queryParams['event_id'];

                $ticketLink = "https://rezerwacja.kinopodbaranami.pl/Rezerwacja/default.aspx?event_id=" . $eventId . "&typetran=0&returnlink=http://kinopodbaranami.pl/rezerwacja_koniec.php&buylink=http://kinopodbaranami.pl/rezerwacja_koniec.php";

                yield new RepertoireItem(
                    timestamp: $dateTime->getTimestamp(),
                    title: trim($title),
                    location: trim($location),
                    ticketLink: $ticketLink,
                );
            }
        }
    }
}
