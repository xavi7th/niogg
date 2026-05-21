<?php

namespace Modules\PublicPage\Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Modules\PublicPage\Models\Event;
use Modules\PublicPage\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user for testing
        User::factory()->create([
            'is_admin' => TRUE,
        ]);
    }

    public function test_dashboard_requires_authentication(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_dashboard_requires_admin_role(): void
    {
        $user = User::factory()->create(['is_admin' => FALSE]);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_dashboard_accessible_by_admin(): void
    {
        $admin = User::where('is_admin', TRUE)->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_accessible_by_super_admin(): void
    {
        $superAdmin = User::factory()->create([
            'is_admin' => TRUE,
            'is_super_admin' => TRUE,
        ]);

        $response = $this->actingAs($superAdmin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_dashboard_shows_correct_stats(): void
    {
        Event::factory()->published()->count(3)->create();
        Event::factory()->draft()->count(2)->create();

        $events = Event::all();
        Video::factory()->for($events->first())->count(5)->create();

        $admin = User::where('is_admin', TRUE)->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->hasAll(['stats', 'recentEvents'])
                ->where('stats.total_events', 5)
                ->where('stats.published_events', 3)
                ->where('stats.draft_events', 2)
                ->where('stats.total_videos', 5)
        );
    }

    public function test_dashboard_shows_recent_events(): void
    {
        Event::factory()->count(10)->create();

        $admin = User::where('is_admin', TRUE)->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(
            fn ($page) => $page
                ->has('recentEvents', 5)
                ->has('recentEvents.0')
        );
    }

    public function test_dashboard_recent_events_ordered_by_created_at(): void
    {
        $events = Event::factory()->count(5)->create();

        $admin = User::where('is_admin', TRUE)->first();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);

        $page = $response->viewData('page');
        $recentIds = collect($page['props']['recentEvents'])->pluck('id')->toArray();
        $expectedIds = $events->sortByDesc('created_at')->take(5)->pluck('id')->toArray();

        $this->assertEquals($expectedIds, $recentIds);
    }
}
