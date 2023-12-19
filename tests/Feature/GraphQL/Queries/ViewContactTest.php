<?php

namespace Tests\Feature\GraphQL\Queries;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewContactTest extends TestCase
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
    public function itShouldReturnUnauthenticatedViewContactRequest(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->graphQL('
            {
                contact(id: "' . $contact->id . '"){
                    id,
                    name,
                    contact_no
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
    public function itShouldReturnViewContactRequest(): void
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $contact = Contact::factory()->create();

        $response = $this->graphQL('
            {
                contact(id: "' . $contact->id . '"){
                    id,
                    name,
                    contact_no
                }
            }
        ');

        $data = $response->json('data.contact');

        $this->assertEquals($data['id'], $contact->id);
        $this->assertEquals($data['name'], $contact->name);
        $this->assertEquals($data['contact_no'], $contact->contact_no);

         $response->assertJsonStructure(
             [
                'data' => [
                    'contact' => [
                        'id',
                        'name',
                        'contact_no',
                    ],
                ],
             ]
         );
    }
}
