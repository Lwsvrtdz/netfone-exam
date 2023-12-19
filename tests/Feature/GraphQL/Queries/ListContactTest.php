<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListContactTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $data = $this->user();

        $this->user = $data['user'];

        $this->token = $data['token'];
    }

    /**
     * @test
     *
     * @return void
     */
    public function itShouldReturnUnauthenticatedAllContactsRequest(): void
    {
        Contact::factory()->count(5)->create();

        $response = $this->graphQL('
            {
                contacts(first:5){
                    data{
                        id,
                        name,
                        contact_no
                    }
                }
            }
        ');
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
    public function itShouldListAllContacts(): void
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $contacts = Contact::factory()->count(5)->create();

        $response = $this->graphQL('
            {
                contacts(first:5){
                    data{
                        id,
                        name,
                        contact_no
                    }
                }
            }
        ');
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'contacts' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'contact_no',
                        ],
                    ],
                ],
            ],
        ]);

        $response->assertJson([
            'data' => [
                'contacts' => [
                    'data' => $contacts->map(function ($contact) {
                        return [
                            'id' => $contact->id,
                            'name' => $contact->name,
                            'contact_no' => $contact->contact_no,
                        ];
                    })->all(),
                ],
            ],
        ]);
    }
}
