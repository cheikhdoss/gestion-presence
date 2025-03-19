<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'professeur']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_user_can_view_own_notifications()
    {
        $notification = Notification::create([
            'message' => 'Test notification',
            'destinataire_id' => $this->user->id,
            'date_envoi' => now(),
            'lu' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->get('/notifications');

        $response->assertStatus(200);
        $response->assertSee('Test notification');
    }

    public function test_user_cannot_view_others_notifications()
    {
        $notification = Notification::create([
            'message' => 'Test notification privée',
            'destinataire_id' => $this->admin->id,
            'date_envoi' => now(),
            'lu' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/notifications/{$notification->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_mark_notification_as_read()
    {
        $notification = Notification::create([
            'message' => 'Test notification',
            'destinataire_id' => $this->user->id,
            'date_envoi' => now(),
            'lu' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->patch("/notifications/{$notification->id}/read");

        $response->assertRedirect();
        $this->assertTrue($notification->fresh()->lu);
    }

    public function test_admin_can_send_notification()
    {
        $response = $this->actingAs($this->admin)
            ->post('/notifications', [
                'message' => 'Notification de test',
                'destinataire_id' => $this->user->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('notifications', [
            'message' => 'Notification de test',
            'destinataire_id' => $this->user->id,
        ]);
    }

    public function test_non_admin_cannot_send_notification()
    {
        $response = $this->actingAs($this->user)
            ->post('/notifications', [
                'message' => 'Notification non autorisée',
                'destinataire_id' => $this->admin->id,
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('notifications', [
            'message' => 'Notification non autorisée',
        ]);
    }

    public function test_user_can_delete_own_notification()
    {
        $notification = Notification::create([
            'message' => 'Test notification',
            'destinataire_id' => $this->user->id,
            'date_envoi' => now(),
            'lu' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->delete("/notifications/{$notification->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id,
        ]);
    }

    public function test_unread_notifications_count()
    {
        // Crée 3 notifications non lues
        Notification::create([
            'message' => 'Test 1',
            'destinataire_id' => $this->user->id,
            'date_envoi' => now(),
            'lu' => false,
        ]);

        Notification::create([
            'message' => 'Test 2',
            'destinataire_id' => $this->user->id,
            'date_envoi' => now(),
            'lu' => false,
        ]);

        Notification::create([
            'message' => 'Test 3',
            'destinataire_id' => $this->user->id,
            'date_envoi' => now(),
            'lu' => true, // Celle-ci est lue
        ]);

        $response = $this->actingAs($this->user)
            ->get('/notifications/unread-count');

        $response->assertJson(['count' => 2]);
    }
} 