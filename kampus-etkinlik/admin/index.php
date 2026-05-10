<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

// Güvenlik: Sadece admin girebilir
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// 1. İSTATİSTİKLERİ ÇEKME (Hocanın istediği "Basit Rapor" kısmı)
$toplam_kulup = $db->query("SELECT COUNT(*) FROM clubs")->fetchColumn();
$toplam_etkinlik = $db->query("SELECT COUNT(*) FROM events")->fetchColumn();
$toplam_basvuru = $db->query("SELECT COUNT(*) FROM registrations")->fetchColumn();

// 2. BAŞVURANLAR LİSTESİ (Hangi öğrenci hangi etkinliğe başvurmuş)
$basvurular = $db->query("
    SELECT registrations.created_at, users.name as user_name, events.title as event_title
    FROM registrations
    JOIN users ON registrations.user_id = users.id
    JOIN events ON registrations.event_id = events.id
    ORDER BY registrations.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action active">Raporlar & Başvurular</a>
            <a href="clubs.php" class="list-group-item list-group-item-action">Kulüp Yönetimi</a>
            <a href="events.php" class="list-group-item list-group-item-action">Etkinlik Yönetimi</a>
            <a href="../index.php" class="list-group-item list-group-item-action text-primary">Siteye Dön</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <h4 class="mb-4">Sistem Raporu</h4>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white shadow-sm text-center py-3 border-0">
                    <h2 class="mb-0"><?= $toplam_kulup ?></h2>
                    <span>Toplam Kulüp</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white shadow-sm text-center py-3 border-0">
                    <h2 class="mb-0"><?= $toplam_etkinlik ?></h2>
                    <span>Toplam Etkinlik</span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark shadow-sm text-center py-3 border-0">
                    <h2 class="mb-0"><?= $toplam_basvuru ?></h2>
                    <span>Toplam Başvuru</span>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Son Başvurular Listesi</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Öğrenci Adı</th>
                                <th>Başvurduğu Etkinlik</th>
                                <th>Başvuru Tarihi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($basvurular as $b): ?>
                                <tr>
                                    <td><?= htmlspecialchars($b['user_name']) ?></td>
                                    <td><?= htmlspecialchars($b['event_title']) ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($b['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($basvurular)): ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Henüz sisteme düşen bir başvuru yok.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>