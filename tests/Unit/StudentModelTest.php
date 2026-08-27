<?php

namespace Tests\Unit;

use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentModelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that all fillable fields can be mass-assigned.
     */
    public function test_all_fillable_fields_are_mass_assignable(): void
    {
        $expected = [
            'student_id', 'first_name', 'middle_name', 'last_name',
            'email', 'mobile_number', 'gender', 'date_of_birth',
            'program', 'year_level', 'address', 'profile_picture',
        ];

        $model = new Student;

        $this->assertSame($expected, $model->getFillable());
    }

    /**
     * Test that student_id has a unique constraint.
     */
    public function test_student_id_must_be_unique(): void
    {
        $base = $this->baseStudentData();

        Student::create($base);

        $this->expectException(QueryException::class);

        Student::create(array_merge($base, ['email' => 'other@example.com']));
    }

    /**
     * Test that email has a unique constraint.
     */
    public function test_email_must_be_unique(): void
    {
        $base = $this->baseStudentData();

        Student::create($base);

        $this->expectException(QueryException::class);

        Student::create(array_merge($base, ['student_id' => 'S9999999']));
    }

    /**
     * Test that middle_name accepts null.
     */
    public function test_middle_name_accepts_null(): void
    {
        $student = Student::create(array_merge($this->baseStudentData(), [
            'middle_name' => null,
        ]));

        $this->assertNull($student->middle_name);
    }

    /**
     * Test that profile_picture accepts null.
     */
    public function test_profile_picture_accepts_null(): void
    {
        $student = Student::create(array_merge($this->baseStudentData(), [
            'profile_picture' => null,
        ]));

        $this->assertNull($student->profile_picture);
    }

    /**
     * @return array<string, mixed>
     */
    private function baseStudentData(): array
    {
        return [
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
            'address' => '123 Sample Street, Barangay Sample',
            'profile_picture' => null,
        ];
    }
}
