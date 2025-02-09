<?php

include "constants.php";

class Movie
{
    public function __construct(
        public string $originalName,
        public string $filmwebName,
        public string $filmwebId,
    ) {}
}

function init_db(SQLite3 $database)
{
    $database->exec('CREATE TABLE IF NOT EXISTS cinema_webpages (name TEXT NOT NULL PRIMARY KEY, webpage BLOB NOT NULL, timestamp INTEGER NOT NULL)');
    $database->exec('CREATE TABLE IF NOT EXISTS movies (original_name TEXT NOT NULL PRIMARY KEY, filmweb_name TEXT NOT NULL, filmweb_id TEXT NOT NULL)');
}

function get_db()
{
    global $DB_FILE_NAME;

    $database = new SQLite3(filename: $DB_FILE_NAME);
    init_db($database);

    return $database;
}

function get_webpage(string $cinemaName, SQLite3 $database)
{
    $statement = $database->prepare('SELECT webpage, timestamp FROM cinema_webpages WHERE name = :cinemaName;');
    $statement->bindValue(':cinemaName', $cinemaName);
    $result = $statement->execute();

    return $result->fetchArray();
}

function insert_webpage($html, int $timestamp, string $cinemaName, SQLite3 $database)
{
    $statement = $database->prepare('INSERT INTO cinema_webpages VALUES (:cinemaName, :html, :timestamp);');
    $statement->bindValue(':html', $html);
    $statement->bindValue(':cinemaName', $cinemaName);
    $statement->bindValue(':timestamp', $timestamp);
    $statement->execute();
}

function delete_webpage(string $cinemaName, SQLite3 $database)
{
    $statement = $database->prepare('DELETE FROM cinema_webpages WHERE name = :cinemaName;');
    $statement->bindValue(':cinemaName', $cinemaName);
    $statement->execute();
}

function add_movie(Movie $movie, SQLite3 $database)
{
    $statement = $database->prepare('INSERT INTO movies VALUES (:originalName, :filmwebName, :filmwebId);');
    $statement->bindValue(':originalName', $movie->originalName);
    $statement->bindValue(':filmwebName', $movie->filmwebName);
    $statement->bindValue(':filmwebId', $movie->filmwebId);
    $statement->execute();
}

function get_movie_by_original_name(string $originalName, SQLite3 $database)
{
    $statement = $database->prepare('SELECT filmweb_name, filmweb_id FROM movies WHERE original_name = :originalName;');
    $statement->bindValue(':originalName', $originalName);
    $result = $statement->execute();

    $resultArray = $result->fetchArray();

    if ($resultArray == false) {
        return null;
    }
    return new Movie($originalName, $resultArray["filmweb_name"], $resultArray["filmweb_id"]);
}
