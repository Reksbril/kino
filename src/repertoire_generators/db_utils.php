<?php

include "constants.php";

function init_db(SQLite3 $database)
{
    $database->exec('CREATE TABLE IF NOT EXISTS cinema_webpages (name TEXT NOT NULL PRIMARY KEY, webpage BLOB NOT NULL, timestamp INTEGER NOT NULL)');
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
