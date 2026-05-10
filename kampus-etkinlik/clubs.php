<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Veritabanındaki tüm kulüpleri çekiyoruz
$stmt = $db->query("SELECT * FROM clubs ORDER BY name ASC");
$clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row mt-4">
    <div class="col-12">
        <h2 class="mb-4">Öğrenci Kulüplerimiz</h2>
    </div>
    
    <?php if (empty($clubs)): ?>
        <div class="col-12">
            <div class="alert alert-info">Henüz sisteme kayıtlı bir kulüp bulunmuyor.</div>
        </div>
    <?php else: ?>
        <?php foreach ($clubs as $club): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><?= htmlspecialchars($club['name']) ?></h5>
                        <p class="card-text text-muted">
                            <?= htmlspecialchars(mb_strimwidth($club['description'], 0, 100, "...")) ?>
                        </p>
                    </div>
                    <div class="card-footer bg-white border-top-0">
                        <a href="events.php?club_id=<?= $club['id'] ?>" class="btn btn-outline-primary btn-sm w-100">
                            Bu Kulübün Etkinliklerini Gör
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>