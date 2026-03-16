<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactListController;
use App\Http\Controllers\CampaignController;
use Illuminate\Support\Facades\Route;

Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
Route::post('contacts', [ContactController::class, 'store'])->name('contacts.store');
Route::post('contacts/{id}/unsubscribe', [ContactController::class, 'unsubscribe'])->name('contacts.unsubscribe');

Route::get('/contact-lists', [ContactListController::class, 'index'])->name('contactsList.index');
Route::post('/contact-lists', [ContactListController::class, 'store'])->name('contactsList.store');
Route::post('/contact-lists/{id}/contacts', [ContactListController::class, 'addContact'])->name('contactsList.addContact');


Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaign.index');
Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaign.store');
Route::get('/campaigns/{id}', [CampaignController::class, 'show'])->name('campaign.show');
Route::post('/campaigns/{id}/dispatch', [CampaignController::class, 'dispatch'])->name('campaign.dispatch');