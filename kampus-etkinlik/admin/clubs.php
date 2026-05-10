<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

// GÜVENLİK: Sadece admin girebilir
if (!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$mesaj = '';

// 1. SİLME İŞLEMİ (Eğer URL'de ?delete=id varsa çalışır)
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $db->prepare("DELETE FROM clubs WHERE id = :id");
    $stmt->execute([':id' => $delete_id]);
    $mesaj = '<div class="alert alert-success">Kulüp başarıyla silindi.</div>';
}

// 2. EKLEME İŞLEMİ (Form gönderildiğinde çalışır)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_club'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (empty($name)) {
        $mesaj = '<div class="alert alert-danger">Kulüp adı boş bırakılamaz.</div>';
    } else {
        // Güvenli veritabanı ekleme (Prepared Statements)
        $stmt = $db->prepare("INSERT INTO clubs (name, description) VALUES (:name, :description)");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description
        ]);
        $mesaj = '<div class="alert alert-success">Kulüp başarıyla eklendi.</div>';
    }
}

// 3. LİSTELEME İŞLEMİ (Veritabanındaki kulüpleri en yenisi en üstte olacak şekilde çekeriz)
$clubs = $db->query("SELECT * FROM clubs ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

require_once '../includes/header.php';
?>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="list-group shadow-sm">
            <a href="index.php" class="list-group-item list-group-item-action">Yönetim Paneli</a>
            <a href="clubs.php" class="list-group-item list-group-item-action active">Kulüp Yönetimi</a>
            <a href="events.php" class="list-group-item list-group-item-action">Etkinlik Yönetimi</a>
            <a href="../index.php" class="list-group-item list-group-item-action text-primary">Siteye Dön</a>
        </div>
    </div>
    
    <div class="col-md-9">
        <?= $mesaj ?>
        
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Yeni Kulüp Ekle</h5>
            </div>
            <div class="card-body">
                <form action="clubs.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Kulüp Adı</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Açıklama</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" name="add_club" class="btn btn-primary">Kulüp Ekle</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Kayıtlı Kulüpler</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Kulüp Adı</th>
                            <th>Açıklama</th>
                            <th width="100">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clubs as $club): ?>
                        <tr>
                            <td><?= $club['id'] ?></td>
                            <td><?= htmlspecialchars($club['name']) ?></td>
                            <td><?= htmlspecialchars($club['description']) ?></td>
                            <td>
                                <a href="clubs.php?delete=<?= $club['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($clubs)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Henüz kayıtlı kulüp bulunmuyor.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>