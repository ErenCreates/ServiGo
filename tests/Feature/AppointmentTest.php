<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Role;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppointmentTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected User $providerUser;
    protected ServiceProvider $serviceProvider;
    protected ServiceCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::factory()->create([
            'name'    => 'Ahmet Müşteri',
            'role_id' => Role::CUSTOMER,
        ]);

        $this->providerUser = User::factory()->create([
            'name'    => 'Mehmet Usta',
            'role_id' => Role::PROVIDER,
        ]);

        $this->category = ServiceCategory::create([
            'name'        => 'Elektrik & Tesisat',
            'description' => 'Elektrik arıza onarım',
            'is_active'   => true,
        ]);

        $this->serviceProvider = ServiceProvider::create([
            'user_id'       => $this->providerUser->id,
            'category_id'   => $this->category->id,
            'company_name'  => 'Usta Elektrik Hizmetleri',
            'bio'           => '15 yıllık tecrübe ile hizmetinizdeyiz.',
            'working_hours' => 'Hafta içi 09:00 - 18:00',
        ]);
    }

    public function test_provider_accepts_request_with_date_and_creates_appointment(): void
    {
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->serviceProvider->id,
            'category_id' => $this->category->id,
            'description' => 'Mutfak prizi arızası ve sigorta değişimi.',
            'status'      => 'pending',
        ]);

        $appointmentTime = now()->addDays(2)->setHour(14)->setMinute(30)->format('Y-m-d H:i:s');

        $response = $this->actingAs($this->providerUser)->post("/provider/requests/{$serviceRequest->id}/accept", [
            'appointment_date' => $appointmentTime,
            'note'             => 'Alet çantası ile geleceğim.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('service_requests', [
            'id'     => $serviceRequest->id,
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('appointments', [
            'service_request_id' => $serviceRequest->id,
            'customer_id'        => $this->customer->id,
            'provider_id'        => $this->serviceProvider->id,
            'status'             => 'scheduled',
            'note'               => 'Alet çantası ile geleceğim.',
        ]);
    }

    public function test_provider_can_view_appointments_and_complete_appointment(): void
    {
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->serviceProvider->id,
            'category_id' => $this->category->id,
            'description' => 'Avize montajı.',
            'status'      => 'accepted',
        ]);

        $appointment = Appointment::create([
            'service_request_id' => $serviceRequest->id,
            'customer_id'        => $this->customer->id,
            'provider_id'        => $this->serviceProvider->id,
            'appointment_date'   => now()->addDay(),
            'status'             => 'scheduled',
            'note'               => 'Montaj yapılacak yer hazır olmalı.',
        ]);

        // 1. View provider appointments list
        $response = $this->actingAs($this->providerUser)->get('/usta/randevular');
        $response->assertOk();
        $response->assertSee('Ahmet Müşteri');
        $response->assertSee('Avize montajı');

        // 2. Complete appointment
        $completeResponse = $this->actingAs($this->providerUser)->patch("/usta/randevular/{$appointment->id}/tamamla");
        $completeResponse->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'id'     => $appointment->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('service_requests', [
            'id'     => $serviceRequest->id,
            'status' => 'completed',
        ]);
    }

    public function test_provider_can_reschedule_and_cancel_appointment(): void
    {
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->serviceProvider->id,
            'category_id' => $this->category->id,
            'description' => 'Kombi bakımı.',
            'status'      => 'accepted',
        ]);

        $appointment = Appointment::create([
            'service_request_id' => $serviceRequest->id,
            'customer_id'        => $this->customer->id,
            'provider_id'        => $this->serviceProvider->id,
            'appointment_date'   => now()->addDays(2),
            'status'             => 'scheduled',
        ]);

        // Reschedule
        $newDate = now()->addDays(5)->format('Y-m-d H:i:s');
        $rescheduleResponse = $this->actingAs($this->providerUser)->patch("/usta/randevular/{$appointment->id}/ertele", [
            'appointment_date' => $newDate,
            'note'             => 'Tarih usta talebiyle ertelendi.',
        ]);
        $rescheduleResponse->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'id'   => $appointment->id,
            'note' => 'Tarih usta talebiyle ertelendi.',
        ]);

        // Cancel
        $cancelResponse = $this->actingAs($this->providerUser)->patch("/usta/randevular/{$appointment->id}/iptal");
        $cancelResponse->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'id'     => $appointment->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_customer_can_view_and_cancel_appointments(): void
    {
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->serviceProvider->id,
            'category_id' => $this->category->id,
            'description' => 'Klima gazı dolumu.',
            'status'      => 'accepted',
        ]);

        $appointment = Appointment::create([
            'service_request_id' => $serviceRequest->id,
            'customer_id'        => $this->customer->id,
            'provider_id'        => $this->serviceProvider->id,
            'appointment_date'   => now()->addDays(3),
            'status'             => 'scheduled',
            'note'               => 'Bina giriş şifresi 1234.',
        ]);

        // 1. View customer appointments list
        $response = $this->actingAs($this->customer)->get('/musteri/randevular');
        $response->assertOk();
        $response->assertSee('Mehmet Usta');
        $response->assertSee('Usta Elektrik Hizmetleri');

        // 2. Customer cancels appointment
        $cancelResponse = $this->actingAs($this->customer)->patch("/musteri/randevular/{$appointment->id}/iptal");
        $cancelResponse->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'id'     => $appointment->id,
            'status' => 'cancelled',
        ]);
    }
}

