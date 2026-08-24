<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/login',[AuthController::class,'create'])->name('login');
Route::post('/login',[AuthController::class,'store'])->name('login.store');

Route::get('/d/{token}',[DocumentController::class,'publicShow'])->name('public.documents.show');
Route::get('/d/{token}/pdf',[DocumentController::class,'publicPdf'])->name('public.documents.pdf');

Route::middleware('auth')->group(function () {
    Route::get('/',DashboardController::class)->name('dashboard');
    Route::post('/logout',[AuthController::class,'destroy'])->name('logout');

    Route::resource('customers',CustomerController::class)->except('destroy');
    Route::post('/customers/{customer}/archive',[CustomerController::class,'archive'])->name('customers.archive');
    Route::post('/customers/{customer}/restore',[CustomerController::class,'restore'])->name('customers.restore');
    Route::delete('/customers/{customer}',[CustomerController::class,'destroy'])->name('customers.destroy');

    Route::get('/service-types',[ServiceTypeController::class,'index'])->name('service-types.index');
    Route::post('/service-types',[ServiceTypeController::class,'store'])->name('service-types.store');
    Route::put('/service-types/{serviceType}',[ServiceTypeController::class,'update'])->name('service-types.update');
    Route::delete('/service-types/{serviceType}',[ServiceTypeController::class,'destroy'])->name('service-types.destroy');

    Route::resource('transactions',TransactionController::class)->except('destroy');
    Route::delete('/transactions/{transaction}',[TransactionController::class,'destroy'])->name('transactions.destroy');
    Route::post('/transactions/{transaction}/cancel',[TransactionController::class,'cancel'])->name('transactions.cancel');

    Route::post('/transactions/{transaction}/payments',[PaymentController::class,'store'])->name('payments.store');
    Route::get('/transactions/{transaction}/payments/{payment}/edit',[PaymentController::class,'edit'])->name('payments.edit');
    Route::put('/transactions/{transaction}/payments/{payment}',[PaymentController::class,'update'])->name('payments.update');
    Route::delete('/transactions/{transaction}/payments/{payment}',[PaymentController::class,'destroy'])->name('payments.destroy');

    Route::get('/documents',[DocumentController::class,'index'])->name('documents.index');
    Route::post('/transactions/{transaction}/documents/{type}',[DocumentController::class,'generate'])->name('documents.generate');
    Route::get('/documents/{document}',[DocumentController::class,'show'])->name('documents.show');
    Route::get('/documents/{document}/pdf',[DocumentController::class,'pdf'])->name('documents.pdf');
    Route::post('/documents/{document}/snapshot',[DocumentController::class,'regenerateSnapshot'])->name('documents.snapshot');
    Route::post('/documents/{document}/token',[DocumentController::class,'regenerateToken'])->name('documents.token');
    Route::post('/documents/{document}/revoke',[DocumentController::class,'revoke'])->name('documents.revoke');
    Route::get('/documents/{document}/whatsapp',[DocumentController::class,'whatsapp'])->name('documents.whatsapp');

    Route::get('/settings',[SettingsController::class,'edit'])->name('settings.edit');
    Route::put('/settings',[SettingsController::class,'update'])->name('settings.update');
});
