<?php

uses(Modules\Conference\Tests\TestCase::class);

test('conference page displays', function (): void {
    $response = $this->get(route('app.conferences.launch.index'));

    $response->assertStatus(200);
});
