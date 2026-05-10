<?php
require_once 'config/db.php';
require_once 'includes/auth.php'; // Session dosyamızı çağırdık
require_once 'includes/header.php';

$mesaj = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $mesaj = '<div class="alert alert-danger">Lütfen e-posta ve şifrenizi girin.</div>';
    } else {
        // Veritabanında bu e-postaya sahip kullanıcıyı arıyoruz
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kullanıcı var mı ve şifresi doğru mu kontrolü (Hocanın istediği password_verify fonksiyonu)
        if ($user && password_verify($password, $user['password_hash'])) {
            // Giriş başarılı, kullanıcı bilgilerini session'a (oturum hafızasına) kaydediyoruz
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            
            // Başarılı girişten sonra ana sayfaya yönlendir
            header("Location: index.php");
            exit;
        } else {
            $mesaj = '<div class="alert alert-danger">E-posta veya şifre hatalı.</div>';
        }
    }
}
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4 class="mb-0">Giriş Yap</h4>
            </div>
            <div class="card-body">
                <?= $mesaj ?>
                
                <form action="login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">E-posta Adresiniz</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Şifreniz</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Giriş Yap</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>