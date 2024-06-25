<?php
session_start();  

echo "Before unsetting session: ";
print_r($_SESSION);

session_unset();  
echo "After unsetting session: ";
print_r($_SESSION);

session_destroy();  


if (session_status() == PHP_SESSION_NONE) {
    echo "Session destroyed successfully.";
} else {
    echo "Failed to destroy session.";
}

header("Location: login.php");  
exit();
?>