<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Property-based test for profile picture path storage.
 *
 * Feature: student-registration-system, Property 6: Profile picture path stored, not binary content
 *
 * Requirements: 3.4, 3.5
 */
class ProfilePicturePropertyTest extends TestCase
{
    use RefreshDatabase;

    private const ITERATIONS = 100;

    // Minimal valid 1×1 JPEG bytes (no GD required)
    private const JPEG_1X1 = "\xff\xd8\xff\xe0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00"
        ."\xff\xdb\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t"
        ."\x08\n\x0c\x14\r\x0c\x0b\x0b\x0c\x19\x12\x13\x0f\x14\x1d\x1a"
        ."\x1f\x1e\x1d\x1a\x1c\x1c $.' \",#\x1c\x1c(7),01444\x1f'9=82<.342\x1e\x1f "
        ."!$# &'+,49;=8? "
        ."\xff\xc0\x00\x0b\x08\x00\x01\x00\x01\x01\x01\x11\x00"
        ."\xff\xc4\x00\x1f\x00\x00\x01\x05\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x00"
        ."\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\x0b"
        ."\xff\xda\x00\x08\x01\x01\x00\x00?\x00\xfb\x28\xa2\x8a\xff\xd9";

    private function fakeJpeg(string $name = 'photo.jpg'): UploadedFile
    {
        $tmpPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.$name;
        file_put_contents($tmpPath, self::JPEG_1X1);

        return new UploadedFile($tmpPath, $name, 'image/jpeg', null, true);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(string $studentId, string $email, UploadedFile $photo): array
    {
        return [
            'student_id' => $studentId,
            'first_name' => 'Juan',
            'last_name' => 'Cruz',
            'email' => $email,
            'mobile_number' => '09171234567',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'program' => 'BSIT',
            'year_level' => 1,
            'address' => '123 Sample Street Barangay Sample City',
            'profile_picture' => $photo,
        ];
    }

    /**
     * Property 6: profile_picture column stores a path string, not binary content.
     */
    public function test_property_6_profile_picture_path_stored_not_binary(): void
    {
        Storage::fake('public');

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // Each iteration uses a unique name so storage doesn't collide
            $name = 'photo_'.$i.'.jpg';
            $photo = $this->fakeJpeg($name);

            $studentId = 'S'.str_pad((string) ($i + 1), 7, '0', STR_PAD_LEFT);
            $email = "student{$i}@example.com";

            $response = $this->post(route('students.store'), $this->validPayload($studentId, $email, $photo));

            $student = Student::where('student_id', $studentId)->first();

            // The column must be a non-empty string
            $this->assertNotNull($student->profile_picture,
                "Iteration {$i}: profile_picture should not be null on successful upload."
            );
            $this->assertIsString($student->profile_picture,
                "Iteration {$i}: profile_picture should be a string path."
            );
            $this->assertNotEmpty($student->profile_picture,
                "Iteration {$i}: profile_picture path should not be empty."
            );

            // Must start with the storage subdirectory
            $this->assertStringStartsWith('profile_pictures/', $student->profile_picture,
                "Iteration {$i}: profile_picture path must start with 'profile_pictures/'."
            );

            // Must NOT contain binary data indicators
            $this->assertStringNotContainsString('data:', $student->profile_picture,
                "Iteration {$i}: profile_picture must not contain a data URI prefix."
            );

            // Must be a path-like string (no null bytes or binary control characters)
            $this->assertMatchesRegularExpression('/^[\w\/\-\.]+$/', $student->profile_picture,
                "Iteration {$i}: profile_picture must be a clean path string."
            );
        }
    }
}
