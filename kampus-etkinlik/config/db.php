<?php
// XAMPP varsayılan veritabanı ayarları
$host = 'localhost';
$dbname = 'kampus_db';
$username = 'root'; 
$password = ''; // XAMPP'ta şifre varsayılan olarak boştur

try {
    // PDO ile güvenli bağlantı kurulumu
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Hata gösterim modunu aktif ediyoruz (Geliştirme aşamasında işimize çok yarayacak)
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    // Eğer bağlantıda bir sorun olursa ekrana hata mesajı basar
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>