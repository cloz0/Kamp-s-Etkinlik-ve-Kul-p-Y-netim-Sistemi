<?php
require_once 'config/db.php';
require_once 'includes/header.php';

$mesaj = '';

// Form gönderildiğinde çalışacak kısım
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // PHP tarafında boş alan kontrolü
    if (empty($name) || empty($email) || empty($password)) {
        $mesaj = '<div class="alert alert-danger">Lütfen tüm alanları doldurun.</div>';
    } else {
        // Şifreyi güvenli hale getiriyoruz
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // PDO Prepared Statements ile güvenli ekleme
            $sql = "INSERT INTO users (name, email, password_hash) VALUES (:name, :email, :password_hash)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password_hash' => $hashed_password
            ]);
            
            $mesaj = '<div class="alert alert-success">Kayıt başarılı! Şimdi giriş yapabilirsiniz.</div>';
        } catch (PDOException $e) {
            $mesaj = '<div class="alert alert-danger">Bu e-posta adresi zaten kullanılıyor.</div>';
        }
    }
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">Kayıt Ol</h4>
            </div>
            <div class="card-body">
                <?= $mesaj ?>
                
                <form action="register.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Adınız Soyadınız</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-posta Adresiniz</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Şifreniz</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Kayıt Ol</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>