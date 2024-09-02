<?php
session_start();
include('../config/config.php');


        echo "No user found with that username!";

?>

<form method="POST" action="">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>
