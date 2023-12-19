<?php

namespace Tests\Feature\GraphQL\Mutations;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteContactTest extends TestCase
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
    public function itShouldReturnUnauthenticatedForDeleteContactRequest(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation($id: ID!) {
                deleteContact(id: $id) {
                    id,
                    name,
                    contact_no
                }
            }
        ', [
            'id' => $contact->id,
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
    public function itShouldDeleteContact(): void
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $contact = Contact::factory()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation($id: ID!) {
                deleteContact(id: $id) {
                    id,
                    name,
                    contact_no
                }
            }
        ', [
            'id' => $contact->id,
        ]);

        $deletedContact = $response->json('data.deleteContact');

        $newContact = Contact::find($contact->id);
        $this->assertNull($newContact);

        $this->assertEquals($deletedContact['name'], $contact->name);
        $this->assertEquals($deletedContact['contact_no'], $contact->contact_no);
    }
}
