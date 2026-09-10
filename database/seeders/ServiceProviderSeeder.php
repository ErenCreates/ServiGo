<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ServiceProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            ['name' => 'Ahmet Usta Tesisat', 'cat' => 'Su Tesisatı', 'lat' => 40.9901, 'lng' => 29.0284, 'hours' => '08:30 - 18:00', 'bio' => 'Kadıköy ve çevresinde 15 yıllık su tesisatı, batarya değişimi ve kaçak tespit tecrübesi.'],
            ['name' => 'Mehmet Usta Elektrik', 'cat' => 'Elektrik', 'lat' => 41.0428, 'lng' => 29.0077, 'hours' => '09:00 - 19:00', 'bio' => 'Beşiktaş merkezli, avize montajı, sigorta değişimi ve elektrik arıza onarımı.'],
            ['name' => 'Ali Usta Boya & Dekorasyon', 'cat' => 'Boya & Badana', 'lat' => 41.0602, 'lng' => 28.9877, 'hours' => '08:00 - 17:30', 'bio' => 'Şişli ve tüm Avrupa yakasında temiz, kaliteli boya badana ve duvar kağıdı hizmeti.'],
            ['name' => 'Hasan Usta Marangoz & Mobilya', 'cat' => 'Mobilya Montajı', 'lat' => 41.0265, 'lng' => 29.0163, 'hours' => '09:00 - 18:30', 'bio' => 'Üsküdar merkezli mobilya kurulumu, tamiri ve özel ahşap tasarımları.'],
            ['name' => 'Emre Usta Isıtma & Kombi', 'cat' => 'Kombi & Isıtma', 'lat' => 39.9208, 'lng' => 32.8541, 'hours' => '08:30 - 19:00', 'bio' => 'Çankaya / Ankara bölgesinde petek temizleme, kombi arıza ve periyodik bakım hizmeti.'],
            ['name' => 'Can Usta Temizlik Hizmetleri', 'cat' => 'Temizlik', 'lat' => 38.4192, 'lng' => 27.1287, 'hours' => '08:00 - 17:00', 'bio' => 'Konak / İzmir bölgesinde ev ve ofis detaylı temizlik hizmetleri.'],
        ];

        foreach ($locations as $index => $loc) {
            $user = User::firstOrCreate(
                ['email' => 'usta' . ($index + 1) . '@servigo.com'],
                [
                    'name'              => $loc['name'],
                    'password'          => Hash::make('password'),
                    'role_id'           => Role::PROVIDER,
                    'email_verified_at' => now(),
                ]
            );

            $category = ServiceCategory::where('name', $loc['cat'])->first();

            ServiceProvider::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'category_id'   => $category ? $category->id : 1,
                    'company_name'  => $loc['name'],
                    'bio'           => $loc['bio'],
                    'working_hours' => $loc['hours'],
                    'latitude'      => $loc['lat'],
                    'longitude'     => $loc['lng'],
                ]
            );
        }

        // Mevcut latitude/longitude verisi boş olan tüm ustaları Türkiye koordinatları ile güncelle
        $existingProviders = ServiceProvider::whereNull('latitude')->orWhereNull('longitude')->get();
        $sampleCoordinates = [
            ['lat' => 40.9818, 'lng' => 29.0576], // Kadıköy
            ['lat' => 41.0428, 'lng' => 29.0076], // Beşiktaş
            ['lat' => 41.0601, 'lng' => 28.9876], // Şişli
            ['lat' => 39.9207, 'lng' => 32.8541], // Ankara
            ['lat' => 38.4192, 'lng' => 27.1287], // İzmir
        ];

        foreach ($existingProviders as $pIndex => $p) {
            $coord = $sampleCoordinates[$pIndex % count($sampleCoordinates)];
            $p->update([
                'latitude'  => $coord['lat'],
                'longitude' => $coord['lng'],
            ]);
        }
    }
}

