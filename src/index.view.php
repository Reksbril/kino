<!DOCTYPE html>
<html lang="pl-pl">

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="styles.css">
    <title>Repertuar kin studyjnych</title>
</head>

<head>
    <div class="head_text"> Repertuar kin w Krakowie </div>
</head>

<body>
    <div class="main_container">

        <?php

        foreach ($repertoire as $day => $dayRepertoire) {
            $rows = [];

            foreach ($dayRepertoire as $item) {
                $time = $item["time"];
                $ticketLink = $item["ticketLink"];
                $title = $item["title"];
                $location = $item["location"];
                $filmwebLink = $item["filmwebLink"];

                $rows[] = <<<EOD
                    <div class="repertoire_element_container">
                        <a href=$filmwebLink> <img src="posters/$title.jpg" alt=""> </a>
                        <div class="time_container"> $time </div> 
                        <a href=$ticketLink> $title </a> 
                        <div class="location_container"> $location </div>
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

        ?>

    </div>
</body>


</html>