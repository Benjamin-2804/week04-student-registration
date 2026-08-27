<?php

namespace Tests\Feature;

use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Property-based tests for uniqueness constraints.
 *
 * Feature: student-registration-system, Property 3: Uniqueness rejection on duplicate student_id or email
 *
 * Uses DatabaseMigrations (not RefreshDatabase) so we can safely truncate between iterations
 * without triggering nested transaction errors on SQLite.
 *
 * Runs a minimum of 100 iterations.
 */
class UniquenessPropertyTest extends TestCase
{
    use DatabaseMigrations;

    private const ITERATIONS = 100;

    /**
     * Run validation with uniqueness rules active.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, array<string>>
     */
    private function validate(array $data): array
    {
        $rules = (new StoreStudentRequest)->rules();
        unset($rules['profile_picture']);

        $validator = Validator::make($data, $rules);

        return $validator->errors()->toArray();
    }

    /**
     * Build a base student record array.
     *
     * @return array<string, mixed>
     */
    private function studentData(string $studentId, string $email): array
    {
        return [
            'student_id' => $studentId,
            'first_name' => 'Juan',
            'middle_name' => null,
            'last_name' => 'Cruz',
            'email' => $email,
            'mobile_number' => '09171234567',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'program' => 'BSIT',
            'year_level' => 1,
            'address' => '123 Sample Street Barangay',
            'profile_picture' => null,
        ];
    }

    /**
     * Property 3a: Duplicate student_id is rejected.
     */
    public function test_property_3_duplicate_student_id_rejected(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Clean slate each iteration
            Student::truncate();

            $studentId = 'S'.str_pad((string) ($i + 1), 7, '0', STR_PAD_LEFT);
            $firstEmail = "first{$i}@example.com";
            $secondEmail = "second{$i}@example.com";

            // Persist first record
            Student::create($this->studentData($studentId, $firstEmail));

            // Second submission: same student_id, different email
            $errors = $this->validate($this->studentData($studentId, $secondEmail));

            $this->assertArrayHasKey('student_id', $errors,
                "Iteration {$i}: Expected student_id uniqueness error for '{$studentId}'."
            );

            $this->assertSame(1, Student::count(),
                "Iteration {$i}: Only one record should exist after duplicate student_id rejection."
            );
        }
    }

    /**
     * Property 3b: Duplicate email is rejected.
     */
    public function test_property_3_duplicate_email_rejected(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Clean slate each iteration
            Student::truncate();

            $firstId = 'S'.str_pad((string) ($i + 1), 7, '0', STR_PAD_LEFT);
            $secondId = 'S'.str_pad((string) ($i + 10001), 7, '0', STR_PAD_LEFT);
            $email = "shared{$i}@example.com";

            // Persist first record
            Student::create($this->studentData($firstId, $email));

            // Second submission: same email, different student_id
            $errors = $this->validate($this->studentData($secondId, $email));

            $this->assertArrayHasKey('email', $errors,
                "Iteration {$i}: Expected email uniqueness error for '{$email}'."
            );

            $this->assertSame(1, Student::count(),
                "Iteration {$i}: Only one record should exist after duplicate email rejection."
            );
        }
    }
}
