<?php

use Illuminate\Support\Facades\Route;
use Modules\PublicPage\Http\Controllers\PublicBlogController;
use Modules\PublicPage\Http\Controllers\PublicPageController;
use Modules\PublicPage\Http\Controllers\EventsMediaShowcaseController;

Route::get('/', [PublicPageController::class, 'index'])->name('app.index');
Route::get('/about-us', [PublicPageController::class, 'about'])->name('app.about');
Route::get('/vision-and-values', [PublicPageController::class, 'visionAndValues'])->name('app.vision-and-values');
Route::get('/career-opportunities', [PublicPageController::class, 'careers'])->name('app.careers');
Route::get('/awards-and-recognitions', [PublicPageController::class, 'awards'])->name('app.awards');
Route::get('/gallery', [PublicPageController::class, 'gallery'])->name('app.gallery');
Route::get('/contact-us', [PublicPageController::class, 'contact'])->name('app.contact');
Route::post('/contact-us', [PublicPageController::class, 'contactUs'])->name('app.contact.store');

Route::get('/our-blog', [PublicBlogController::class, 'index'])->name('app.blog.index');
Route::get('/article/{post}', [PublicBlogController::class, 'show'])->name('app.blog.show');

Route::get('/events/media-showcase', [EventsMediaShowcaseController::class, 'index'])->name('events.media-showcase');
Route::get('/events/{event:slug}', [EventsMediaShowcaseController::class, 'show'])->name('events.show');
Route::get('/events/{event:slug}/videos', [EventsMediaShowcaseController::class, 'eventVideos'])->name('events.videos');
