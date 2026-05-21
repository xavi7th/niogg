<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

uses(
    Tests\TestCase::class,
)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Traits
|--------------------------------------------------------------------------
*/

uses(Illuminate\Foundation\Testing\RefreshDatabase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Fakes
|--------------------------------------------------------------------------
*/

function fakeQueue(): void
{
    Illuminate\Support\Facades\Queue::fake();
}

function fakeMail(): void
{
    Illuminate\Support\Facades\Mail::fake();
}

function fakeNotification(): void
{
    Illuminate\Support\Facades\Notification::fake();
}

function fakeStorage(array $disks = ['public']): void
{
    foreach ($disks as $disk) {
        Illuminate\Support\Facades\Storage::fake($disk);
    }
}

function fakeBus(): void
{
    Illuminate\Support\Facades\Bus::fake();
}
