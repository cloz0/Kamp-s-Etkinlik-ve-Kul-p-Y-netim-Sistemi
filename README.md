# Kampus Etkinlik ve Kulup Yonetim Sistemi

Öğrenci kulüplerinin ve kampüs etkinliklerinin dijital ortamda yönetilmesini sağlayan web tabanlı bir otomasyon sistemidir. Web Programlama dersi proje ödevi kapsamında geliştirilmiştir.

## 💻 Kullanılan Teknolojiler
* **Frontend:** HTML5, CSS3, Bootstrap 5, JavaScript
* **Backend:** PHP (PDO Mimarisi)
* **Veritabanı:** MySQL

## 📌 Temel Özellikler

**Öğrenci (Kullanıcı) Paneli:**
* Kulüp ve etkinlik listeleme, dinamik arama ve filtreleme.
* Etkinliklere başvuru yapma (kontenjan bazlı).
* Profil paneli üzerinden aktif başvuruları görüntüleme ve iptal etme.
* İstemci tarafı (Client-side) JavaScript form doğrulamaları.

**Yönetici (Admin) Paneli:**
* Güvenli oturum (Session) ve yetki kontrolü.
* Kulüp ekleme, silme ve düzenleme (CRUD).
* Afiş yükleme (Upload) destekli etkinlik yönetimi.
* Sisteme başvuran öğrencilerin takibi ve genel istatistik raporları.

## ⚙️ Kurulum ve Çalıştırma
Projeyi yerel sunucunuzda (localhost) çalıştırmak için aşağıdaki adımları izleyin:

1. Proje dosyalarını indirin ve XAMPP dizinindeki `htdocs` klasörünün içine aktarın.
2. XAMPP kontrol panelinden **Apache** ve **MySQL** servislerini başlatın.
3. Tarayıcınızdan `localhost/phpmyadmin` adresine gidin.
4. `kampus_db` adında yeni bir veritabanı oluşturun (Karakter seti: `utf8mb4_general_ci` olmalıdır).
5. Proje ana dizininde bulunan `database.sql` dosyasını oluşturduğunuz bu veritabanına içe aktarın (Import).
6. Tarayıcınızdan `localhost/kampus-etkinlik` (veya belirlediğiniz klasör adı) adresine giderek projeyi çalıştırın.

---
**Hazırlayan:** Berkan 
**Öğrenci No:** 0
