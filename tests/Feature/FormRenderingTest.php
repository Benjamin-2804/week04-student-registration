<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for the student registration form rendering.
 *
 * Requirements: 1.3, 1.6
 */
class FormRenderingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * GET /students/create renders a form containing all required field names.
     */
    public function test_create_form_contains_all_required_fields(): void
    {
        $response = $this->get('/students/create');

        $response->assertStatus(200);

        $requiredFields = [
            'student_id',
            'first_name',
            'middle_name',
            'last_name',
            'email',
            'mobile_number',
            'date_of_birth',
            'gender',
            'program',
            'year_level',
            'address',
            'profile_picture',
        ];

        foreach ($requiredFields as $field) {
            $response->assertSee('name="'.$field.'"', false);
        }
    }

    /**
     * Form contains a submit button labeled "Register Student".
     */
    public function test_create_form_contains_register_student_submit_button(): void
    {
        $response = $this->get('/students/create');

        $response->assertStatus(200);
        $response->assertSee('Register Student');
    }
}
