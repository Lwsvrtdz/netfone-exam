<?php

namespace Tests\Feature\GraphQL\Mutations;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateContactTest extends TestCase
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
    public function itShouldReturnUnauthenticatedForUpdateContactRequest(): void
    {
        $newNameValue = 'testName';
        $newContactNoValue = '12344444444';

        $contact = Contact::factory()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation($id: ID!, $name: String!, $newContactNoValue: String!) {
                updateContact(id: $id, name: $name, contact_no: $newContactNoValue) {
                    id,
                    name,
                    contact_no
                }
            }
        ', [
            'id' => $contact->id,
            'name' => $newNameValue,
            'newContactNoValue' => $newContactNoValue
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
    public function itShouldUpdateContact(): void
    {
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $newNameValue = 'testName';
        $newContactNoValue = '12344444444';

        $contact = Contact::factory()->create();

        $response = $this->graphQL(/** @lang GraphQL */ '
            mutation($id: ID!, $name: String!, $newContactNoValue: String!) {
                updateContact(id: $id, name: $name, contact_no: $newContactNoValue) {
                    id,
                    name,
                    contact_no
                }
            }
        ', [
            'id' => $contact->id,
            'name' => $newNameValue,
            'newContactNoValue' => $newContactNoValue
        ]);

        $updatedContact = $response->json('data.updateContact');

        $this->assertEquals($updatedContact['name'], $newNameValue);
        $this->assertEquals($updatedContact['contact_no'], $newContactNoValue);
    }
}
