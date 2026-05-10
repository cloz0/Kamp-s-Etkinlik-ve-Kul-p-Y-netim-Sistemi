<?php
require_once 'config/db.php';
require_once 'includes/header.php';

// Arama ve Filtreleme Parametrelerini Alıyoruz
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$club_filter = isset($_GET['club_id']) ? $_GET['club_id'] : '';

// SQL Sorgusunu Dinamik Olarak Hazırlıyoruz (Zorunlu Arama/Filtreleme Özelliği)
$sql = "SELECT events.*, clubs.name as club_name 
        FROM events 
        JOIN clubs ON events.club_id = clubs.id 
        WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (events.title LIKE :search OR events.location LIKE :search)";
    $params[':search'] = "%$search%";
}

if (!empty($club_filter)) {
    $sql .= " AND events.club_id = :club_id";
    $params[':club_id'] = $club_filter;
}

$sql .= " ORDER BY events.event_date ASC";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Filtreleme menüsü için kulüpleri çekiyoruz
$all_clubs = $db->query("SELECT id, name FROM clubs ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5>Filtrele & Ara</h5>
                <form action="events.php" method="GET">
                    <div class="mb-3">
                        <label class="form-label small">Etkinlik Ara</label>
                        <input type="text" name="q" class="form-control form-control-sm" value="<?= htmlspecialchars($search) ?>" placeholder="Başlık veya konum...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Kulüp Seçin</label>
                        <select name="club_id" class="form-select form-select-sm">
                            <option value="">Tümü</option>
                            <?php foreach ($all_clubs as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $club_filter == $c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100">Uygula</button>
                    <a href="events.php" class="btn btn-link btn-sm w-100 text-decoration-none mt-2 text-muted">Temizle</a>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <h2 class="mb-4">Güncel Etkinlikler</h2>
        <div class="row">
            <?php if (empty($events)): ?>
                <div class="col-12">
                    <div class="alert alert-warning">Aradığınız kriterlere uygun etkinlik bulunamadı.</div>
                </div>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm overflow-hidden">
                            <?php if ($event['poster_path']): ?>
                                <img src="<?= htmlspecialchars($event['poster_path']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <span class="badge bg-info text-dark mb-2"><?= htmlspecialchars($event['club_name']) ?></span>
                                <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                                <p class="small text-muted mb-1">📅 <?= date('d.m.Y H:i', strtotime($event['event_date'])) ?></p>
                                <p class="small text-muted mb-2">📍 <?= htmlspecialchars($event['location']) ?></p>
                            </div>
                            <div class="card-footer bg-white border-top-0 pt-0">
                                <a href="event_detail.php?id=<?= $event['id'] ?>" class="btn btn-success btn-sm w-100">Detay ve Başvuru</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>