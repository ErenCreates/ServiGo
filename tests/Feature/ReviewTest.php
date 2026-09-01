<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Review;
use App\Models\Role;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $otherCustomer;
    protected User $providerUser;
    protected ServiceProvider $serviceProvider;
    protected ServiceCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'name'    => 'Ali Müşteri',
            'role_id' => Role::CUSTOMER,
        ]);

        $this->otherCustomer = User::factory()->create([
            'name'    => 'Veli Müşteri',
            'role_id' => Role::CUSTOMER,
        ]);

        $this->providerUser = User::factory()->create([
            'name'    => 'Hasan Usta',
            'role_id' => Role::PROVIDER,
        ]);

        $this->category = ServiceCategory::create([
            'name'        => 'Boya & Badana',
            'description' => 'İç ve dış cephe boya',
            'is_active'   => true,
        ]);

        $this->serviceProvider = ServiceProvider::create([
            'user_id'       => $this->providerUser->id,
            'category_id'   => $this->category->id,
            'company_name'  => 'Hasan Usta Boya Hizmetleri',
            'bio'           => 'Titiz ve kaliteli işçilik garantisi.',
            'working_hours' => 'Her gün 08:00 - 20:00',
        ]);
    }

    public function test_customer_can_review_completed_appointment(): void
    {
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->serviceProvider->id,
            'category_id' => $this->category->id,
            'description' => 'Salon ve koridor boyama.',
            'status'      => 'completed',
        ]);

        $appointment = Appointment::create([
            'service_request_id' => $serviceRequest->id,
            'customer_id'        => $this->customer->id,
            'provider_id'        => $this->serviceProvider->id,
            'appointment_date'   => now()->subDay(),
            'status'             => 'completed',
            'note'               => 'İş tamamlandı.',
        ]);

        $response = $this->actingAs($this->customer)->post("/musteri/randevular/{$appointment->id}/degerlendir", [
            'rating'  => 5,
            'comment' => 'İşçilik mükemmeldi, çok temiz çalıştılar.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'customer_id'        => $this->customer->id,
            'provider_id'        => $this->serviceProvider->id,
            'appointment_id'     => $appointment->id,
            'service_request_id' => $serviceRequest->id,
            'rating'             => 5,
            'comment'            => 'İşçilik mükemmeldi, çok temiz çalıştılar.',
            'is_approved'        => true,
        ]);
    }

    public function test_customer_cannot_review_scheduled_or_cancelled_appointment(): void
    {
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->serviceProvider->id,
            'category_id' => $this->category->id,
            'description' => 'Mutfak boyama.',
            'status'      => 'accepted',
        ]);

        $scheduledAppointment = Appointment::create([
            'service_request_id' => $serviceRequest->id,
            'customer_id'        => $this->customer->id,
            'provider_id'        => $this->serviceProvider->id,
            'appointment_date'   => now()->addDay(),
            'status'             => 'scheduled',
        ]);

        $response = $this->actingAs($this->customer)->post("/musteri/randevular/{$scheduledAppointment->id}/degerlendir", [
            'rating'  => 5,
            'comment' => 'Erken yorum yapmaya çalışıyorum.',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_review_requires_valid_rating_between_1_and_5(): void
    {
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->serviceProvider->id,
            'category_id' => $this->category->id,
            'description' => 'Oda boyama.',
            'status'      => 'completed',
        ]);

        $appointment = Appointment::create([
            'service_request_id' => $serviceRequest->id,
            'customer_id'        => $this->customer->id,
            'provider_id'        => $this->serviceProvider->id,
            'appointment_date'   => now()->subDays(2),
            'status'             => 'completed',
        ]);

        // Rating > 5
        $invalidResponse = $this->actingAs($this->customer)->post("/musteri/randevular/{$appointment->id}/degerlendir", [
            'rating'  => 6,
            'comment' => 'Çok iyi hizmet.',
        ]);
        $invalidResponse->assertSessionHasErrors('rating');

        // Rating < 1
        $zeroResponse = $this->actingAs($this->customer)->post("/musteri/randevular/{$appointment->id}/degerlendir", [
            'rating'  => 0,
            'comment' => 'Çok kötü hizmet.',
        ]);
        $zeroResponse->assertSessionHasErrors('rating');

        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_customer_cannot_review_other_customers_appointment(): void
    {
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->serviceProvider->id,
            'category_id' => $this->category->id,
            'description' => 'Boya işi.',
            'status'      => 'completed',
        ]);

        $appointment = Appointment::create([
            'service_request_id' => $serviceRequest->id,
            'customer_id'        => $this->customer->id,
            'provider_id'        => $this->serviceProvider->id,
            'appointment_date'   => now()->subDays(2),
            'status'             => 'completed',
        ]);

        // Other customer tries to review
        $response = $this->actingAs($this->otherCustomer)->post("/musteri/randevular/{$appointment->id}/degerlendir", [
            'rating'  => 4,
            'comment' => 'Başkasının randevusuna yorum.',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('reviews', 0);
    }

    public function test_craftsman_profile_displays_average_rating_and_reviews_list(): void
    {
        // Add 2 reviews for this craftsman: 5 and 4 -> Average should be 4.5
        Review::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->serviceProvider->id,
            'rating'      => 5,
            'comment'     => 'Harika ustalık, çok teşekkürler!',
            'is_approved' => true,
        ]);

        Review::create([
            'customer_id' => $this->otherCustomer->id,
            'provider_id' => $this->serviceProvider->id,
            'rating'      => 4,
            'comment'     => 'Zamanında geldi, temiz çalıştı.',
            'is_approved' => true,
        ]);

        $response = $this->actingAs($this->customer)->get("/usta-profili/{$this->serviceProvider->id}");
        $response->assertOk();
        $response->assertSee('4.5');
        $response->assertSee('2 Müşteri Değerlendirmesi');
        $response->assertSee('Harika ustalık, çok teşekkürler!');
        $response->assertSee('Zamanında geldi, temiz çalıştı.');
    }
}

