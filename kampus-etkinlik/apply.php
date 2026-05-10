<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

// Güvenlik: Sadece giriş yapanlar başvuru yapabilir
if (!isLoggedIn()) {
    die("<script>alert('Başvuru yapmak için giriş yapmalısınız.'); window.location.href='login.php';</script>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id'];
    
    // 1. Etkinliğin Kontenjan Bilgisini Çekiyoruz
    $stmt = $db->prepare("SELECT quota FROM events WHERE id = :id");
    $stmt->execute([':id' => $event_id]);
    $event = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        die("Etkinlik bulunamadı.");
    }

    // 2. Kullanıcı Zaten Başvurmuş mu Kontrolü (Aynı etkinliğe 2 kez başvurulmasın)
    $check_stmt = $db->prepare("SELECT id FROM registrations WHERE event_id = :event_id AND user_id = :user_id");
    $check_stmt->execute([':event_id' => $event_id, ':user_id' => $user_id]);
    if ($check_stmt->rowCount() > 0) {
        die("<script>alert('Bu etkinliğe zaten başvurdunuz!'); window.location.href='event_detail.php?id=$event_id';</script>");
    }

    // 3. Hocanın İstediği: Kontenjan Kontrolü
    if ($event['quota'] > 0) { // Quota 0'dan büyükse sınır vardır
        $count_stmt = $db->prepare("SELECT COUNT(*) as total FROM registrations WHERE event_id = :event_id");
        $count_stmt->execute([':event_id' => $event_id]);
        $current_applications = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Eğer mevcut başvuru sayısı kontenjana ulaştıysa veya aştıysa hata ver
        if ($current_applications >= $event['quota']) {
            die("<script>alert('Maalesef bu etkinlik için kontenjan dolmuştur.'); window.location.href='event_detail.php?id=$event_id';</script>");
        }
    }

    // 4. Her şey tamamsa Başvuruyu Kaydet (PDO ile Güvenli Ekleme)
    $insert_stmt = $db->prepare("INSERT INTO registrations (event_id, user_id) VALUES (:event_id, :user_id)");
    $insert_stmt->execute([
        ':event_id' => $event_id,
        ':user_id' => $user_id
    ]);

    // Başarılı olunca JS ile uyarı verip Profil sayfasına yolluyoruz
    echo "<script>alert('Başvurunuz başarıyla alındı!'); window.location.href='profile.php';</script>";
} else {
    header("Location: index.php");
}
?>