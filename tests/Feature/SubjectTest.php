<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubjectTest extends TestCase
{
    use RefreshDatabase;
     /**
     * Test GET request to fetch all users.
     *
     * @return void
     */
   public function test_get_all_users()
   {
    $response = $this->get('/api/subjects');
    $response->assertStatus(200);
   }
   public function test_store_method_with_valid_data()
   {
       $data = [
           'subject_code' => 'ENG101',
           'name' => 'English Literature',
           'faculty' => 'Arts',
       ];

       $response = $this->post('/api/subjects', $data);

       $response->assertStatus(Response::HTTP_CREATED)
                ->assertJson(['message' => 'Subject created']);

       $this->assertDatabaseHas('subjects', [
           'subject_code' => 'ENG101',
           'name' => 'English Literature',
           'faculty' => 'Arts',
       ]);
   }

   /**
    * Test store method with invalid data.
    *
    * @return void
    */
   public function test_store_method_with_invalid_data()
   {
       $data = [
           // Missing 'subject_code' field intentionally
           'name' => 'English Literature',
           'faculty' => 'Arts',
       ];

       $response = $this->postJson('/api/subjects', $data);

       $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
                ->assertJsonValidationErrors(['subject_code']);
   }
}
