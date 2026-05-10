<?php
require_once 'config/db.php';
require_once 'includes/auth.php';

// URL'den etkinlik ID'sini al (Örn: event_detail.php?id=3)
if (!isset($_GET['id'])) {
    die("Etkinlik bulunamadı.");
}
$event_id = $_GET['id'];

// Etkinlik bilgilerini kulüp adıyla beraber çek
$stmt = $db->prepare("SELECT events.*, clubs.name as club_name FROM events JOIN clubs ON events.club_id = clubs.id WHERE events.id = :id");
$stmt->execute([':id' => $event_id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    die("Böyle bir etkinlik yok.");
}

require_once 'includes/header.php'; 
?>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card event-card-custom shadow-sm mb-4">
            <?php if ($event['poster_path']): ?>
                <img src="<?= htmlspecialchars($event['poster_path']) ?>" class="card-img-top" style="max-height: 400px; object-fit: cover;">
            <?php endif; ?>
            <div class="card-body">
                <span class="badge bg-primary mb-2"><?= htmlspecialchars($event['club_name']) ?></span>
                <h2><?= htmlspecialchars($event['title']) ?></h2>
                <p class="text-muted">📅 Tarih: <?= date('d.m.Y H:i', strtotime($event['event_date'])) ?></p>
                <p class="text-muted">📍 Konum: <?= htmlspecialchars($event['location']) ?></p>
                <p class="text-muted">👥 Kontenjan: <?= $event['quota'] > 0 ? $event['quota'] . ' Kişi' : 'Sınırsız' ?></p>
                <hr>
                <p class="fs-5"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Etkinliğe Başvur</h5>
            </div>
            <div class="card-body">
                <?php if (isLoggedIn()): ?>
                    <form id="applyForm" action="apply.php" method="POST">
                        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Başvuru Notunuz (Zorunlu)</label>
                            <textarea id="user_note" name="user_note" class="form-control" rows="3" placeholder="Neden katılmak istiyorsunuz?"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Başvurumu Gönder</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning text-center">Başvuru yapmak için giriş yapmalısınız.</div>
                    <a href="login.php" class="btn btn-primary w-100">Giriş Yap</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>