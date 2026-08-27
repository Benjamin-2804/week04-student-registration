<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature tests for the student profile view.
 *
 * Requirements: 6.1, 6.2, 6.4, 6.5, 6.6
 */
class ProfileViewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function baseStudent(array $overrides = []): array
    {
        return array_merge([
            'student_id' => 'S1234567',
            'first_name' => 'Juan',
            'middle_name' => 'Dela',
            'last_name' => 'Cruz',
            'email' => 'juan@example.com',
            'mobile_number' => '09171234567',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'program' => 'BSIT',
            'year_level' => 1,
            'address' => '123 Sample Street Barangay Sample City',
            'profile_picture' => null,
        ], $overrides);
    }

    /**
     * Valid student ID renders profile with all fields.
     */
    public function test_valid_student_id_renders_profile_with_all_fields(): void
    {
        $student = Student::create($this->baseStudent());

        $response = $this->get(route('students.show', $student));

        $response->assertStatus(200);

        // All fields should appear in the rendered HTML
        $response->assertSee($student->student_id);
        $response->assertSee($student->first_name);
        $response->assertSee($student->middle_name);
        $response->assertSee($student->last_name);
        $response->assertSee($student->email);
        $response->assertSee($student->mobile_number);
        $response->assertSee($student->program);
    }

    /**
     * Null middle name displays "N/A".
     */
    public function test_null_middle_name_displays_na(): void
    {
        $student = Student::create($this->baseStudent(['middle_name' => null]));

        $response = $this->get(route('students.show', $student));

        $response->assertStatus(200);
        $response->assertSee('N/A');
    }

    /**
     * Missing profile picture displays placeholder image.
     */
    public function test_missing_profile_picture_displays_placeholder(): void
    {
        Storage::fake('public');

        $student = Student::create($this->baseStudent(['profile_picture' => null]));

        $response = $this->get(route('students.show', $student));

        $response->assertStatus(200);
        $response->assertSee('placeholder.png', false);
    }

    /**
     * Profile picture that is stored but missing from disk displays placeholder.
     */
    public function test_inaccessible_profile_picture_displays_placeholder(): void
    {
        Storage::fake('public');

        // Store a path that doesn't actually exist on the fake disk
        $student = Student::create($this->baseStudent([
            'profile_picture' => 'profile_pictures/nonexistent.jpg',
        ]));

        $response = $this->get(route('students.show', $student));

        $response->assertStatus(200);
        $response->assertSee('placeholder.png', false);
    }

    /**
     * Non-existent student ID returns 404.
     */
    public function test_nonexistent_student_id_returns_404(): void
    {
        $response = $this->get('/students/99999');

        $response->assertStatus(404);
    }
}
