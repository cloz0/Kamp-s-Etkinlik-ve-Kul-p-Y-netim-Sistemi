<?php
// Oturumu başlatıyoruz
session_start();

// Kullanıcı giriş yapmış mı diye kontrol eden basit bir fonksiyon
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
?>