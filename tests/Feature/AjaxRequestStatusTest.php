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

class AjaxRequestStatusTest extends TestCase
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
            'name'    => 'Ali Can Müşteri',
            'role_id' => Role::CUSTOMER,
        ]);

        $this->providerUser = User::factory()->create([
            'name'    => 'Mustafa Usta',
            'role_id' => Role::PROVIDER,
        ]);

        $this->category = ServiceCategory::create([
            'name'        => 'Su Tesisatı',
            'description' => 'Sıhhi tesisat ve arıza',
            'is_active'   => true,
        ]);

        $this->serviceProvider = ServiceProvider::create([
            'user_id'       => $this->providerUser->id,
            'category_id'   => $this->category->id,
            'company_name'  => 'Mustafa Tesisat Ltd.',
            'bio'           => 'Tesisat uzmanı.',
            'working_hours' => '08:00 - 18:00',
        ]);
    }

    public function test_provider_can_accept_request_via_ajax(): void
    {
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->serviceProvider->id,
            'category_id' => $this->category->id,
            'description' => 'Mutfak bataryası su kaçırıyor.',
            'status'      => 'pending',
        ]);

        $response = $this->actingAs($this->providerUser)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->postJson("/provider/requests/{$serviceRequest->id}/accept", [
                'appointment_date' => now()->addDays(2)->format('Y-m-d H:i:s'),
                'note'             => 'Yedek conta ile gelinecek.',
            ]);

        $response->assertOk();
        $response->assertJson([
            'success'    => true,
            'status'     => 'accepted',
            'request_id' => $serviceRequest->id,
        ]);

        $this->assertDatabaseHas('service_requests', [
            'id'     => $serviceRequest->id,
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('appointments', [
            'service_request_id' => $serviceRequest->id,
            'status'             => 'scheduled',
        ]);
    }

    public function test_provider_can_reject_request_via_ajax(): void
    {
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->serviceProvider->id,
            'category_id' => $this->category->id,
            'description' => 'Kanal açma talebi.',
            'status'      => 'pending',
        ]);

        $response = $this->actingAs($this->providerUser)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->postJson("/provider/requests/{$serviceRequest->id}/reject");

        $response->assertOk();
        $response->assertJson([
            'success'    => true,
            'status'     => 'rejected',
            'request_id' => $serviceRequest->id,
        ]);

        $this->assertDatabaseHas('service_requests', [
            'id'     => $serviceRequest->id,
            'status' => 'rejected',
        ]);
    }

    public function test_provider_can_complete_appointment_via_ajax(): void
    {
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->serviceProvider->id,
            'category_id' => $this->category->id,
            'description' => 'Klozet tamiri.',
            'status'      => 'accepted',
        ]);

        $appointment = Appointment::create([
            'service_request_id' => $serviceRequest->id,
            'customer_id'        => $this->customer->id,
            'provider_id'        => $this->serviceProvider->id,
            'appointment_date'   => now()->addDay(),
            'status'             => 'scheduled',
        ]);

        $response = $this->actingAs($this->providerUser)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->patchJson("/usta/randevular/{$appointment->id}/tamamla");

        $response->assertOk();
        $response->assertJson([
            'success'        => true,
            'status'         => 'completed',
            'appointment_id' => $appointment->id,
        ]);

        $this->assertDatabaseHas('appointments', [
            'id'     => $appointment->id,
            'status' => 'completed',
        ]);
    }

    public function test_customer_can_cancel_appointment_via_ajax(): void
    {
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $this->customer->id,
            'provider_id' => $this->serviceProvider->id,
            'category_id' => $this->category->id,
            'description' => 'Lavabo montajı.',
            'status'      => 'accepted',
        ]);

        $appointment = Appointment::create([
            'service_request_id' => $serviceRequest->id,
            'customer_id'        => $this->customer->id,
            'provider_id'        => $this->serviceProvider->id,
            'appointment_date'   => now()->addDays(3),
            'status'             => 'scheduled',
        ]);

        $response = $this->actingAs($this->customer)
            ->withHeaders(['Accept' => 'application/json', 'X-Requested-With' => 'XMLHttpRequest'])
            ->patchJson("/musteri/randevular/{$appointment->id}/iptal");

        $response->assertOk();
        $response->assertJson([
            'success'        => true,
            'status'         => 'cancelled',
            'appointment_id' => $appointment->id,
        ]);

        $this->assertDatabaseHas('appointments', [
            'id'     => $appointment->id,
            'status' => 'cancelled',
        ]);
    }
}

