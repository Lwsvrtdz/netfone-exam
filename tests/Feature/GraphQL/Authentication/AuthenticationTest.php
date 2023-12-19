<?php

namespace Tests\Feature\GraphQL\Authentication;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     *
     * @return void
     */
    public function itShouldReturnUnauthenticatedForWrongCredentials(): void
    {
        $this->user();
        $email = 'wrongEmail@test.com';
        $password = 'wrongPassword';

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation($email: String!, $password: String!) {
                login(email: $email, password: $password, device_name: "web")
            }
        ', [
            'email' => $email,
            'password' => $password
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'errors' => [
                0 => [
                    'message' => 'Unauthenticated.',
                    'message' => 'The provided credentials are incorrect.',
                ]
            ],
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function itShouldSuccessfullyLogin(): void
    {
        $email = 'testEmail@test.com';
        $password = 'testPassword';

        User::factory()->create([
            'email' => $email,
            'password' => $password
        ]);

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation($email: String!, $password: String!) {
                login(email: $email, password: $password, device_name: "web")
            }
        ', [
            'email' => $email,
            'password' => $password
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'login',
            ],
        ]);

        $token = $response->json('data.login');
        $this->assertNotNull($token);
    }
}
