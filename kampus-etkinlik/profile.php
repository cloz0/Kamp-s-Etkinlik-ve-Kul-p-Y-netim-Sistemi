<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

// Güvenlik: Sadece giriş yapanlar görebilir
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$mesaj = '';

// İptal Etme İşlemi
if (isset($_GET['cancel_id'])) {
    $cancel_id = $_GET['cancel_id'];
    // Güvenlik: Sadece kullanıcının "kendi" başvurusunu silebildiğinden emin oluyoruz
    $del_stmt = $db->prepare("DELETE FROM registrations WHERE id = :id AND user_id = :user_id");
    $del_stmt->execute([':id' => $cancel_id, ':user_id' => $user_id]);
    $mesaj = '<div class="alert alert-success">Başvurunuz başarıyla iptal edildi.</div>';
}

// Kullanıcının Başvurduğu Etkinlikleri Veritabanından Çekme
$sql = "SELECT registrations.id as reg_id, registrations.created_at as apply_date, 
               events.title, events.event_date, events.location, clubs.name as club_name
        FROM registrations
        JOIN events ON registrations.event_id = events.id
        JOIN clubs ON events.club_id = clubs.id
        WHERE registrations.user_id = :user_id
        ORDER BY registrations.created_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute([':user_id' => $user_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name']) ?>&background=0d6efd&color=fff" class="rounded-circle mb-3" alt="Profil">
                <h5><?= htmlspecialchars($_SESSION['user_name']) ?></h5>
                <p class="text-muted mb-0"><?= $_SESSION['user_role'] == 'admin' ? 'Yönetici' : 'Öğrenci' ?></p>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <?= $mesaj ?>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h5 class="mb-0">Başvurularım</h5>
            </div>
            <div class="card-body">
                <?php if (empty($applications)): ?>
                    <div class="alert alert-info">Henüz hiçbir etkinliğe başvurmadınız. <a href="events.php">Etkinlikleri Keşfet!</a></div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Etkinlik</th>
                                    <th>Kulüp</th>
                                    <th>Tarih</th>
                                    <th>Başvuru Tarihi</th>
                                    <th>İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($app['title']) ?></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($app['club_name']) ?></span></td>
                                        <td><?= date('d.m.Y H:i', strtotime($app['event_date'])) ?></td>
                                        <td><?= date('d.m.Y', strtotime($app['apply_date'])) ?></td>
                                        <td>
                                            <a href="profile.php?cancel_id=<?= $app['reg_id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Başvurunuzu iptal etmek istediğinize emin misiniz?')">İptal Et</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>