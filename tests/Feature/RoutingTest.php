<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for routing.
 *
 * Requirements: 1.1, 6.4, 7.5
 */
class RoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * GET /students/create returns HTTP 200.
     */
    public function test_create_route_returns_200(): void
    {
        $response = $this->get('/students/create');

        $response->assertStatus(200);
    }

    /**
     * GET /students/{valid-id} returns HTTP 200 with existing student.
     */
    public function test_show_route_with_valid_student_returns_200(): void
    {
        $student = Student::create([
            'student_id' => 'S1000001',
            'first_name' => 'Juan',
            'last_name' => 'Cruz',
            'email' => 'juan@example.com',
            'mobile_number' => '09171234567',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'program' => 'BSIT',
            'year_level' => 1,
            'address' => '123 Sample Street Barangay Sample',
        ]);

        $response = $this->get("/students/{$student->id}");

        $response->assertStatus(200);
    }

    /**
     * GET /students/{nonexistent-id} returns HTTP 404.
     */
    public function test_show_route_with_nonexistent_id_returns_404(): void
    {
        $response = $this->get('/students/99999');

        $response->assertStatus(404);
    }

    /**
     * Undefined route returns HTTP 404.
     */
    public function test_undefined_route_returns_404(): void
    {
        $response = $this->get('/undefined-route-xyz');

        $response->assertStatus(404);
    }
}
