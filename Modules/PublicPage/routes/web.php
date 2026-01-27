<?php

use Illuminate\Support\Facades\Route;
use Modules\PublicPage\Http\Controllers\PublicBlogController;
use Modules\PublicPage\Http\Controllers\PublicPageController;
use Modules\PublicPage\Http\Controllers\Admin\AdminEventController;
use Modules\PublicPage\Http\Controllers\Admin\AdminVideoController;
use Modules\PublicPage\Http\Controllers\EventsMediaShowcaseController;
use Modules\PublicPage\Http\Controllers\Admin\AdminDashboardController;

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

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::prefix('events')->name('events.')->group(function (): void {
        Route::get('/', [AdminEventController::class, 'index'])->name('index');
        Route::get('/create', [AdminEventController::class, 'create'])->name('create');
        Route::post('/', [AdminEventController::class, 'store'])->name('store');
        Route::post('/bulk/publish', [AdminEventController::class, 'bulkPublish'])->name('bulkPublish');
        Route::post('/bulk/unpublish', [AdminEventController::class, 'bulkUnpublish'])->name('bulkUnpublish');
        Route::delete('/bulk', [AdminEventController::class, 'bulkDelete'])->name('bulkDelete');
        Route::get('/{event}', [AdminEventController::class, 'show'])->name('show');
        Route::get('/{event}/edit', [AdminEventController::class, 'edit'])->name('edit');
        Route::put('/{event}', [AdminEventController::class, 'update'])->name('update');
        Route::delete('/{event}', [AdminEventController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('videos')->name('videos.')->group(function (): void {
        Route::get('/upload/{event}', [AdminVideoController::class, 'create'])->name('create');
        Route::post('/upload/{event}', [AdminVideoController::class, 'upload'])->name('upload');
        Route::post('/events/{event}', [AdminVideoController::class, 'store'])->name('store');
        Route::post('/events/{event}/reorder', [AdminVideoController::class, 'reorder'])->name('reorder');
        Route::get('/{video}/edit', [AdminVideoController::class, 'edit'])->name('edit');
        Route::put('/{video}', [AdminVideoController::class, 'update'])->name('update');
        Route::delete('/{video}', [AdminVideoController::class, 'destroy'])->name('destroy');
    });
});
