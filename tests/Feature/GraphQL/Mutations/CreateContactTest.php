<?php

namespace Tests\Feature\GraphQL\Mutations;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateContactTest extends TestCase
{
    use RefreshDatabase;

    private $name;

    private $contact_no;

    public function setUp(): void
    {
        parent::setUp();

        $data = $this->user();

        $this->user = $data['user'];

        $this->token = $data['token'];

        $this->name = 'Louise';
        $this->contact_no = '1234445435';
    }

    /**
     * @test
     *
     * @return void
     */
    public function itShouldReturnUnauthenticatedForCreateContactRequest(): void
    {
        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation($name: String!, $contact_no: String!) {
                createContact(name: $name, contact_no: $contact_no) {
                    id
                    name,
                    contact_no
                }
            }
        ', [
            'name' => $this->name,
            'contact_no' => $this->contact_no
        ]);
        $response->assertJson([
            'errors' => [
                0 => [
                    'message' => 'Unauthenticated.'
                ]
            ],
        ]);
    }

    /**
     * @test
     *
     * @return void
     */
    public function itShouldCreateContact(): void
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation($name: String!, $contact_no: String!) {
                createContact(name: $name, contact_no: $contact_no) {
                    id
                    name,
                    contact_no
                }
            }
        ', [
            'name' => $this->name,
            'contact_no' => $this->contact_no
        ]);

        $createdContact = $response->json('data.createContact');
        $this->assertEquals($createdContact['name'], $this->name);
        $this->assertEquals($createdContact['contact_no'], $this->contact_no);

        $response->assertJsonStructure([
            'data' => [
                'createContact' => [
                    'id',
                    'name',
                    'contact_no'
                ],
            ],
        ]);
    }
}
