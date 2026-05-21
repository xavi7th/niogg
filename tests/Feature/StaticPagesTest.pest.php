<?php

beforeEach(function (): void {
    config(['inertia.testing.ensure_pages_exist' => FALSE]);
});

test('vision and values page displays', function (): void {
    $response = $this->get(route('app.vision-and-values'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('PublicPage::VisionAndValues'));
});

test('career opportunities page displays', function (): void {
    $response = $this->get(route('app.careers'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('PublicPage::Careers'));
});

test('awards and recognitions page displays', function (): void {
    $response = $this->get(route('app.awards'));

    $response->assertStatus(200)
        ->assertInertia(fn ($page) => $page->component('PublicPage::Awards'));
});
