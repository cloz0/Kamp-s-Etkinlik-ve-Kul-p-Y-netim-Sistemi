<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

// GÜVENLİK: Sadece admin girebilir
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$mesaj = '';

// 1. SİLME İŞLEMİ
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM events WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $mesaj = '<div class="alert alert-success">Etkinlik başarıyla silindi.</div>';
}

// 2. EKLEME VE DOSYA YÜKLEME İŞLEMİ
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_event'])) {
    $club_id = $_POST['club_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $event_date = $_POST['event_date'];
    $location = trim($_POST['location']);
    $quota = $_POST['quota'];
    
    $poster_path = '';

    // HOCANIN ZORUNLU TUTTUĞU DOSYA YÜKLEME (UPLOAD) KISMI
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) {
        $uzanti = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
        // Dosya isimleri çakışmasın diye benzersiz bir isim üretiyoruz
        $yeni_isim = time() . '_' . uniqid() . '.' . $uzanti;
        $hedef = '../uploads/' . $yeni_isim;
        
        // Dosyayı geçici klasörden asıl uploads klasörümüze taşıyoruz
        if (move_uploaded_file($_FILES['poster']['tmp_name'], $hedef)) {
            $poster_path = 'uploads/' . $yeni_isim; // Veritabanına bu yolu kaydedeceğiz
        }
    }

    if (empty($title) || empty($club_id) || empty($event_date)) {
        $mesaj = '<div class="alert alert-danger">Başlık, Kulüp ve Tarih alanları zorunludur.</div>';
    } else {
        $stmt = $db->prepare("INSERT INTO events (club_id, title, description, event_date, location, quota, poster_path) 
                              VALUES (:club_id, :title, :description, :event_date, :location, :quota, :poster_path)");
        $stmt->execute([
            ':club_id' => $club_id,
            ':title' => $title,
            ':description' => $description,
            ':event_date' => $event_date,
            ':location' => $location,
            ':quota' => $quota,
            ':poster_path' => $poster_path
        ]);
        $mesaj = '<div class="alert alert-success">Etkinlik ve afiş başarıyla eklendi!</div>';
    }
}

// Formda göstermek için kulüpleri çekiyoruz
$clubs = $db->query("SELECT id, name FROM clubs ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Listelemek için etkinlikleri (kulüp adlarıyla birlikte) çekiyoruz
$events = $db->query("
    SELECT events.*, clubs.name as club_name 
    FROM events 
    LEFT JOIN clubs ON events.club_id = clubs.id 
    ORDER BY events.event_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action">Yönetim Paneli</a>
            <a href="clubs.php" class="list-group-item list-group-item-action">Kulüp Yönetimi</a>
            <a href="events.php" class="list-group-item list-group-item-action active">Etkinlik Yönetimi</a>
            <a href="../index.php" class="list-group-item list-group-item-action text-primary">Siteye Dön</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <?= $mesaj ?>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Yeni Etkinlik Ekle</h5>
            </div>
            <div class="card-body">
                <form action="events.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Etkinlik Başlığı</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Düzenleyen Kulüp</label>
                            <select name="club_id" class="form-control" required>
                                <option value="">Kulüp Seçin...</option>
                                <?php foreach ($clubs as $club): ?>
                                    <option value="<?= $club['id'] ?>"><?= htmlspecialchars($club['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Etkinlik Açıklaması</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tarih ve Saat</label>
                            <input type="datetime-local" name="event_date" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Konum</label>
                            <input type="text" name="location" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Kontenjan (0 = Sınırsız)</label>
                            <input type="number" name="quota" class="form-control" value="0" min="0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Etkinlik Afişi Yükle</label>
                        <input type="file" name="poster" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" name="add_event" class="btn btn-primary w-100">Etkinlik Ekle</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Kayıtlı Etkinlikler</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Afiş</th>
                                <th>Başlık</th>
                                <th>Kulüp</th>
                                <th>Tarih</th>
                                <th>Kontenjan</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                            <tr>
                                <td>
                                    <?php if ($event['poster_path']): ?>
                                        <img src="../<?= $event['poster_path'] ?>" alt="Afiş" style="height: 40px; border-radius:4px;">
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Yok</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($event['title']) ?></td>
                                <td><?= htmlspecialchars($event['club_name']) ?></td>
                                <td><?= date('d.m.Y H:i', strtotime($event['event_date'])) ?></td>
                                <td><?= $event['quota'] > 0 ? $event['quota'] . ' Kişi' : 'Sınırsız' ?></td>
                                <td>
                                    <a href="events.php?delete=<?= $event['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($events)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Henüz kayıtlı etkinlik bulunmuyor.</td>
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