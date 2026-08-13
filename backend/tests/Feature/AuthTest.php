<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Ash Ketchum',
            'email' => 'ash@pallettown.com',
            'password' => 'pikachu123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'ash@pallettown.com',
            'name' => 'Ash Ketchum',
        ]);
    }

    public function test_user_cannot_register_with_existing_email()
    {
        User::factory()->create([
            'email' => 'ash@pallettown.com',
        ]);

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Ash Ketchum 2',
            'email' => 'ash@pallettown.com',
            'password' => 'pikachu1234',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'email' => 'ash@pallettown.com',
            'password' => bcrypt('pikachu123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'ash@pallettown.com',
            'password' => 'pikachu123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'user' => [
                    'id',
                    'name',
                    'email',
                ]
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'ash@pallettown.com',
            'password' => bcrypt('pikachu123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'ash@pallettown.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Sesión cerrada exitosamente.'
            ]);
    }
}
