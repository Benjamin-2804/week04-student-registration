# Design Document: Student Registration System

## Overview

The Student Registration System is a Laravel web application that replaces a paper-based registration process for the College of Information Technology. College staff can register new students by filling out a Blade-rendered form, uploading a profile picture, and submitting the data. The system validates every field, persists valid records to a relational database, stores uploaded images on the server filesystem, and presents a profile view upon successful registration.

The application follows standard Laravel MVC conventions: routes defined in `routes/web.php`, a single `StudentController` handling four actions, a `StoreStudentRequest` Form Request class for validation, an Eloquent `Student` model backed by a migration, and Blade templates for all views.

---

## Architecture

```mermaid
flowchart TD
    Browser -->|GET /students/create| Router
    Browser -->|POST /students| Router
    Browser -->|GET /students/{id}| Router
    Router --> StudentController
    StudentController -->|validates via| StoreStudentRequest
    StoreStudentRequest -->|passes| StudentController
    StudentController -->|stores file| FileSystem["storage/app/public"]
    StudentController -->|persists record| Student["Student Model (Eloquent)"]
    Student --> Database[(MySQL / SQLite)]
    StudentController -->|renders| BladeViews["Blade Views"]
    FileSystem -->|symlink| PublicStorage["public/storage (web-accessible)"]
    BladeViews --> Browser
```

**Request flow for registration:**

1. Staff navigates to `GET /students/create` → `StudentController@create` returns the form view.
2. Staff submits the form → `POST /students` is routed to `StudentController@store`.
3. Laravel resolves `StoreStudentRequest`; validation runs before `store()` body executes.
4. On failure: Laravel automatically redirects back with errors and old input.
5. On success: controller stores the profile picture, saves the Eloquent record, sets a flash message, and redirects to `GET /students/{student}`.
6. `StudentController@show` retrieves the record by its route-model-bound ID and renders the profile view.

---

## Components and Interfaces

### Routes (`routes/web.php`)

| Verb | URI | Controller Method | Name |
|------|-----|-------------------|------|
| GET  | `/students` | `StudentController@index` | `students.index` |
| GET  | `/students/create` | `StudentController@create` | `students.create` |
| POST | `/students` | `StudentController@store` | `students.store` |
| GET  | `/students/{student}` | `StudentController@show` | `students.show` |

Route-model binding on `{student}` will use Laravel's implicit binding against the `Student` model's primary key. A missing record will automatically yield an HTTP 404 response.

### `StudentController`

```php
class StudentController extends Controller
{
    public function index(): View;
    public function create(): View;
    public function store(StoreStudentRequest $request): RedirectResponse;
    public function show(Student $student): View;
}
```

- **`index()`**: Retrieves all student records (paginated) and passes them to `students.index`.
- **`create()`**: Returns the `students.create` Blade view (no data required).
- **`store(StoreStudentRequest $request)`**: Receives a validated request; stores the profile picture; creates a `Student` record; flashes a success message; redirects to `students.show`.
- **`show(Student $student)`**: Receives a model instance via route-model binding; renders `students.show`.

### `StoreStudentRequest` (Form Request)

Encapsulates all validation rules. Laravel resolves this class before the `store()` method body runs — an invalid submission is automatically redirected back with errors and old input before any controller logic executes.

```php
class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool  // returns true (auth handled separately)
    public function rules(): array     // all field validation rules
    public function messages(): array  // custom field-level error messages (optional)
}
```

### `Student` Eloquent Model

```php
class Student extends Model
{
    protected $fillable = [
        'student_id', 'first_name', 'middle_name', 'last_name',
        'email', 'mobile_number', 'gender', 'date_of_birth',
        'program', 'year_level', 'address', 'profile_picture',
    ];
}
```

### Blade Views

| View | Route | Purpose |
|------|-------|---------|
| `resources/views/students/create.blade.php` | `students.create` | Registration form |
| `resources/views/students/show.blade.php` | `students.show` | Student profile |
| `resources/views/students/index.blade.php` | `students.index` | Student list |
| `resources/views/layouts/app.blade.php` | — | Shared layout with Tailwind CSS |

### File Storage

Profile pictures are stored via Laravel's `Storage` facade:

```php
$path = $request->file('profile_picture')->store('profile_pictures', 'public');
// Resulting path: storage/app/public/profile_pictures/<unique-name>.<ext>
// Public URL: asset('storage/profile_pictures/<unique-name>.<ext>')
```

Laravel's `store()` method generates a unique filename automatically (UUID-based). The `php artisan storage:link` command creates the symlink from `public/storage` → `storage/app/public`.

---

## Data Models

### Database Migration: `create_students_table`

```php
Schema::create('students', function (Blueprint $table) {
    $table->id();                                          // system-generated PK
    $table->string('student_id')->unique();                // unique student ID
    $table->string('first_name', 100);
    $table->string('middle_name', 100)->nullable();
    $table->string('last_name', 100);
    $table->string('email')->unique();
    $table->string('mobile_number', 15);
    $table->date('date_of_birth');
    $table->string('gender', 20);
    $table->string('program', 100);
    $table->unsignedTinyInteger('year_level');             // 1–6
    $table->text('address');
    $table->string('profile_picture')->nullable();         // relative path only
    $table->timestamps();
});
```

Key design decisions:
- `student_id` and `email` carry database-level `UNIQUE` constraints in addition to validation-layer uniqueness checks. This prevents race conditions on concurrent submissions.
- `profile_picture` stores only the relative storage path (e.g., `profile_pictures/abc123.jpg`), not binary content.
- `middle_name` is nullable to represent the optional field.
- `year_level` is a small unsigned integer; the valid range (1–6) is enforced at the validation layer.

### Validation Rules (`StoreStudentRequest::rules()`)

```php
return [
    'student_id'      => ['required', 'string', 'unique:students,student_id'],
    'first_name'      => ['required', 'string', 'alpha', 'max:100'],
    'middle_name'     => ['nullable', 'string', 'alpha', 'max:100'],
    'last_name'       => ['required', 'string', 'alpha', 'max:100'],
    'email'           => ['required', 'email', 'unique:students,email'],
    'mobile_number'   => ['required', 'digits_between:10,15'],
    'date_of_birth'   => ['required', 'date', 'before:today', 'before_or_equal:' . now()->subYears(15)->toDateString()],
    'gender'          => ['required', 'in:male,female,other'],
    'program'         => ['required', 'string', 'max:100'],
    'year_level'      => ['required', 'integer', 'between:1,6'],
    'address'         => ['required', 'string', 'min:10', 'max:500'],
    'profile_picture' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
];
```

---

## Correctness Properties

This feature is primarily a Laravel CRUD web application involving form handling, file upload, Blade rendering, and database persistence. The core logic — validation rule evaluation — is a pure, deterministic function that maps field values to pass/fail outcomes. This makes the validation layer well-suited to property-based testing. Infrastructure concerns (storage symlink availability, database connectivity) are integration concerns and will be tested with examples rather than properties.

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

---

### Property 1: Valid submissions always pass validation

*For any* set of student field values that satisfies every rule (non-empty names, valid email, 10–15 digit mobile, past date of birth with age ≥ 15, accepted gender, year level 1–6, address between 10–500 chars, image file ≤ 2048 KB), the `StoreStudentRequest` validation SHALL pass and return no errors.

**Validates: Requirements 2.1**

---

### Property 2: Invalid field values always fail validation

*For any* student submission where at least one field violates its rule (empty required field, email already in use, mobile number with non-digit characters or wrong length, age < 15, year level outside 1–6, address too short or too long, non-image file attachment), the validator SHALL reject the submission and return at least one field-level error — with no database write occurring.

**Validates: Requirements 2.1, 2.2, 4.1**

---

### Property 3: Uniqueness rejection on duplicate student_id or email

*For any* two registration submissions that share the same `student_id` or the same `email`, the second submission SHALL be rejected by the validator with a field-level error identifying the duplicate field, and only one record SHALL exist in the database.

**Validates: Requirements 2.1, 4.3, 4.6**

---

### Property 4: Mobile number digit-length boundary

*For any* mobile number string, the validator SHALL accept it if and only if it contains between 10 and 15 digits (inclusive) and no non-digit characters. Strings of length 9 or less, or 16 or more, SHALL be rejected. Strings containing letters or symbols SHALL be rejected regardless of length.

**Validates: Requirements 2.1**

---

### Property 5: Age-at-submission invariant

*For any* `date_of_birth` value, the validator SHALL accept it if and only if the resulting age at the time of submission is at least 15 years. Dates that yield an age of exactly 15 years SHALL be accepted; dates that yield an age of 14 years and 364 days SHALL be rejected.

**Validates: Requirements 2.1**

---

### Property 6: Profile picture path stored, not binary content

*For any* successful registration, the `profile_picture` column in the stored student record SHALL contain a non-empty string path pointing to a file within the `profile_pictures/` subdirectory of the public storage disk, and SHALL NOT contain binary data or a data URI.

**Validates: Requirements 3.4, 3.5**

---

### Property 7: Year level boundary invariant

*For any* `year_level` value, the validator SHALL accept it if and only if it is an integer in the closed interval [1, 6]. Values of 0, 7, negative integers, and non-integer strings SHALL be rejected.

**Validates: Requirements 2.1**

---

### Property 8: Address length boundary invariant

*For any* address string, the validator SHALL accept it if and only if its length is between 10 and 500 characters inclusive. Strings of length 9 or less SHALL be rejected; strings of length 501 or more SHALL be rejected.

**Validates: Requirements 2.1**

---

## Error Handling

### Validation Errors

Laravel's `StoreStudentRequest` handles the redirect automatically on failure: it redirects back to the previous URL (`GET /students/create`) with:
- `$errors` bag containing per-field messages (accessible in Blade via `@error`)
- `old()` input for all non-file fields (profile picture is never restored per requirement 2.4/2.6)

The Blade form template uses `@error('field_name')` directives to display messages beneath each input and `old('field_name')` to repopulate text inputs.

### File Storage Failure

If `Storage::putFile()` throws an exception after validation passes:
- The controller catches the exception.
- The student record is still saved with `profile_picture` set to `null`.
- A field-level notice is added to the session (separate from the success flash) and rendered on the profile view.
- This fulfills Requirement 3.8: the student record is created even if the file operation fails.

### Database Failure

If `Student::create()` throws a `QueryException`:
- The controller catches `QueryException` specifically.
- For duplicate key violations (`23000` SQL state), it redirects back with a field-level error identifying `student_id` or `email` as the duplicate.
- For all other database errors, it redirects back with a general error flash message.
- Old input is preserved via `withInput()`.

### 404 Handling

Route-model binding on `{student}` automatically triggers Laravel's 404 handler when no matching record exists (Requirement 6.4). No custom code is needed; Laravel returns a 404 response with its default error view (or a custom `404.blade.php` if defined).

### Missing Profile Picture on Profile View

The `students.show` Blade view checks whether the stored path resolves to an accessible file:

```blade
@if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture))
    <img src="{{ asset('storage/' . $student->profile_picture) }}" alt="Profile Picture">
@else
    <img src="{{ asset('images/placeholder.png') }}" alt="No profile picture">
@endif
```

---

## Testing Strategy

### Overview

This feature is primarily a form-handling CRUD application. The core testable logic is the **validation layer** (pure input → pass/fail mapping), which is well-suited to property-based testing. All other layers (routing, file upload, database persistence, Blade rendering) are best covered with example-based unit tests and feature integration tests.

Property-based testing will **not** be applied to:
- Blade rendering and layout (use snapshot/example tests)
- File system operations (use feature tests with fakes)
- Database persistence integration (use feature tests with in-memory SQLite)

### Property-Based Tests

**Library**: [PHPUnit](https://phpunit.de/) with [Eris](https://github.com/giorgiosironi/eris) (PHP property-based testing library) or equivalently [php-quickcheck](https://github.com/steos/php-quickcheck). Each property test MUST run a minimum of **100 iterations**.

Each test is tagged with a comment referencing the design property it validates:

```
// Feature: student-registration-system, Property N: <property text>
```

| Test | Property | What Varies |
|------|----------|-------------|
| Valid submissions pass | Property 1 | All fields within bounds |
| Invalid fields are rejected | Property 2 | Each field violated in isolation and combination |
| Duplicate student_id/email rejected | Property 3 | Randomly generated IDs/emails, submitted twice |
| Mobile number digit-length boundary | Property 4 | String length (0–20), character composition |
| Age-at-submission invariant | Property 5 | Date of birth relative to today |
| Profile picture path is a string path | Property 6 | Valid image files of varying names/sizes |
| Year level boundary | Property 7 | Integer values from -5 to 10 |
| Address length boundary | Property 8 | Strings from 0 to 600 characters |

### Unit / Feature Tests (PHPUnit)

| Test | Type | Covers |
|------|------|--------|
| `GET /students/create` returns 200 | Feature | Req 1.1 |
| Form includes all required fields | Feature | Req 1.3 |
| Valid form submission creates record and redirects | Feature | Req 4.1, 4.2 |
| Successful redirect shows success flash banner | Feature | Req 5.1 |
| Success banner not shown on page refresh | Feature | Req 5.3 |
| Duplicate student_id returns field error | Feature | Req 4.3 |
| Duplicate email returns field error | Feature | Req 4.3 |
| Missing required field returns validation error | Feature | Req 2.2 |
| Old input restored on validation failure | Feature | Req 2.4 |
| File input cleared on validation failure notice | Feature | Req 2.6 |
| Profile picture stored in public disk | Feature | Req 3.1 |
| Oversized file rejected with field error | Feature | Req 3.7 |
| Non-image file rejected | Feature | Req 3.3, 3.7 |
| File storage failure saves record with null picture | Feature | Req 3.8 |
| `GET /students/{id}` with valid ID shows profile | Feature | Req 6.1 |
| Profile view shows all student fields | Feature | Req 6.2 |
| Null middle name shows "N/A" | Feature | Req 6.5 |
| Missing profile picture shows placeholder | Feature | Req 6.6 |
| `GET /students/{id}` with invalid ID returns 404 | Feature | Req 6.4 |
| Undefined route returns 404 | Feature | Req 7.5 |

### Test Infrastructure Notes

- Use `Storage::fake('public')` in feature tests to avoid real filesystem writes.
- Use `RefreshDatabase` trait to isolate each test in a transaction.
- Use `UploadedFile::fake()->image('photo.jpg', 100, 100)` for valid image uploads.
- Run all property-based tests in isolation from integration tests to avoid DB state contamination between random iterations.
