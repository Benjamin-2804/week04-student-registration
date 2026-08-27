# Requirements Document

## Introduction

The College of Information Technology requires a digital Student Registration System to replace its current paper-based process. The system will be built with Laravel and will allow college staff to register students, upload profile pictures, validate submitted information, and view registered student profiles through a responsive web interface. This document defines the functional, validation, technical, and UI requirements for the system.

---

## Glossary

- **System**: The Laravel-based Student Registration System web application.
- **Student**: A person being registered in the College of Information Technology.
- **Student_ID**: A unique alphanumeric identifier assigned to a student.
- **Registration_Form**: The Blade-rendered HTML form used to collect and submit student information.
- **StudentController**: The Laravel controller responsible for handling registration form display, data submission, storage, and profile retrieval.
- **Form_Request**: A Laravel Form Request class encapsulating validation rules for student registration input.
- **Profile_Picture**: An image file (JPG, JPEG, or PNG, max 2048 KB) uploaded during student registration.
- **Student_Profile**: The view page displaying a registered student's full information including the profile picture.
- **Flash_Message**: A one-time session message displayed after a form submission to indicate success or failure.
- **Storage_Link**: The symbolic link created via `php artisan storage:link` that makes uploaded files publicly accessible.
- **Validator**: The Laravel validation layer (Form_Request or controller-inline) that enforces field rules before persisting data.
- **Database**: The relational database managed via Laravel migrations that stores student records.

---

## Requirements

### Requirement 1: Student Registration Form Display

**User Story:** As a college staff member, I want to open a registration form, so that I can enter and submit student information.

#### Acceptance Criteria

1. WHEN a user navigates to the registration route (`GET /students/create`), THE System SHALL render the Registration_Form using a Blade template and return an HTTP 200 response.
2. IF the Registration_Form fails to render due to a template error or server issue, THEN THE System SHALL display a standard Laravel error page with an appropriate HTTP error status code.
3. THE Registration_Form SHALL include all required input fields: Student_ID (text), First Name (text, max 100 characters), Middle Name (text, optional), Last Name (text, max 100 characters), Email Address (email), Mobile Number (text), Date of Birth (date), Gender (select or radio), Program (select or text), Year Level (select or text), Address (textarea), and Profile_Picture (file). Required fields SHALL be visually marked as required; Middle Name SHALL be visually marked as optional.
4. THE Registration_Form SHALL visually group related fields into at least the following sections: Personal Information (Student_ID, First Name, Middle Name, Last Name, Date of Birth, Gender), Contact Information (Email Address, Mobile Number, Address), Academic Information (Program, Year Level), and Profile Picture — each group clearly labeled with a visible heading.
5. THE Registration_Form SHALL be responsive and render correctly on desktop (≥1024px), tablet (768px–1023px), and mobile (≤767px) viewports using Tailwind CSS utility classes.
6. THE Registration_Form SHALL include a submit button labeled "Register Student" that triggers the POST submission to the registration store route.

---

### Requirement 2: Student Registration Input Validation

**User Story:** As a college staff member, I want the system to validate my input before saving, so that only complete and correct student records are stored.

#### Acceptance Criteria

1. WHEN a registration form is submitted, THE Validator SHALL enforce the following rules:
   - `student_id`: required; must not already exist in the student records
   - `first_name`: required; must be text only; no longer than 100 characters
   - `last_name`: required; must be text only; no longer than 100 characters
   - `email`: required; must be a well-formed email address; must not already exist in the student records
   - `mobile_number`: required; must contain digits only; must be between 10 and 15 digits in length
   - `date_of_birth`: required; must be a valid calendar date; must represent a date in the past; the student must be at least 15 years old at the time of submission
   - `gender`: required; must be one of the offered gender options (e.g., male, female, or other)
   - `program`: required; must be text only; no longer than 100 characters
   - `year_level`: required; must be a whole number between 1 and 6 inclusive
   - `address`: required; must be no shorter than 10 characters and no longer than 500 characters
   - `profile_picture`: required; must be an image file; must be one of the accepted formats (jpg, jpeg, png); must not exceed 2048 KB in size
2. IF any required field is missing or fails its validation rule, THEN THE System SHALL reject the submission and return a field-specific error message for every failing field without proceeding to save any data.
3. WHERE validation errors are returned, THE Registration_Form SHALL display each error message directly beneath its corresponding input field so that the user can identify and correct each specific error.
4. WHEN validation fails, THE System SHALL re-render the Registration_Form with all previously submitted non-file field values restored, so the user does not need to re-enter correct data. File input fields SHALL NOT be pre-populated, and the user must re-select a file before resubmitting.
5. THE Registration_Form SHALL permit the user to correct errors and resubmit as many times as needed, with validation re-evaluated on each submission attempt.
6. WHEN a submission fails validation, THE Profile_Picture file input SHALL be cleared, and THE System SHALL display a notice informing the user that the profile picture must be re-selected before resubmitting.

---

### Requirement 3: Profile Picture Upload and Storage

**User Story:** As a college staff member, I want to upload a student's profile picture during registration, so that the student record includes a visual identifier.

#### Acceptance Criteria

1. WHEN a valid registration form is submitted and a Profile_Picture file is provided, THE System SHALL store the uploaded file inside the `storage/app/public` directory.
2. THE System SHALL accept Profile_Picture files with a maximum size of 2048 KB inclusive.
3. THE System SHALL accept only files that are image type with formats: jpg, jpeg, or png. Files of any other type SHALL be rejected.
4. THE System SHALL store each Profile_Picture under a name that is unique across all stored profile pictures, so that no two stored files share the same filename.
5. THE System SHALL save only the relative file path of the stored Profile_Picture to the student record; the binary file content SHALL NOT be stored in the database.
6. WHEN a request is made for a stored profile picture, THE System SHALL serve the file through a publicly accessible URL made available via the symbolic link created by `php artisan storage:link`.
7. IF the uploaded file exceeds 2048 KB or is not one of the accepted image formats, THEN THE System SHALL reject the submission and return a field-level error for the `profile_picture` field that identifies which rule was violated (size or format).
8. IF the Profile_Picture file storage operation fails after validation passes, THEN THE System SHALL save the remaining student record fields to the database and display a field-level notice on the resulting page informing the user that the profile picture was not saved. The student record SHALL still be created.
9. IF no Profile_Picture file is included in the submission, THEN THE Validator SHALL treat the absence as a validation failure and return an error message for the `profile_picture` field indicating the field is required.

---

### Requirement 4: Persisting Student Records

**User Story:** As a college staff member, I want successfully validated student data to be saved to the database, so that the registration is permanently recorded.

#### Acceptance Criteria

1. WHEN all validation rules pass, THE System SHALL save the submitted student data as a new record in the student records store.
2. WHEN a student record is successfully saved, THE System SHALL display a success notification to the user and redirect them to the Student_Profile view for the newly registered student.
3. IF saving the student record fails due to a duplicate Student_ID or duplicate email, THEN THE System SHALL return the user to the Registration_Form with an error message identifying which field contains the duplicate value, and all previously submitted non-file field values SHALL be restored.
4. IF saving the student record fails for any other reason (e.g., database unavailability), THEN THE System SHALL return the user to the Registration_Form with a general error message indicating the registration could not be completed, and all previously submitted non-file field values SHALL be restored.
5. THE student records store SHALL support the following fields for each record: a unique system-generated identifier, Student_ID (unique), First Name, Middle Name (optional/nullable), Last Name, Email (unique), Mobile Number, Gender, Date of Birth, Program, Year Level, Address, Profile Picture path, and timestamps for record creation and last update.
6. THE System SHALL enforce uniqueness on both the Student_ID and Email fields in the student records store, so that no two records can share the same Student_ID or the same Email Address.

---

### Requirement 5: Flash Notifications

**User Story:** As a college staff member, I want to see a clear success or failure notification after submitting the registration form, so that I know whether the registration was completed.

#### Acceptance Criteria

1. WHEN a student record is successfully saved, THE System SHALL store a success notification message in the session and display it as a notification banner on the Student_Profile page after the redirect.
2. IF submission fails due to validation errors, THEN THE System SHALL display an inline error message adjacent to each field that contains a validation error on the Registration_Form without redirecting the user.
3. THE success notification banner SHALL be displayed exactly once on the Student_Profile page and SHALL NOT appear if the user navigates away and returns, or refreshes the page.
4. IF a system-level error occurs during the store operation (e.g., a database failure), THEN THE System SHALL store an error notification message in the session and display it as a notification banner when the user is returned to the Registration_Form.
5. WHEN a notification banner is displayed, THE System SHALL render it in a color that indicates the message type: a success banner SHALL use a green color scheme and an error banner SHALL use a red color scheme.

---

### Requirement 6: View Registered Student Profile

**User Story:** As a college staff member, I want to view a registered student's profile after registration, so that I can confirm the stored information is correct.

#### Acceptance Criteria

1. WHEN a user navigates to the student profile route with a valid student record identifier, THE System SHALL retrieve the corresponding student record and render it in the Student_Profile Blade view.
2. THE Student_Profile view SHALL display all stored student fields: Student_ID, First Name, Middle Name, Last Name, Email Address, Mobile Number, Date of Birth, Gender, Program, Year Level, Address, and the Profile_Picture image.
3. WHEN the Student_Profile view is rendered and the student has a stored profile picture, THE System SHALL display the profile picture image using its publicly accessible storage URL.
4. IF a requested student record does not exist, THEN THE System SHALL return an HTTP 404 response and display a page that informs the user that the requested student record was not found.
5. IF the Middle Name field is null or empty for a student record, THEN THE Student_Profile view SHALL display "N/A" in place of the Middle Name.
6. IF the stored profile picture file is inaccessible or missing from storage, THEN THE Student_Profile view SHALL display a default placeholder image in place of the missing profile picture.

---

### Requirement 7: Routing and Controller Structure

**User Story:** As a developer, I want well-defined routes and a structured controller, so that the application is maintainable and follows Laravel conventions.

#### Acceptance Criteria

1. THE System SHALL define the following routes:
   - `GET /students/create` → `StudentController@create` (display registration form)
   - `POST /students` → `StudentController@store` (process and save registration)
   - `GET /students/{student}` → `StudentController@show` (display student profile)
   - `GET /students` → `StudentController@index` (list registered students)
2. THE StudentController SHALL implement exactly the following public methods: `index()`, `create()`, `store()`, and `show()`.
3. WHEN a registration form submission is received by the `store()` method, THE System SHALL validate the input using the Form_Request class before any persistence logic executes, observable by the fact that invalid submissions are rejected before any database write occurs.
4. IF validation fails during a `store()` request, THE System SHALL redirect the user back to `GET /students/create` with field-level error messages and all previously submitted non-file field values available for re-display.
5. IF a route is accessed that does not match any defined route, THEN THE System SHALL return an HTTP 404 response.

---
