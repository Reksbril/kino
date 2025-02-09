<?php

include "db_utils.php";
include "filmweb.php";

function transform_names_to_filmweb_names(&$repertoire_list)
{
    $database = get_db();

    foreach ($repertoire_list as $element) {
        $movie = get_movie_by_original_name($element->title, $database);
        if ($movie != null) {
            $element->title = $movie->filmwebName;
            continue;
        }

        $id_title = get_movie_id_and_title_from_filmweb($element->title);

        if ($id_title == null) {
            continue;
        }

        list($id, $title) = $id_title;

        add_movie(new Movie($element->title, $title, $id), $database);
        $element->title = $title;
    }
}


function postprocess($generator)
{
    $repertoire_list = [...$generator];

    transform_names_to_filmweb_names($repertoire_list);

    return $repertoire_list;
}
