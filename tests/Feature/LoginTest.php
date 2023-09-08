<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
    }


    public function testSuccessfulLogin()
    {

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
        ]);
    }

    public function testLoginWhenValidationNotPassed()
    {

        $response = $this->postJson('/api/login', [
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email', 'password'
        ]);
        $response->assertSee(['email' => 'The email must be a valid email address.', 'password' => 'The password field is required.']);
    }

    public function testUnSuccessfulLogin()
    {

        $response = $this->postJson('/api/login', [
            'email' => 'test1@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401);
        $response->assertSee([
            'error',
        ]);
    }
}
