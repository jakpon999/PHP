<?php
// argumenty to nazwa hosta, nazwa użytkownika, hasło, nazwa bazy danych
$con = mysqli_connect("localhost", "root", "", "canvas");
//test połączenia
if(mysqli_connect_errno()) {
    echo "Błąd przy łączeniu z bazą danych" . $mysqli_connect_error();
}
?>