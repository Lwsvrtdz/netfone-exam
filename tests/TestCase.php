<?php

namespace Tests;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    use MakesGraphQLRequests;

    protected $user;

    protected $token;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function user(Authenticatable $user = null): array
    {
        if (true === is_null($user)) {
            $user = User::factory([
                'email' => 'test@test.com',
                'password' => Hash::make('password')
            ])->create();
        }
        $token = $user->createToken('web')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }
}
