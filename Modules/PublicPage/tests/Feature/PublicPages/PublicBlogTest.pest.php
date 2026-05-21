<?php

uses(Modules\PublicPage\Tests\TestCase::class);

test('blog index page displays', function (): void {
    $response = $this->get(route('app.blog.index'));

    $response->assertStatus(200);
});

test('blog show page displays', function (): void {
    $response = $this->get(route('app.blog.show', ['post' => 'sample-post']));

    $response->assertStatus(200);
});
