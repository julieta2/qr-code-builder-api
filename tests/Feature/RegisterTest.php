<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user registration via the API with valid data.
     *
     * @return void
     */
    public function testUserRegistrationWithValidData()
    {
        $data = [
            'name' => 'test name',
            'email' => 'test@gmail.com',
            'password' => '123123123',
        ];

        $response = $this->json('POST', '/api/register', $data);
        $response->assertStatus(201);
        $response->assertSee([
            'message' => 'Successfully registered',
        ]);
    }

    /**
     * Test user registration via the API with invalid data.
     *
     * @return void
     */
    public function testUserRegistrationWithInvalidData()
    {
        $data = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
        ];

        $response = $this->json('POST', '/api/register', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'password']);
    }
}

