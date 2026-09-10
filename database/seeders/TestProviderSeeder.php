<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates 10 test craftsmen with emails usta1@servigo.com to usta10@servigo.com
     * with standard password '12345678' and realistic Turkey coordinates.
     */
    public function run(): void
    {
        // 10 Farklı Test Ustası Bilgileri ve Türkiye Koordinatları
        $testProviders = [
            [
                'name'         => 'Ahmet Usta Tesisat',
                'company_name' => 'Ahmet Usta Sıhhi Tesisat & Kaçak Tespiti',
                'category'     => 'Su Tesisatı',
                'lat'          => 40.990142,
                'lng'          => 29.028421,
                'hours'        => '08:30 - 18:30',
                'bio'          => 'Kadıköy ve Anadolu yakasında 15 yıllık su tesisatı, batarya değişimi, pimaş tıkanıklığı açma ve kırmadan kaçak tespiti uzmanlığı.',
            ],
            [
                'name'         => 'Mehmet Usta Elektrik',
                'company_name' => 'Mehmet Elektrik & Aydınlatma Çözümleri',
                'category'     => 'Elektrik',
                'lat'          => 41.042846,
                'lng'          => 29.007685,
                'hours'        => '09:00 - 19:00',
                'bio'          => 'Beşiktaş merkezli; avize montajı, sigorta kutusu değişimi, kısa devre onarımı ve 7/24 acil elektrik servisi.',
            ],
            [
                'name'         => 'Ali Usta Boya Badana',
                'company_name' => 'Ali Usta Dekoratif Boya & Badana',
                'category'     => 'Boya & Badana',
                'lat'          => 41.060156,
                'lng'          => 28.987661,
                'hours'        => '08:00 - 18:00',
                'bio'          => 'Şişli ve tüm Avrupa yakasında temiz, tozsuz, kaliteli boya badana, alçı çekme ve duvar kağıdı uygulamaları.',
            ],
            [
                'name'         => 'Hasan Usta Mobilya',
                'company_name' => 'Hasan Usta Mobilya Montaj & Marangoz',
                'category'     => 'Mobilya Montajı',
                'lat'          => 41.026489,
                'lng'          => 29.016335,
                'hours'        => '09:00 - 18:00',
                'bio'          => 'Üsküdar ve çevresinde IKEA, Koçtaş, Vivense mobilya kurulumu, ray dolap tamiri ve özel ahşap kesim işleri.',
            ],
            [
                'name'         => 'Emre Usta Kombi Servisi',
                'company_name' => 'Emre Isıtma & Kombi Bakım Servisi',
                'category'     => 'Kombi & Isıtma',
                'lat'          => 39.920770,
                'lng'          => 32.854110,
                'hours'        => '08:30 - 19:30',
                'bio'          => 'Ankara Çankaya ve Kızılay çevresinde kombi yıllık bakımı, petek temizliği ve termostat montajı.',
            ],
            [
                'name'         => 'Can Usta Ev Temizliği',
                'company_name' => 'Can Profesyonel Temizlik Hizmetleri',
                'category'     => 'Temizlik',
                'lat'          => 38.419249,
                'lng'          => 27.128720,
                'hours'        => '08:00 - 17:00',
                'bio'          => 'İzmir Konak ve Alsancak bölgesinde detaylı ev temizliği, inşaat sonrası temizlik ve koltuk yıkama hizmeti.',
            ],
            [
                'name'         => 'Murat Usta İklimlendirme',
                'company_name' => 'Murat Klima Bakım & Montaj',
                'category'     => 'Klima & Havalandırma',
                'lat'          => 36.884140,
                'lng'          => 30.705630,
                'hours'        => '08:30 - 20:00',
                'bio'          => 'Antalya Muratpaşa ve Konyaaltı bölgesinde her marka klima montajı, gaz dolumu ve filtre dezenfeksiyonu.',
            ],
            [
                'name'         => 'Burak Usta Çilingir',
                'company_name' => 'Burak Çilingir & Kilit Sistemleri',
                'category'     => 'Çilingir',
                'lat'          => 40.978210,
                'lng'          => 28.872340,
                'hours'        => '00:00 - 23:59',
                'bio'          => 'Bakırköy ve Florya bölgesinde 7/24 kapı açma, çelik kapı kilit değişimi ve barel yenileme hizmeti.',
            ],
            [
                'name'         => 'Kemal Usta Beyaz Eşya',
                'company_name' => 'Kemal Usta Beyaz Eşya Özel Servisi',
                'category'     => 'Beyaz Eşya',
                'lat'          => 40.216667,
                'lng'          => 28.983333,
                'hours'        => '09:00 - 18:30',
                'bio'          => 'Bursa Nilüfer ve Osmangazi genelinde çamaşır makinesi, bulaşık makinesi ve buzdolabı tamir servisi.',
            ],
            [
                'name'         => 'Süleyman Usta Tadilat',
                'company_name' => 'Süleyman Usta Anahtar Teslim Tadilat',
                'category'     => 'Tadilat & Dekorasyon',
                'lat'          => 41.018240,
                'lng'          => 28.945320,
                'hours'        => '08:00 - 18:00',
                'bio'          => 'Fatih ve çevresinde banyo yenileme, mutfak tezgahı, seramik döşeme ve komple ev tadilatı.',
            ],
        ];

        // Kategoriler
        $categories = ServiceCategory::all();
        if ($categories->isEmpty()) {
            $this->call(ServiceCategorySeeder::class);
            $categories = ServiceCategory::all();
        }

        foreach ($testProviders as $index => $providerData) {
            $emailIndex = $index + 1;
            $email = "usta{$emailIndex}@servigo.com";

            // Usta Kullanıcısı (Password: 12345678)
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name'              => $providerData['name'],
                    'password'          => Hash::make('12345678'),
                    'role_id'           => Role::PROVIDER,
                    'is_active'         => true,
                    'email_verified_at' => now(),
                ]
            );

            // Kategori eşleştir veya rastgele bir kategori seç
            $category = $categories->firstWhere('name', $providerData['category']) ?? $categories->random();

            // Usta Profili & Konum Bilgisi
            ServiceProvider::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'category_id'   => $category ? $category->id : 1,
                    'company_name'  => $providerData['company_name'],
                    'bio'           => $providerData['bio'],
                    'working_hours' => $providerData['hours'],
                    'latitude'      => $providerData['lat'],
                    'longitude'     => $providerData['lng'],
                ]
            );
        }
    }
}

