# ServiGo - Servis ve Usta Bulma Platformu

ServiGo; ev veya iş yerinde tadilat, bakım ve onarım ihtiyacı olan müşteriler ile elektrik, sıhhi tesisat gibi farklı alanlarda hizmet veren ustaları doğrudan buluşturan iki taraflı bir pazar yeri (marketplace) web uygulamasıdır.

Proje; Laravel (PHP) çatısı, MySQL ilişkisel veritabanı, Tailwind CSS ve Leaflet.js kütüphanesi kullanılarak MVC (Model-View-Controller) mimarisiyle geliştirilmiştir.

---

## Proje Kapsamı ve Özellikler

* Çok Rollü Kimlik Doğrulama: Müşteri, Hizmet Sağlayıcı (Usta) ve Yönetici (Admin) için rol bazlı kayıt ve giriş akışları.
* Kategori ve Usta Arama: Kategori bazlı filtreleme ve arama kutusu üzerinden anahtar kelime sorgulama.
* Konum ve Harita Entegrasyonu (Leaflet.js): Usta profillerinde OpenStreetMap tabanlı interaktif harita gösterimi ve ustanın harita üzerinden koordinat (enlem/boylam) belirleyebilmesi.
* Talep ve Randevu Akışı: Müşterinin talep oluşturması, ustanın talebi inceleyerek randevuya dönüştürmesi (Fetch API / AJAX destekli).
* Değerlendirme ve Puanlama: Yalnızca hizmeti tamamlanmış müşterilerin 1-5 arası puan ve yorum bırakabilmesi, ortalama puanın dinamik hesaplanması.
* Yönetici Paneli: Kategori yönetimi (CRUD), kullanıcı denetimi, platform istatistikleri ve moderasyon araçları.

---

## Mimari Karar: Rol ve Yetki Yönetimi

Şartnamede belirtilen rol/yetki mimarisi araştırması doğrultusunda; sistemdeki aktörlerin sınırları net ve hiyerarşik (Müşteri, Usta, Admin) olduğu için harici bir paket (Spatie vb.) yerine Laravel'in yerleşik yapısına uygun özel bir Middleware (CheckRole) geliştirilmiştir.

Bu tercihin gerekçeleri:
1. Dış paket bağımlılığını en aza indirmek.
2. Basit rol kontrolleri için fazladan veritabanı tabloları ve karmaşık izin sorguları oluşturmayarak performansı korumak.
3. Rota güvenliğini doğrudan çekirdek katmanda yönetmek.

---

## Kullanıcı Rolleri ve Yetki Matrisi

| Yetki / İşlem | Müşteri | Usta | Yönetici (Admin) |
| :--- | :---: | :---: | :---: |
| Kayıt Olma ve Giriş | Evet | Evet | Evet |
| Kategori Listeleme ve Arama | Evet | Hayır | Evet |
| Hizmet Talebi Oluşturma | Evet | Hayır | Hayır |
| Talep Kabul / Reddetme | Hayır | Evet | Hayır |
| Randevu Takvimi Yönetimi | Görüntüleme / İptal | Görüntüleme / Güncelleme | Tam Yetki |
| Harita Konumu Güncelleme | Hayır | Evet | Hayır |
| Yorum ve Puan Verme | Evet (Tamamlanan işe) | Hayır | Moderasyon / Silme |
| Kategori Yönetimi (CRUD) | Hayır | Hayır | Evet |
| Sistem İstatistiklerini Görme | Hayır | Hayır | Evet |

---

## Veritabanı Modeli ve İlişkiler

* users tablosu ile service_providers arasında 1-e-1 ilişki bulunur (hasOne / belongsTo).
* service_categories tablosu ile service_providers arasında 1-e-Çok ilişki bulunur (hasMany / belongsTo).
* users (müşteri) ile service_requests arasında 1-e-Çok ilişki bulunur (hasMany).
* service_requests tablosu ile appointments arasında 1-e-1 ilişki bulunur.
* service_providers ile reviews arasında 1-e-Çok ilişki bulunur (hasMany).

---

## Kurulum Adımları

Projeyi yerel ortamda çalıştırmak için:

### 1. Depoyu Klonlayın
    git clone https://github.com/ErenCreates/ServiGo.git
    cd ServiGo

### 2. Bağımlılıkları Kurun
    composer install
    npm install

### 3. Çevre Değişkenlerini Yapılandırın
    cp .env.example .env
    php artisan key:generate

.env dosyasındaki MySQL bağlantı ayarlarını düzenleyin:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=servigo
    DB_USERNAME=root
    DB_PASSWORD=

### 4. Veritabanını ve Test Verilerini Yükleyin
    php artisan migrate:fresh --seed

### 5. Sunucuyu Başlatın
İki ayrı terminal penceresinde frontend derleyicisini ve yerel sunucuyu çalıştırın:
    npm run dev
    php artisan serve

Tarayıcıdan http://127.0.0.1:8000 adresine gidin.

---

## Test Hesapları

Sistemdeki tüm hazır test hesaplarının şifresi 12345678 olarak ayarlanmıştır:

* Admin: admin@servigo.com
* Müşteri: musteri1@servigo.com ... musteri10@servigo.com
* Usta: usta1@servigo.com ... usta10@servigo.com (İstanbul koordinatları ile tanımlıdır)

---

## Test Paketi

Uygulamanın ana senaryoları PHPUnit ile doğrulanmıştır:
    php artisan test

Yazılan testler:
* AuthTest: Rol bazlı kayıt, giriş ve yetkisiz erişim kontrolleri.
* AppointmentTest: Talep kabulü ve randevu oluşturma süreçleri.
* ReviewTest: Puanlama kuralları ve mükerrer yorum engelleme.
* AjaxRequestStatusTest: Fetch API ile durum güncelleme akışları.
* ProviderLocationMapTest: Harita koordinatlarının kaydedilmesi ve veri tutarlılığı.
