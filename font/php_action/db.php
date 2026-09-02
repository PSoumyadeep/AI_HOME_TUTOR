<?php
$db_server = "sql112.infinityfree.com";
$db_user   = "if0_42482161";
$db_pass   = "Kn2DZfzihycY";
$db_name   = "if0_42482161_ai_home_tutor";
$conn = new mysqli($db_server, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed."]));
}
?>