<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature tests for flash notification banners.
 *
 * Requirements: 5.1, 5.3, 5.4
 */
class FlashNotificationTest extends TestCase
{
    use RefreshDatabase;

    // Minimal 1×1 JPEG without GD
    private const JPEG_1X1 = "\xff\xd8\xff\xe0\x00\x10JFIF\x00\x01\x01\x00\x00\x01\x00\x01\x00\x00"
        ."\xff\xdb\x00C\x00\x08\x06\x06\x07\x06\x05\x08\x07\x07\x07\t\t"
        ."\x08\n\x0c\x14\r\x0c\x0b\x0b\x0c\x19\x12\x13\x0f\x14\x1d\x1a"
        ."\x1f\x1e\x1d\x1a\x1c\x1c $.' \",#\x1c\x1c(7),01444\x1f'9=82<.342\x1e\x1f "
        ."!$# &'+,49;=8? "
        ."\xff\xc0\x00\x0b\x08\x00\x01\x00\x01\x01\x01\x11\x00"
        ."\xff\xc4\x00\x1f\x00\x00\x01\x05\x01\x01\x01\x01\x01\x01\x00\x00\x00\x00\x00\x00\x00"
        ."\x00\x01\x02\x03\x04\x05\x06\x07\x08\t\n\x0b"
        ."\xff\xda\x00\x08\x01\x01\x00\x00?\x00\xfb\x28\xa2\x8a\xff\xd9";

    private function fakeJpeg(): UploadedFile
    {
        $tmpPath = sys_get_temp_dir().DIRECTORY_SEPARATOR.'flash_test.jpg';
        file_put_contents($tmpPath, self::JPEG_1X1);

        return new UploadedFile($tmpPath, 'flash_test.jpg', 'image/jpeg', null, true);
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(): array
    {
        return [
            'student_id' => 'S1234567',
            'first_name' => 'Juan',
            'last_name' => 'Cruz',
            'email' => 'juan@example.com',
            'mobile_number' => '09171234567',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'program' => 'BSIT',
            'year_level' => 1,
            'address' => '123 Sample Street Barangay Sample City',
            'profile_picture' => $this->fakeJpeg(),
        ];
    }

    /**
     * Successful registration shows success banner on profile page.
     */
    public function test_successful_registration_shows_success_banner_on_profile(): void
    {
        Storage::fake('public');

        $response = $this->post(route('students.store'), $this->validPayload());

        $student = Student::first();

        // Follow redirect to profile page
        $profileResponse = $this->get(route('students.show', $student));
        $profileResponse->assertStatus(200);
        $profileResponse->assertSee('Student registered successfully!');
    }

    /**
     * Success banner is absent on subsequent page refresh (flash is consumed once).
     */
    public function test_success_banner_absent_on_subsequent_page_refresh(): void
    {
        Storage::fake('public');

        $this->post(route('students.store'), $this->validPayload());

        $student = Student::first();

        // First visit consumes the flash
        $this->get(route('students.show', $student));

        // Second visit — banner should be gone
        $secondVisit = $this->get(route('students.show', $student));
        $secondVisit->assertDontSee('Student registered successfully!');
    }

    /**
     * Database failure during store shows error banner on form.
     *
     * We verify that when the session contains an 'error' flash (as set by the controller
     * on a DB failure), the shared layout renders it as a visible red banner.
     * The controller's error-flash path is tested in StoreStudentTest; here we confirm
     * the layout renders it correctly.
     */
    public function test_error_flash_banner_is_rendered_on_create_form(): void
    {
        $this->withSession([
            'error' => 'Registration could not be completed due to a database error. Please try again.',
        ]);

        $response = $this->get(route('students.create'));

        $response->assertStatus(200);
        $response->assertSee('Registration could not be completed due to a database error. Please try again.');
    }
}
