<?php

namespace Tests\Feature;

use App\Http\Requests\StoreStudentRequest;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Property-based tests for the student registration validation layer.
 *
 * Tests Properties 1, 2, 4, 5, 7, and 8 — all pure validation (no DB writes).
 * Property 3 (uniqueness) is in UniquenessPropertyTest which handles DB state.
 *
 * Each test runs a minimum of 100 iterations over randomly generated inputs
 * to verify that the validation rules behave correctly across all valid/invalid
 * input combinations.
 */
class ValidationPropertyTest extends TestCase
{
    private const ITERATIONS = 100;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Build a complete valid data set for student registration.
     *
     * @return array<string, mixed>
     */
    private function validData(int $seed = 0): array
    {
        $faker = Factory::create();
        $faker->seed($seed);

        $dob = Carbon::now()->subYears(rand(15, 60))->subDays(rand(0, 364))->toDateString();

        return [
            'student_id' => 'S'.$faker->unique()->numerify('#######'),
            'first_name' => $faker->lexify(str_repeat('?', rand(1, 20))),
            'middle_name' => null,
            'last_name' => $faker->lexify(str_repeat('?', rand(1, 20))),
            'email' => $faker->unique()->safeEmail(),
            'mobile_number' => str_pad((string) rand(0, 99999), 10, '0', STR_PAD_LEFT),
            'date_of_birth' => $dob,
            'gender' => ['male', 'female', 'other'][rand(0, 2)],
            'program' => 'BSIT',
            'year_level' => rand(1, 4),
            'address' => str_pad('Sample Address ', 10, 'x'),
        ];
    }

    /**
     * Run validation rules directly, excluding profile_picture (file-only, tested separately).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, array<string>>
     */
    private function validate(array $data): array
    {
        $rules = (new StoreStudentRequest)->rules();
        unset($rules['profile_picture']);

        // Also remove unique rules — pure property tests don't touch the DB.
        $rules['student_id'] = array_filter($rules['student_id'], fn ($r) => ! str_starts_with((string) $r, 'unique:'));
        $rules['email'] = array_filter($rules['email'], fn ($r) => ! str_starts_with((string) $r, 'unique:'));

        $validator = Validator::make($data, $rules);

        return $validator->errors()->toArray();
    }

    // -------------------------------------------------------------------------
    // Property 1: Valid submissions always pass validation
    // Feature: student-registration-system, Property 1: Valid submissions always pass validation
    // -------------------------------------------------------------------------

    public function test_property_1_valid_submissions_always_pass(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $data = $this->validData($i);

            $errors = $this->validate($data);

            $this->assertEmpty(
                $errors,
                "Iteration {$i}: Expected no validation errors for valid data, got: ".json_encode($errors)
            );
        }
    }

    // -------------------------------------------------------------------------
    // Property 2: Invalid field values always fail validation
    // Feature: student-registration-system, Property 2: Invalid field values always fail validation
    // -------------------------------------------------------------------------

    public function test_property_2_empty_required_fields_fail(): void
    {
        $requiredFields = [
            'student_id', 'first_name', 'last_name', 'email',
            'mobile_number', 'date_of_birth', 'gender', 'program',
            'year_level', 'address',
        ];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $field = $requiredFields[$i % count($requiredFields)];
            $data = $this->validData($i);
            $data[$field] = '';

            $errors = $this->validate($data);

            $this->assertArrayHasKey(
                $field,
                $errors,
                "Iteration {$i}: Expected error on field '{$field}' when empty."
            );
        }
    }

    public function test_property_2_non_alpha_names_fail(): void
    {
        $nameFields = ['first_name', 'last_name'];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $field = $nameFields[$i % 2];
            $data = $this->validData($i);
            $data[$field] = 'Name123!';

            $errors = $this->validate($data);

            $this->assertArrayHasKey(
                $field,
                $errors,
                "Iteration {$i}: Expected error on '{$field}' with non-alpha value."
            );
        }
    }

    public function test_property_2_invalid_email_fails(): void
    {
        // Only emails that Laravel's built-in rule actually rejects
        $invalidEmails = [
            'notanemail',
            'missing@',
            '@nodomain.com',
            '',
            'double@@at.com',
            'spaces in@email.com',
        ];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $data = $this->validData($i);
            $data['email'] = $invalidEmails[$i % count($invalidEmails)];

            $errors = $this->validate($data);

            $this->assertArrayHasKey('email', $errors,
                "Iteration {$i}: Expected email error for '{$data['email']}'."
            );
        }
    }

    public function test_property_2_invalid_gender_fails(): void
    {
        $invalidGenders = ['M', 'F', 'yes', 'no', '1', 'MALE', 'Female', '', 'nonbinary', 'x'];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $data = $this->validData($i);
            $data['gender'] = $invalidGenders[$i % count($invalidGenders)];

            $errors = $this->validate($data);

            $this->assertArrayHasKey('gender', $errors,
                "Iteration {$i}: Expected gender error for '{$data['gender']}'."
            );
        }
    }

    // -------------------------------------------------------------------------
    // Property 4: Mobile number digit-length boundary
    // Feature: student-registration-system, Property 4: Mobile number digit-length boundary
    // -------------------------------------------------------------------------

    public function test_property_4_valid_mobile_numbers_accepted(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $length = 10 + ($i % 6); // cycles 10–15
            $mobile = str_pad((string) ($i * 7 + 1234567890), $length, '1', STR_PAD_RIGHT);
            $mobile = substr($mobile, 0, $length);

            $data = $this->validData($i);
            $data['mobile_number'] = $mobile;

            $errors = $this->validate($data);

            $this->assertArrayNotHasKey('mobile_number', $errors,
                "Iteration {$i}: Expected mobile '{$mobile}' (len={$length}) to be accepted."
            );
        }
    }

    public function test_property_4_short_mobile_numbers_rejected(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $length = $i % 10; // 0–9

            $mobile = str_repeat('1', $length);

            $data = $this->validData($i);
            $data['mobile_number'] = $mobile;

            $errors = $this->validate($data);

            $this->assertArrayHasKey('mobile_number', $errors,
                "Iteration {$i}: Expected mobile '{$mobile}' (len={$length}) to be rejected."
            );
        }
    }

    public function test_property_4_long_mobile_numbers_rejected(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $length = 16 + ($i % 10); // 16–25

            $mobile = str_repeat('1', $length);

            $data = $this->validData($i);
            $data['mobile_number'] = $mobile;

            $errors = $this->validate($data);

            $this->assertArrayHasKey('mobile_number', $errors,
                "Iteration {$i}: Expected mobile '{$mobile}' (len={$length}) to be rejected."
            );
        }
    }

    public function test_property_4_non_digit_mobile_numbers_rejected(): void
    {
        $nonDigitMobiles = [
            '0917-123-4567',
            '+639171234567',
            '09171234abc',
            'abcdefghij',
            '0917 1234567',
            '09171234567.',
        ];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $mobile = $nonDigitMobiles[$i % count($nonDigitMobiles)];

            $data = $this->validData($i);
            $data['mobile_number'] = $mobile;

            $errors = $this->validate($data);

            $this->assertArrayHasKey('mobile_number', $errors,
                "Iteration {$i}: Expected mobile '{$mobile}' with non-digits to be rejected."
            );
        }
    }

    // -------------------------------------------------------------------------
    // Property 5: Age-at-submission invariant
    // Feature: student-registration-system, Property 5: Age-at-submission invariant
    // -------------------------------------------------------------------------

    public function test_property_5_age_at_least_15_accepted(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $yearsAgo = 15 + ($i % 66); // 15–80

            $dob = Carbon::now()->subYears($yearsAgo)->toDateString();

            $data = $this->validData($i);
            $data['date_of_birth'] = $dob;

            $errors = $this->validate($data);

            $this->assertArrayNotHasKey('date_of_birth', $errors,
                "Iteration {$i}: DOB '{$dob}' (age {$yearsAgo}y) should be accepted."
            );
        }
    }

    public function test_property_5_age_under_15_rejected(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            // 1 day to 14 years 364 days old
            $daysUnder15 = 1 + ($i % ((15 * 365) - 1));
            $dob = Carbon::now()->subDays($daysUnder15)->toDateString();

            $data = $this->validData($i);
            $data['date_of_birth'] = $dob;

            $errors = $this->validate($data);

            $this->assertArrayHasKey('date_of_birth', $errors,
                "Iteration {$i}: DOB '{$dob}' ({$daysUnder15} days ago) should be rejected (age < 15)."
            );
        }
    }

    public function test_property_5_exactly_15_years_accepted(): void
    {
        $dob = Carbon::now()->subYears(15)->toDateString();

        $data = $this->validData(0);
        $data['date_of_birth'] = $dob;

        $errors = $this->validate($data);

        $this->assertArrayNotHasKey('date_of_birth', $errors,
            "DOB of exactly 15 years ago ('{$dob}') should be accepted."
        );
    }

    public function test_property_5_boundary_14_years_364_days_rejected(): void
    {
        // One day shy of 15 years
        $dob = Carbon::now()->subYears(15)->addDay()->toDateString();

        $data = $this->validData(0);
        $data['date_of_birth'] = $dob;

        $errors = $this->validate($data);

        $this->assertArrayHasKey('date_of_birth', $errors,
            "DOB of 14y 364d ago ('{$dob}') should be rejected."
        );
    }

    // -------------------------------------------------------------------------
    // Property 7: Year level boundary invariant
    // Feature: student-registration-system, Property 7: Year level boundary invariant
    // -------------------------------------------------------------------------

    public function test_property_7_valid_year_levels_accepted(): void
    {
        $validLevels = [1, 2, 3, 4];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $level = $validLevels[$i % count($validLevels)];

            $data = $this->validData($i);
            $data['year_level'] = $level;

            $errors = $this->validate($data);

            $this->assertArrayNotHasKey('year_level', $errors,
                "Iteration {$i}: Year level {$level} should be accepted."
            );
        }
    }

    public function test_property_7_out_of_range_integers_rejected(): void
    {
        $candidates = array_merge(range(-5, 0), range(5, 10));

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $level = $candidates[$i % count($candidates)];

            $data = $this->validData($i);
            $data['year_level'] = $level;

            $errors = $this->validate($data);

            $this->assertArrayHasKey('year_level', $errors,
                "Iteration {$i}: Year level {$level} (outside [1,6]) should be rejected."
            );
        }
    }

    public function test_property_7_non_integer_values_rejected(): void
    {
        $nonIntegers = ['a', 'one', '1.5', '2.0', '', 'null', '1a', 'year1'];

        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $value = $nonIntegers[$i % count($nonIntegers)];

            $data = $this->validData($i);
            $data['year_level'] = $value;

            $errors = $this->validate($data);

            $this->assertArrayHasKey('year_level', $errors,
                "Iteration {$i}: Year level '{$value}' (non-integer) should be rejected."
            );
        }
    }

    // -------------------------------------------------------------------------
    // Property 8: Address length boundary invariant
    // Feature: student-registration-system, Property 8: Address length boundary invariant
    // -------------------------------------------------------------------------

    public function test_property_8_valid_address_lengths_accepted(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $length = 10 + ($i % 491); // 10–500

            $address = str_repeat('x', $length);

            $data = $this->validData($i);
            $data['address'] = $address;

            $errors = $this->validate($data);

            $this->assertArrayNotHasKey('address', $errors,
                "Iteration {$i}: Address of length {$length} should be accepted."
            );
        }
    }

    public function test_property_8_short_addresses_rejected(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $length = $i % 10; // 0–9

            $address = str_repeat('x', $length);

            $data = $this->validData($i);
            $data['address'] = $address;

            $errors = $this->validate($data);

            $this->assertArrayHasKey('address', $errors,
                "Iteration {$i}: Address of length {$length} should be rejected (< 10)."
            );
        }
    }

    public function test_property_8_long_addresses_rejected(): void
    {
        for ($i = 0; $i < self::ITERATIONS; $i++) {
            $length = 501 + ($i % 100); // 501–600

            $address = str_repeat('x', $length);

            $data = $this->validData($i);
            $data['address'] = $address;

            $errors = $this->validate($data);

            $this->assertArrayHasKey('address', $errors,
                "Iteration {$i}: Address of length {$length} should be rejected (> 500)."
            );
        }
    }
}
