<!DOCTYPE html>
<html lang="pl-pl">

<head>
    <meta charset="utf-8">
    <script src="dropdown.js"></script>
    <link rel="stylesheet" href="styles.css">
    <title>Repertuar kin studyjnych</title>
</head>

<body>
    <h1>Repertuar kin studyjnych w Krakowie</h1>

    <div class="filter_container">
        <div class="dropdown">
            <button class="dropdown_button" onclick="toggleDropdown('titleDropdown')">Filtruj po tytule ▼</button>
            <div id="titleDropdown" class="dropdown_content"></div>
        </div>

        <div class="dropdown">
            <button class="dropdown_button" onclick="toggleDropdown('locationDropdown')">Filtruj po lokalizacji ▼</button>
            <div id="locationDropdown" class="dropdown_content"></div>
        </div>
    </div>

    <div class="main_container">
        <?php
        $titles = [];
        $locations = [];

        foreach ($repertoire as $day => $dayRepertoire) {
            $rows = [];
            foreach ($dayRepertoire as $item) {
                $time = $item["time"];
                $ticketLink = $item["ticketLink"];
                $title = $item["title"];
                $location = $item["location"];
                $filmwebLink = $item["filmwebLink"];
                $locationLink = $item["locationLink"];

                if (!in_array($title, $titles)) $titles[] = $title;
                if (!in_array($location, $locations)) $locations[] = $location;

                $rows[] = <<<EOD
                        <div class="repertoire_element_container" data-title="$title" data-location="$location">
                            <a href="$filmwebLink"> <img src="posters/$title.jpg" alt=""> </a>
                            <div class="time_container"> $time </div> 
                            <a href="$ticketLink" class="title">$title</a> 
                            <a href="$locationLink"> <div class="location_container">$location</div> </a> 
                        </div>
                    EOD;
            }

            $rows_text = implode("", $rows);
            echo <<<EOD
                    <div class="day_container">
                        <div class="day_container_day_name"> $day </div>
                        <div class="repertoire_rows_container">
                            $rows_text 
                        </div>
                    </div>
                EOD;
        }

        echo "<script>let availableTitles = " . json_encode($titles) . "; let availableLocations = " . json_encode($locations) . ";</script>";
        ?>
    </div>
</body>

</html>