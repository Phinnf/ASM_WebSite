<?php

$host = "localhost";
$port = "5432";
$dbname = "computer_sale";
$user = "postgres";
$password = "123";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");

if (!$conn) {
    echo "An error occurred while connecting to the database.";
} else {

}
?>