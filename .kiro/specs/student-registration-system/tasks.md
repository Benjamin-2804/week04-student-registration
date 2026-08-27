# Implementation Plan: Student Registration System

## Overview

Implement a Laravel MVC web application for student registration. The implementation follows standard Laravel conventions: database migration and Eloquent model first, then the Form Request validation layer, then the controller, then Blade views and layout. Property-based tests validate the pure validation layer; feature tests cover routing, file upload, persistence, flash messages, and profile display.

---

## Tasks

- [ ] 1. Database migration and Eloquent model
  - [x] 1.1 Create the `create_students_table` migration
    - Define all columns per the data model: `id`, `student_id` (unique), `first_name`, `middle_name` (nullable), `last_name`, `email` (unique), `mobile_number`, `date_of_birth`, `gender`, `program`, `year_level` (unsignedTinyInteger), `address`, `profile_picture` (nullable), `timestamps()`
    - Run `php artisan migrate` against SQLite (or MySQL) to confirm the migration executes without errors
    - _Requirements: 4.5, 4.6_

  - [x] 1.2 Create the `Student` Eloquent model
    - Set the `$fillable` array to all mass-assignable fields
    - Confirm the model resolves against the migration table
    - _Requirements: 4.5_

  - [-] 1.3 Write unit tests for the Student model
    - Test that all fillable fields can be mass-assigned
    - Test that `student_id` and `email` columns carry unique constraints (attempt duplicate insert, expect `QueryException`)
    - Test that `middle_name` and `profile_picture` accept null
    - _Requirements: 4.5, 4.6_

- [ ] 2. Form Request validation layer
  - [-] 2.1 Create `StoreStudentRequest` with all validation rules
    - Implement `authorize()` returning `true`
    - Implement `rules()` with all field rules from the design: required/nullable, alpha constraints, unique checks, `digits_between:10,15`, `before:today` + age-15 guard, `in:male,female,other`, `between:1,6`, `min:10|max:500`, `image|mimes:jpg,jpeg,png|max:2048`
    - _Requirements: 2.1_

  - [~] 2.2 Write property test for Property 1 — valid submissions always pass validation
    - **Property 1: Valid submissions always pass validation**
    - Generate random valid field combinations (names within 100 chars, valid email, 10–15 digit mobile, DOB yielding age ≥ 15, valid gender, year level 1–6, address 10–500 chars, valid image stub), assert validator returns no errors
    - Run minimum 100 iterations
    - **Validates: Requirements 2.1**

  - [~] 2.3 Write property test for Property 2 — invalid field values always fail validation
    - **Property 2: Invalid field values always fail validation**
    - For each field in isolation, generate values that violate its rule and assert at least one field-level error is returned with no DB write
    - Run minimum 100 iterations
    - **Validates: Requirements 2.1, 2.2, 4.1**

  - [~] 2.4 Write property test for Property 3 — uniqueness rejection on duplicate student_id or email
    - **Property 3: Uniqueness rejection on duplicate student_id or email**
    - Persist a student record, then attempt a second submission with the same `student_id` or `email`, assert the validator/DB layer returns a field-level error and only one record exists
    - Run minimum 100 iterations
    - **Validates: Requirements 2.1, 4.3, 4.6**

  - [~] 2.5 Write property test for Property 4 — mobile number digit-length boundary
    - **Property 4: Mobile number digit-length boundary**
    - Generate strings of length 0–20 with varying character composition; assert acceptance iff length is 10–15 and all characters are digits
    - Run minimum 100 iterations
    - **Validates: Requirements 2.1**

  - [~] 2.6 Write property test for Property 5 — age-at-submission invariant
    - **Property 5: Age-at-submission invariant**
    - Generate `date_of_birth` values relative to today; assert acceptance iff age ≥ 15 years, rejection if age < 15 years (boundary: exactly 14 years 364 days rejected)
    - Run minimum 100 iterations
    - **Validates: Requirements 2.1**

  - [~] 2.7 Write property test for Property 7 — year level boundary invariant
    - **Property 7: Year level boundary invariant**
    - Generate integers from -5 to 10 and non-integer strings; assert acceptance iff value is an integer in [1, 6]
    - Run minimum 100 iterations
    - **Validates: Requirements 2.1**

  - [~] 2.8 Write property test for Property 8 — address length boundary invariant
    - **Property 8: Address length boundary invariant**
    - Generate strings of length 0–600; assert acceptance iff length is in [10, 500]
    - Run minimum 100 iterations
    - **Validates: Requirements 2.1**

- [~] 3. Checkpoint — Ensure all validation tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 4. Routes and controller skeleton
  - [x] 4.1 Define the four named routes in `routes/web.php`
    - `GET /students` → `StudentController@index` (name: `students.index`)
    - `GET /students/create` → `StudentController@create` (name: `students.create`)
    - `POST /students` → `StudentController@store` (name: `students.store`)
    - `GET /students/{student}` → `StudentController@show` (name: `students.show`)
    - Confirm implicit route-model binding on `{student}` resolves to the `Student` model
    - _Requirements: 7.1, 7.5_

  - [ ] 4.2 Create `StudentController` with all four method stubs
    - `index()`: fetch all students (paginated), pass to `students.index` view
    - `create()`: return `students.create` view
    - `store(StoreStudentRequest $request)`: stub — return redirect for now
    - `show(Student $student)`: return `students.show` view with the bound model
    - _Requirements: 7.2, 7.3_

  - [~] 4.3 Write feature tests for routing
    - `GET /students/create` returns HTTP 200
    - `GET /students/{valid-id}` returns HTTP 200
    - `GET /students/{nonexistent-id}` returns HTTP 404
    - Undefined route returns HTTP 404
    - _Requirements: 1.1, 6.4, 7.5_

- [ ] 5. Shared layout and registration form view
  - [~] 5.1 Create `resources/views/layouts/app.blade.php`
    - Include Tailwind CSS (CDN or compiled asset)
    - Define `@yield('content')` slot and a page title slot
    - _Requirements: 1.5_

  - [~] 5.2 Create `resources/views/students/create.blade.php`
    - Extend the shared layout
    - Render all required input fields with correct `name` attributes matching validation rule keys
    - Visually mark required fields; mark Middle Name as optional
    - Group fields into four labeled sections: Personal Information, Contact Information, Academic Information, Profile Picture
    - Wire `old()` helper to each non-file input for value restoration on validation failure
    - Display `@error('field')` messages beneath each input
    - Display the profile-picture re-select notice when a validation failure carries that session flag
    - Include a submit button labeled "Register Student"
    - _Requirements: 1.3, 1.4, 1.5, 1.6, 2.3, 2.4, 2.6_

  - [~] 5.3 Write feature tests for form rendering
    - `GET /students/create` renders a form containing all required field names
    - Form contains a submit button with label "Register Student"
    - _Requirements: 1.3, 1.6_

- [ ] 6. `store()` controller action — file upload and persistence
  - [~] 6.1 Implement `StudentController@store` with file storage and record creation
    - Store the profile picture via `Storage::disk('public')->putFile('profile_pictures', $request->file('profile_picture'))` inside a try/catch for file storage failure
    - Call `Student::create([...])` with validated fields plus the stored path (or `null` on storage failure)
    - On storage failure: create the record with `profile_picture = null` and flash a storage-failure notice
    - Catch `QueryException` for duplicate key violations (SQL state `23000`): redirect back with `withInput()` and field-level error identifying the duplicate field
    - Catch any other `QueryException`: redirect back with `withInput()` and a general error flash
    - On success: flash a success message and redirect to `route('students.show', $student)`
    - _Requirements: 3.1, 3.4, 3.5, 3.8, 4.1, 4.2, 4.3, 4.4, 5.1_

  - [~] 6.2 Write property test for Property 6 — profile picture path stored, not binary
    - **Property 6: Profile picture path stored, not binary content**
    - Submit valid forms with image files of varying names and sizes; assert the `profile_picture` column is a non-empty string starting with `profile_pictures/` and contains no binary data or data URI prefix
    - Run minimum 100 iterations
    - **Validates: Requirements 3.4, 3.5**

  - [~] 6.3 Write feature tests for `store()` success and failure paths
    - Valid form submission creates a DB record and redirects to profile view
    - Duplicate `student_id` returns form with field-level error and old input restored
    - Duplicate `email` returns form with field-level error and old input restored
    - Missing required field returns validation error without DB write
    - Old input restored on validation failure; file input cleared
    - Oversized file rejected with `profile_picture` field error
    - Non-image file rejected with `profile_picture` field error
    - File storage failure saves record with `null` picture and shows notice
    - _Requirements: 2.2, 2.4, 2.6, 3.7, 3.8, 4.1, 4.2, 4.3, 4.4_

- [~] 7. Checkpoint — Ensure all store and validation tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 8. Flash notification banners
  - [~] 8.1 Add success and error flash banner rendering to the shared layout
    - Read `session('success')` and `session('error')` keys
    - Render success banner in green and error banner in red
    - Banners only appear on the page immediately after the redirect (session flash, one-time display)
    - _Requirements: 5.1, 5.3, 5.4, 5.5_

  - [~] 8.2 Write feature tests for flash notifications
    - Successful registration shows success banner on profile page
    - Success banner absent on subsequent page refresh (not re-flashed)
    - Database failure during store shows error banner on form
    - _Requirements: 5.1, 5.3, 5.4_

- [ ] 9. Student profile and student list views
  - [~] 9.1 Create `resources/views/students/show.blade.php`
    - Display all stored fields: Student_ID, First Name, Middle Name (or "N/A" if null/empty), Last Name, Email, Mobile Number, Date of Birth, Gender, Program, Year Level, Address
    - Show profile picture using `asset('storage/' . $student->profile_picture)` when the file exists on the public disk
    - Show placeholder image when `profile_picture` is null or file is inaccessible
    - Render the flash success banner (delegated to layout)
    - _Requirements: 6.1, 6.2, 6.3, 6.5, 6.6_

  - [~] 9.2 Create `resources/views/students/index.blade.php`
    - List all registered students with links to each profile view
    - Paginate using Laravel's built-in pagination
    - _Requirements: 7.1_

  - [~] 9.3 Write feature tests for profile view
    - Valid student ID renders profile with all fields
    - Null middle name displays "N/A"
    - Missing profile picture displays placeholder
    - Non-existent student ID returns 404
    - _Requirements: 6.1, 6.2, 6.4, 6.5, 6.6_

- [~] 10. Final checkpoint — Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

---

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Property-based tests use [Eris](https://github.com/giorgiosironi/eris) or [php-quickcheck](https://github.com/steos/php-quickcheck); each property test runs a minimum of 100 iterations
- Use `Storage::fake('public')` and `RefreshDatabase` in all feature tests to isolate filesystem and DB state
- Use `UploadedFile::fake()->image('photo.jpg', 100, 100)` for valid image upload mocks
- Run `php artisan storage:link` once in environment setup so that the public storage symlink is in place before integration tests that check public URLs
- Each task references specific requirements for traceability

---

## Task Dependency Graph

```json
{
  "waves": [
    { "id": 0, "tasks": ["1.1"] },
    { "id": 1, "tasks": ["1.2", "4.1"] },
    { "id": 2, "tasks": ["1.3", "2.1", "4.2"] },
    { "id": 3, "tasks": ["2.2", "2.3", "2.4", "2.5", "2.6", "2.7", "2.8", "4.3"] },
    { "id": 4, "tasks": ["5.1", "5.2"] },
    { "id": 5, "tasks": ["5.3", "6.1"] },
    { "id": 6, "tasks": ["6.2", "6.3", "8.1"] },
    { "id": 7, "tasks": ["8.2", "9.1", "9.2"] },
    { "id": 8, "tasks": ["9.3"] }
  ]
}
```
