<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Veritabanından en yakın tarihli 3 etkinliği çekiyoruz (Öne çıkanlar)
$stmt = $db->query("
    SELECT events.*, clubs.name as club_name 
    FROM events 
    JOIN clubs ON events.club_id = clubs.id 
    WHERE events.event_date >= NOW() 
    ORDER BY events.event_date ASC 
    LIMIT 3
");
$featured_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="p-5 mb-4 bg-white rounded-3 shadow-sm border mt-4">
    <div class="container-fluid py-4">
        <h1 class="display-5 fw-bold text-primary">Kampüse Hoş Geldin!</h1>
        <p class="col-md-8 fs-5 mt-3">En güncel öğrenci kulüplerini keşfet, etkinliklere katıl, sosyalleş ve kampüs hayatının tadını sonuna kadar çıkar.</p>
        <a href="events.php" class="btn btn-primary btn-lg mt-2">Etkinlikleri Keşfet</a>
    </div>
</div>

<h3 class="mb-4 mt-5 border-bottom pb-2">Öne Çıkan Etkinlikler</h3>
<div class="row">
    <?php if (empty($featured_events)): ?>
        <div class="col-12">
            <div class="alert alert-info">Şu an için planlanan yeni etkinlik bulunmuyor.</div>
        </div>
    <?php else: ?>
        <?php foreach ($featured_events as $event): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm event-card-custom">
                    <?php if ($event['poster_path']): ?>
                        <img src="<?= htmlspecialchars($event['poster_path']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <span class="badge bg-info text-dark mb-2"><?= htmlspecialchars($event['club_name']) ?></span>
                        <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                        <p class="card-text text-muted small">
                            📅 <?= date('d.m.Y H:i', strtotime($event['event_date'])) ?><br>
                            📍 <?= htmlspecialchars($event['location']) ?>
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-0">
                        <a href="event_detail.php?id=<?= $event['id'] ?>" class="btn btn-outline-primary w-100">Detayları Gör</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>