<?php

use App\Http\Controllers\AppointmentRequestController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthHistoryController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PublicSiteController;
use App\Http\Controllers\SupabaseUploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicSiteController::class, 'home'])->name('home');
Route::post('/permintaan-temu-janji', [PublicSiteController::class, 'storeRequest'])
    ->middleware('throttle:3,1')->name('appointment-requests.store');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'show'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/admin', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/landing-page', [LandingPageController::class, 'edit'])->name('landing-page.edit');
    Route::put('/landing-page', [LandingPageController::class, 'update'])->name('landing-page.update');
    Route::post('/admin/media/sign', [SupabaseUploadController::class, 'sign'])->middleware('throttle:20,1')->name('media.sign');
    Route::post('/landing-page/undo/{revision}', [LandingPageController::class, 'restore'])->whereNumber('revision')->name('landing-page.restore');
    Route::post('/landing-page/undo/{revision}', [LandingPageController::class, 'restore'])->whereNumber('revision')->name('landing-page.restore');
    Route::get('/admin/permintaan', [AppointmentRequestController::class, 'appointmentRequests'])->name('appointment-requests.index');
    Route::patch('/admin/permintaan/{appointmentRequest}', [AppointmentRequestController::class, 'updateAppointmentRequest'])->name('appointment-requests.update');
    Route::post('/admin/permintaan/{appointmentRequest}/sahkan', [AppointmentRequestController::class, 'confirmAppointmentRequest'])->name('appointment-requests.confirm');
    Route::get('/health-history', [HealthHistoryController::class, 'healthHistory'])->name('health-history.index');
    Route::put('/health-history/{customer}', [HealthHistoryController::class, 'updateHealthHistory'])->name('health-history.update');
    Route::get('/customers', [CustomerController::class, 'customers'])->name('customers.index');
    Route::post('/customers', [CustomerController::class, 'storeCustomer'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'customer'])->name('customers.show');
    Route::put('/customers/{customer}', [CustomerController::class, 'updateCustomer'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'deleteCustomer'])->name('customers.destroy');
    Route::get('/bookings', [BookingController::class, 'bookings'])->name('bookings.index');
    Route::post('/bookings', [BookingController::class, 'storeBooking'])->name('bookings.store');
    Route::put('/bookings/{booking}', [BookingController::class, 'updateBooking'])->name('bookings.update');
    Route::delete('/bookings/{booking}', [BookingController::class, 'deleteBooking'])->name('bookings.destroy');
    Route::get('/packages', [PackageController::class, 'packages'])->name('packages.index');
    Route::post('/packages', [PackageController::class, 'storePackage'])->name('packages.store');
    Route::put('/packages/{treatmentPackage}', [PackageController::class, 'updatePackage'])->name('packages.update');
    Route::delete('/packages/{treatmentPackage}', [PackageController::class, 'deletePackage'])->name('packages.destroy');
    Route::get('/payments', [PaymentController::class, 'payments'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'storePayment'])->name('payments.store');
    Route::put('/payments/{payment}', [PaymentController::class, 'updatePayment'])->name('payments.update');
    Route::delete('/payments/{payment}', [PaymentController::class, 'deletePayment'])->name('payments.destroy');
    Route::get('/notes', [NoteController::class, 'notes'])->name('notes.index');
    Route::post('/notes', [NoteController::class, 'storeNote'])->name('notes.store');
    Route::put('/notes/{customerNote}', [NoteController::class, 'updateNote'])->name('notes.update');
    Route::delete('/notes/{customerNote}', [NoteController::class, 'deleteNote'])->name('notes.destroy');
});
