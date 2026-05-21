<?php

namespace Modules\PublicPage\Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\PublicPage\Http\Requests\Admin\EventFormRequest;

class EventFormRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_returns_true(): void
    {
        $request = new EventFormRequest();

        $this->assertTrue($request->authorize());
    }

    public function test_rules_require_name(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->post('/admin/events', [
            'name' => '',
            'category' => 'Conference',
            'event_date' => '2025-01-01',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_rules_name_max_255(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->post('/admin/events', [
            'name' => str_repeat('a', 256),
            'category' => 'Conference',
            'event_date' => '2025-01-01',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_rules_require_category(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->post('/admin/events', [
            'name' => 'Test Event',
            'category' => '',
            'event_date' => '2025-01-01',
        ]);

        $response->assertSessionHasErrors(['category']);
    }

    public function test_rules_category_max_100(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->post('/admin/events', [
            'name' => 'Test Event',
            'category' => str_repeat('a', 101),
            'event_date' => '2025-01-01',
        ]);

        $response->assertSessionHasErrors(['category']);
    }

    public function test_rules_require_event_date(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->post('/admin/events', [
            'name' => 'Test Event',
            'category' => 'Conference',
            'event_date' => '',
        ]);

        $response->assertSessionHasErrors(['event_date']);
    }

    public function test_rules_event_date_must_be_valid_date(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->post('/admin/events', [
            'name' => 'Test Event',
            'category' => 'Conference',
            'event_date' => 'not-a-date',
        ]);

        $response->assertSessionHasErrors(['event_date']);
    }

    public function test_rules_allows_valid_data(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->post('/admin/events', [
            'name' => 'Test Event',
            'description' => 'Test Description',
            'icon' => '🎉',
            'category' => 'Conference',
            'event_date' => '2025-01-01',
            'is_published' => TRUE,
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_rules_allows_nullable_fields(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->post('/admin/events', [
            'name' => 'Test Event',
            'description' => NULL,
            'icon' => NULL,
            'category' => 'Conference',
            'event_date' => '2025-01-01',
            'is_published' => FALSE,
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_rules_icon_max_255(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->post('/admin/events', [
            'name' => 'Test Event',
            'category' => 'Conference',
            'event_date' => '2025-01-01',
            'icon' => str_repeat('a', 256),
        ]);

        $response->assertSessionHasErrors(['icon']);
    }

    public function test_rules_is_published_boolean(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->post('/admin/events', [
            'name' => 'Test Event',
            'category' => 'Conference',
            'event_date' => '2025-01-01',
            'is_published' => TRUE,
        ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_custom_error_messages(): void
    {
        $user = User::factory()->create([
            'is_admin' => TRUE,
        ]);

        $response = $this->actingAs($user)->post('/admin/events', [
            'name' => '',
            'category' => '',
            'event_date' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'category', 'event_date']);

        $errors = session('errors');
        $this->assertEquals('Event name is required.', $errors->first('name'));
        $this->assertEquals('Event category is required.', $errors->first('category'));
        $this->assertEquals('Event date is required.', $errors->first('event_date'));
    }
}
