<?php

namespace Tests\Feature;

use App\Models\AppointmentRequest;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\SiteSetting;
use App\Models\TreatmentPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLandingTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_is_public_and_shows_only_active_packages(): void
    {
        TreatmentPackage::create(['name' => 'Pakej Aktif', 'price' => 88, 'is_active' => true]);
        TreatmentPackage::create(['name' => 'Pakej Dalaman', 'price' => 100, 'is_active' => false]);

        $this->get('/')->assertOk()->assertSee('Pakej Aktif')->assertDontSee('Pakej Dalaman')->assertSee('Buat Temu Janji')->assertDontSee('class="hero-visual"', false);
        $this->get('/admin/permintaan')->assertRedirect('/login');
    }

    public function test_package_image_is_displayed_above_package_details(): void
    {
        TreatmentPackage::create([
            'name' => 'Pakej Dengan Gambar',
            'price' => 88,
            'is_active' => true,
            'background_image' => 'packages/example.jpg',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder([
                '<img class="public-package-cover"',
                '<div class="public-package-details">',
                'PAKEJ 01',
                'Pakej Dengan Gambar',
            ], false);
    }

    public function test_hero_image_is_displayed_beside_the_hero_copy(): void
    {
        SiteSetting::create([
            'key' => 'landing_page',
            'value' => ['section_images' => ['hero' => 'landing/sections/hero/example.jpg']],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder([
                'class="hero-copy"',
                '<div class="hero-visual">',
                'class="hero-image"',
                'alt="Gambar muka depan YUSTERAPI"',
            ], false);
    }

    public function test_landing_assets_use_forwarded_https_scheme(): void
    {
        $this->withServerVariables([
            'REMOTE_ADDR' => '127.0.0.1',
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ])->get('/')
            ->assertOk()
            ->assertSee('href="https://localhost:8000/css/landing.css?v=2026112002"', false);
    }

    public function test_visitor_can_send_request_and_staff_can_review_it(): void
    {
        $this->post('/permintaan-temu-janji', [
            'name' => 'Tetamu Contoh', 'phone' => '0123456789',
            'preferred_at' => now()->addDays(7)->format('Y-m-d H:i'),
            'message' => 'Sesi pada waktu pagi',
        ])->assertRedirect('http://localhost:8000#tempah');

        $appointmentRequest = AppointmentRequest::firstOrFail();
        $this->assertSame('new', $appointmentRequest->status);
        $this->actingAs(User::factory()->create());
        $this->get('/admin/permintaan')->assertOk()->assertSee('Tetamu Contoh');
        $this->patch('/admin/permintaan/'.$appointmentRequest->id, ['status' => 'contacted'])->assertSessionHasNoErrors();
        $this->assertSame('contacted', $appointmentRequest->fresh()->status);
    }

    public function test_appointment_form_accepts_json_and_reports_validation_errors(): void
    {
        $this->postJson('/permintaan-temu-janji', [
            'name' => 'Tetamu JSON',
            'phone' => '0123456789',
        ])->assertCreated()->assertJsonPath('message', 'Permintaan diterima. Pasukan YUSTERAPI akan menghubungi anda untuk susulan.');

        $this->assertDatabaseHas('appointment_requests', ['name' => 'Tetamu JSON']);

        $this->postJson('/permintaan-temu-janji', [
            'name' => '',
            'phone' => '',
        ])->assertUnprocessable()->assertJsonValidationErrors(['name', 'phone']);

        $inactivePackage = TreatmentPackage::create(['name' => 'Tidak Aktif', 'price' => 80, 'is_active' => false]);
        $this->postJson('/permintaan-temu-janji', [
            'name' => 'Tetamu JSON',
            'phone' => '0123456789',
            'treatment_package_id' => $inactivePackage->id,
        ])->assertUnprocessable()->assertJsonValidationErrors('treatment_package_id');

        $this->assertDatabaseCount('appointment_requests', 1);
    }

    public function test_staff_can_confirm_public_request_and_complete_customer_record(): void
    {
        $package = TreatmentPackage::create(['name' => 'Rawatan Yusterapi', 'price' => 90, 'is_active' => true]);
        $this->post('/permintaan-temu-janji', [
            'name' => 'Siti Aminah', 'phone' => '01112345678',
            'preferred_at' => '2027-01-15 10:00', 'treatment_package_id' => $package->id,
        ])->assertSessionHasNoErrors();
        $appointmentRequest = AppointmentRequest::firstOrFail();
        $this->assertSame($package->id, $appointmentRequest->treatment_package_id);

        $this->actingAs(User::factory()->create());
        $this->post(route('appointment-requests.confirm', $appointmentRequest), [
            'starts_at' => '2027-01-15 11:00', 'treatment_package_id' => $package->id,
        ])->assertRedirect('/customers/1');
        $customer = Customer::firstOrFail();
        $booking = Booking::firstOrFail();
        $this->assertSame('YT00001', $customer->code);
        $this->assertSame($customer->id, $booking->customer_id);
        $this->assertSame($booking->id, $appointmentRequest->fresh()->booking_id);

        $this->post(route('appointment-requests.confirm', $appointmentRequest), [
            'starts_at' => '2027-01-15 11:00', 'treatment_package_id' => $package->id,
        ])->assertSessionHasErrors('booking');
        $this->assertDatabaseCount('bookings', 1);

        $this->put(route('health-history.update', $customer), ['health_history' => 'Alahan minyak tertentu'])
            ->assertSessionHasNoErrors();
        $this->post('/payments', [
            'booking_id' => $booking->id, 'amount' => 90, 'method' => 'Tunai',
            'status' => 'paid', 'paid_at' => '2027-01-15',
        ])->assertSessionHasNoErrors();
        $this->post('/notes', [
            'customer_id' => $customer->id, 'booking_id' => $booking->id, 'body' => 'Susulan minggu depan',
        ])->assertSessionHasNoErrors();
        $this->get('/health-history')->assertOk()->assertSee('Alahan minyak tertentu');
        $this->get('/customers/'.$customer->id)->assertOk()->assertSee('Susulan minggu depan');
        $this->get('/admin?month=2027-01')->assertOk()->assertSee('RM 90.00');
        $this->assertSame(1, Payment::count());
    }
}
