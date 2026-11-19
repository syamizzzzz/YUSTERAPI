<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\CustomerNote;
use App\Models\Payment;
use App\Models\TreatmentPackage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagementFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_create_and_view_the_complete_customer_booking_flow(): void
    {
        $staff = User::factory()->create();
        $this->actingAs($staff);

        $this->post('/customers', [
            'name' => 'Pelanggan Contoh', 'phone' => '0123456789', 'address' => 'Kuala Lumpur',
            'age' => 34, 'gender' => 'Perempuan', 'health_history' => 'Alergi lateks',
        ])->assertRedirect('/customers/1');
        $customer = Customer::firstOrFail();
        $this->assertSame('YT00001', $customer->code);
        $this->get('/customers/1')->assertOk()->assertSee('Alergi lateks')->assertSee('name="phone"', false)->assertDontSee('@include');

        $this->post('/packages', [
            'name' => 'Pakej Contoh',
            'price' => 120,
            'duration_minutes' => 60,
            'is_active' => 1,
            'name_color' => '#173F39',
            'description_color' => '#788D80',
            'duration_color' => '#89A093',
            'price_color' => '#17685F',
            'package_label_color' => '#17685F',
        ])->assertSessionHasNoErrors();
        $package = TreatmentPackage::firstOrFail();
        $this->post('/bookings', ['customer_id' => $customer->id, 'treatment_package_id' => $package->id, 'starts_at' => '2027-01-15 10:00', 'status' => 'confirmed'])->assertSessionHasNoErrors();
        $booking = Booking::firstOrFail();
        $this->post('/payments', ['booking_id' => $booking->id, 'amount' => 120, 'method' => 'Tunai', 'status' => 'paid', 'paid_at' => '2027-01-15'])->assertSessionHasNoErrors();
        $this->post('/notes', ['customer_id' => $customer->id, 'booking_id' => $booking->id, 'body' => 'Susulan dalam 2 minggu'])->assertSessionHasNoErrors();

        $this->assertSame(1, Payment::count());
        $this->assertSame(1, CustomerNote::count());
        $this->get('/admin?month=2027-01')->assertOk()->assertSee('RM 120.00')->assertSee('Pelanggan Contoh');
        $this->get('/bookings?month=2027-01')->assertOk()->assertSee('Pakej Contoh')->assertDontSee('@include');
        $this->get('/packages')->assertOk()->assertDontSee('@include');
        $this->get('/payments')->assertOk()->assertSee('RM 120.00')->assertDontSee('@include');
        $this->get('/notes')->assertOk()->assertSee('Susulan dalam 2 minggu')->assertDontSee('@include');
    }

    public function test_guest_is_redirected_and_note_cannot_be_attached_to_another_customers_booking(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/customers')->assertRedirect('/login');

        $this->actingAs(User::factory()->create());
        $first = Customer::create(['name' => 'A', 'phone' => '111']);
        $second = Customer::create(['name' => 'B', 'phone' => '222']);
        $booking = Booking::create(['customer_id' => $first->id, 'starts_at' => '2027-01-10 10:00', 'status' => 'confirmed']);
        $this->post('/notes', ['customer_id' => $second->id, 'booking_id' => $booking->id, 'body' => 'Incorrect link'])
            ->assertSessionHasErrors('booking_id');
        $this->assertDatabaseCount('customer_notes', 0);
    }

    public function test_staff_forms_render_input_fields_instead_of_blade_directives(): void
    {
        $this->actingAs(User::factory()->create());

        foreach ([
            '/customers' => 'name="phone"',
            '/bookings' => 'name="starts_at"',
            '/packages' => 'name="price"',
            '/payments' => 'name="amount"',
            '/notes' => 'name="body"',
        ] as $url => $field) {
            $this->get($url)->assertOk()->assertSee($field, false)->assertDontSee('@include');
        }
    }

    public function test_landing_page_section_design_controls_are_associated_with_the_save_form(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/landing-page')
            ->assertOk()
            ->assertSee('id="landing-page-form"', false)
            ->assertSee('form="landing-page-form"', false)
            ->assertSee('name="section_design[header][background_color]"', false)
            ->assertSee('name="section_design[header][overlay_color]"', false)
            ->assertSee('name="section_design[header][overlay_opacity]"', false)
            ->assertSee('form="landing-page-form" name="section_image_paths[header]"', false);
    }
}
