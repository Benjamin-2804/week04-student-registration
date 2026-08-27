<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature tests for StudentController@store success and failure paths.
 *
 * Requirements: 2.2, 2.4, 2.6, 3.7, 3.8, 4.1, 4.2, 4.3, 4.4
 */
class StoreStudentTest extends TestCase
{
    use RefreshDatabase;

    // Minimal 1×1 pixel JPEG — raw bytes that pass the `image` / `mimes:jpg` rule without GD.
    private const JPEG_1X1 = "\xff\xd8\xff\xe0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00"
        ."\xff\xdb\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t"
        ."\x08\n\x0c\x14\r\x0c\x0b\x0b\x0c\x19\x12\x13\x0f\x14\x1d\x1a"
        ."\x1f\x1e\x1d\x1a\x1c\x1c $.' \",#\x1c\x1c(7),01444\x1f'9=82<.342\x1e\x1f "
        ."!$# &'+,49;=8? "
        ."\xff\xc0\x00\x0b\x08\x00\x01\x00\x01\x01\x01\x11\x00"
        ."\xff\xc4\x00\x1f\x00\x00\x01\x05\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x00"
        ."\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\x0b"
        ."\xff\xc4\x00\xb5\x10\x00\x02\x01\x03\x03\x02\x04\x03\x05\x05\x04\x04\x00\x00\x01}"
        ."\x01\x02\x03\x00\x04\x11\x05\x12!1A\x06\x13Qa\x07\"q\x142\x81\x91\xa1\x08#B\xb1\xc1"
        ."\x15R\xd1\xf0$3br\x82\t\n\x16\x17\x18\x19\x1a%&'()*456789:CDEFGHIJSTUVWXYZ"
        ."cdefghijstuvwxyz\x83\x84\x85\x86\x87\x88\x89\x8a\x92\x93\x94\x95\x96\x97\x98\x99\x9a"
        ."\xa2\xa3\xa4\xa5\xa6\xa7\xa8\xa9\xaa\xb2\xb3\xb4\xb5\xb6\xb7\xb8\xb9\xba\xc2\xc3\xc4"
        ."\xc5\xc6\xc7\xc8\xc9\xca\xd2\xd3\xd4\xd5\xd6\xd7\xd8\xd9\xda\xe1\xe2\xe3\xe4\xe5\xe6"
        ."\xe7\xe8\xe9\xea\xf1\xf2\xf3\xf4\xf5\xf6\xf7\xf8\xf9\xfa"
        ."\xff\xda\x00\x08\x01\x01\x00\x00?\x00\xfb\x28\xa2\x8a\xff\xd9";

    /**
     * Create a fake JPEG UploadedFile without requiring the GD extension.
     */
    private function fakeJpeg(string $name = 'photo.jpg', int $sizeKb = 10): UploadedFile
    {
        // Pad to simulate desired size
        $content = self::JPEG_1X1;
        if ($sizeKb > 1) {
            $content .= str_repeat("\x00", ($sizeKb * 1024) - strlen(self::JPEG_1X1));
        }

        $tmpPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.$name;
        file_put_contents($tmpPath, $content);

        return new UploadedFile($tmpPath, $name, 'image/jpeg', null, true);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(?UploadedFile $photo = null): array
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
            'address' => '123 Sample Street Barangay Sample City',
            'profile_picture' => $photo ?? $this->fakeJpeg(),
        ];
    }

    /**
     * Valid form submission creates a DB record and redirects to profile view.
     */
    public function test_valid_submission_creates_record_and_redirects_to_profile(): void
    {
        Storage::fake('public');

        $response = $this->post(route('students.store'), $this->validPayload());

        $this->assertDatabaseCount('students', 1);

        $student = Student::first();
        $response->assertRedirect(route('students.show', $student));
    }

    /**
     * Duplicate student_id returns form with field-level error and old input restored.
     */
    public function test_duplicate_student_id_returns_field_error_with_old_input(): void
    {
        Storage::fake('public');

        Student::create([
            'student_id' => 'S1234567',
            'first_name' => 'Juan',
            'last_name' => 'Cruz',
            'email' => 'first@example.com',
            'mobile_number' => '09171234567',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'program' => 'BSIT',
            'year_level' => 1,
            'address' => '123 Sample Street Barangay Sample City',
        ]);

        $payload = $this->validPayload();
        $payload['email'] = 'second@example.com';

        $response = $this->post(route('students.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseCount('students', 1);
        $response->assertSessionHasErrors('student_id');
    }

    /**
     * Duplicate email returns form with field-level error.
     */
    public function test_duplicate_email_returns_field_error(): void
    {
        Storage::fake('public');

        Student::create([
            'student_id' => 'S9999999',
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'juan@example.com',
            'mobile_number' => '09171234567',
            'date_of_birth' => '2000-01-01',
            'gender' => 'female',
            'program' => 'BSIT',
            'year_level' => 1,
            'address' => '123 Sample Street Barangay Sample City',
        ]);

        $payload = $this->validPayload();
        $payload['student_id'] = 'S1111111';

        $response = $this->post(route('students.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseCount('students', 1);
        $response->assertSessionHasErrors('email');
    }

    /**
     * Missing required field returns validation error without DB write.
     */
    public function test_missing_required_field_returns_validation_error_without_db_write(): void
    {
        Storage::fake('public');

        $payload = $this->validPayload();
        unset($payload['first_name']);

        $response = $this->post(route('students.store'), $payload);

        $response->assertSessionHasErrors('first_name');
        $this->assertDatabaseCount('students', 0);
    }

    /**
     * Old input restored on validation failure.
     */
    public function test_old_input_restored_on_validation_failure(): void
    {
        Storage::fake('public');

        $payload = $this->validPayload();
        $payload['first_name'] = ''; // trigger failure

        $this->post(route('students.store'), $payload);

        $this->assertEquals('S1234567', session()->getOldInput('student_id'));
        $this->assertEquals('juan@example.com', session()->getOldInput('email'));
    }

    /**
     * Oversized file rejected with profile_picture field error.
     */
    public function test_oversized_file_rejected_with_field_error(): void
    {
        Storage::fake('public');

        // 2049 KB — over the 2048 KB limit
        $oversized = $this->fakeJpeg('big.jpg', 2049);

        $payload = $this->validPayload($oversized);

        $response = $this->post(route('students.store'), $payload);

        $response->assertSessionHasErrors('profile_picture');
        $this->assertDatabaseCount('students', 0);
    }

    /**
     * Non-image file rejected with profile_picture field error.
     */
    public function test_non_image_file_rejected_with_field_error(): void
    {
        Storage::fake('public');

        $tmpPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'document.pdf';
        file_put_contents($tmpPath, '%PDF-1.4 fake content');
        $pdf = new UploadedFile($tmpPath, 'document.pdf', 'application/pdf', null, true);

        $payload = $this->validPayload($pdf);

        $response = $this->post(route('students.store'), $payload);

        $response->assertSessionHasErrors('profile_picture');
        $this->assertDatabaseCount('students', 0);
    }

    /**
     * File storage failure saves record with null picture and shows notice.
     */
    public function test_file_storage_failure_saves_record_with_null_picture(): void
    {
        Storage::fake('public');

        // Mock the Storage facade BEFORE the request so putFile throws
        Storage::shouldReceive('fake')->passthru();
        Storage::shouldReceive('disk')
            ->with('public')
            ->once()
            ->andReturnSelf();
        Storage::shouldReceive('putFile')
            ->once()
            ->andThrow(new \RuntimeException('Storage failed'));

        $response = $this->post(route('students.store'), $this->validPayload());

        // Record still created with null picture
        $this->assertDatabaseCount('students', 1);
        $this->assertDatabaseHas('students', ['profile_picture' => null]);

        // Redirected to show page
        $student = Student::first();
        $response->assertRedirect(route('students.show', $student));
    }
}
