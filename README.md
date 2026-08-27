# Student Registration System

**ITST 302 – Client-Server Technologies | Week 4 Laboratory Activity**

A Laravel 12-based **Student Registration System** developed using Laravel, MySQL, and Tailwind CSS. The project demonstrates form processing, server-side validation, database integration, profile-picture uploading, flash messages, and student profile management.

---

# Table of Contents

1. [Project Introduction](#1-project-introduction)
2. [Learning Objectives](#2-learning-objectives)
3. [Laravel Request Lifecycle](#3-laravel-request-lifecycle)
4. [Validation Rules and Their Purpose](#4-validation-rules-and-their-purpose)
5. [Database Design](#5-database-design)
6. [Student Registration Flowchart](#6-student-registration-flowchart)
7. [Project Screenshots](#7-project-screenshots)
8. [Problems Encountered](#8-problems-encountered)
9. [Solutions Implemented](#9-solutions-implemented)
10. [Reflection](#10-reflection)
11. [References](#11-references)

---

# 1. Project Introduction

## Project Overview

The **Student Registration System** is a Laravel-based web application designed to demonstrate how student information can be collected, checked, stored, and displayed through a digital registration process.

The application was developed using **Laravel 12**, **MySQL**, and **Tailwind CSS**. It allows students to enter their personal and academic information and upload a profile picture during registration.

After the submitted information passes Laravel's validation process, the system stores the student's information in the MySQL database and redirects the user to a student profile page.

The project demonstrates several important web-development concepts, including:

* Laravel routing
* Blade templates
* Controllers
* Server-side validation
* Database migrations
* MySQL integration
* File uploads
* Laravel Storage
* Flash messages
* HTTP request and response handling

## Purpose of a Student Registration System

A Student Registration System provides an organized digital method for collecting and managing student information.

Instead of relying entirely on handwritten forms or manual data entry, students can provide their information through a web-based registration form.

The system collects information such as:

* Student ID
* First name
* Middle name
* Last name
* Email address
* Mobile number
* Date of birth
* Gender
* Program
* Year level
* Address
* Profile picture

Digitizing the registration process can make information easier to organize, retrieve, and maintain.

## Importance of Data Validation

Data validation is an important part of the system because information submitted by users cannot automatically be considered correct or safe.

Laravel checks the submitted values before they are stored in the database. For example, the application verifies that required fields are completed, email addresses follow an acceptable format, student IDs and email addresses are unique, and profile pictures use approved file types.

Validation helps prevent:

* Incomplete records
* Duplicate student information
* Invalid email addresses
* Incorrect data formats
* Oversized file uploads
* Potentially unsafe file submissions

## Role of Registration Systems in Enterprise Applications

Registration systems are commonly used as the starting point for many enterprise applications.

Universities can use them for student records, businesses can use them for customer accounts, organizations can use them for membership systems, and companies can use similar processes for employee registration.

The basic workflow remains similar:

```text
User Input
    ↓
Validation
    ↓
Processing
    ↓
Database Storage
    ↓
System Response
```

Understanding this process provides a foundation for developing larger enterprise applications.

---

# 2. Learning Objectives

After completing this laboratory activity, the following objectives were accomplished:

1. Create a student registration form using Laravel Blade.
2. Configure Laravel routes for handling registration requests.
3. Process submitted information using a Laravel controller.
4. Apply server-side validation rules.
5. Prevent duplicate student IDs and email addresses.
6. Validate profile-picture uploads.
7. Store uploaded files using Laravel Storage.
8. Connect Laravel to a MySQL database.
9. Create a database table using Laravel migrations.
10. Display validation errors through Blade.
11. Implement session-based flash messages.
12. Display registered student information on a profile page.
13. Organize the Laravel project using the MVC architecture.
14. Document the project using Markdown.
15. Use Git and GitHub for version control and project presentation.

---

# 3. Laravel Request Lifecycle

When a student submits the registration form, the request passes through several Laravel components before a response is returned.

## Request Flow Diagram

```text
┌────────────────────┐
│      Browser       │
│                    │
│ Student fills out  │
│ registration form  │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│       Route        │
│                    │
│ routes/web.php     │
│ POST /register     │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│     Controller     │
│                    │
│ StudentController  │
│       ::store()    │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│     Validation     │
│                    │
│ Laravel checks     │
│ submitted data     │
└─────────┬──────────┘
          │
     ┌────┴─────┐
     │          │
   VALID      INVALID
     │          │
     │          ▼
     │   ┌───────────────┐
     │   │ Return to     │
     │   │ Registration  │
     │   │ Form + Errors │
     │   └───────────────┘
     │
     ▼
┌────────────────────┐
│       Model        │
│                    │
│    Student.php     │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│      Database      │
│                    │
│ MySQL students     │
│      table         │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│      Response      │
│                    │
│ Flash Message +    │
│ Profile Redirect   │
└────────────────────┘
```

## Browser

The process starts when the student opens the registration page.

The browser displays the Laravel Blade registration form where the student enters their personal and academic information and selects a profile picture.

When the student clicks the registration button, the browser sends the completed form to Laravel through an HTTP POST request.

## Route

Laravel receives the request and checks the route configuration in `routes/web.php`.

The POST request for registration is connected to the controller method responsible for storing the student's information.

```text
POST /register
        ↓
StudentController@store
```

The route determines where the registration request should be processed.

## Controller

The `StudentController::store()` method handles the submitted registration request.

The controller coordinates several operations, including:

* Receiving the request
* Validating the submitted data
* Processing the profile picture
* Creating the student record
* Setting a success message
* Redirecting the user

## Validation

Laravel checks the submitted information against the application's validation rules.

The system verifies:

* Required fields
* Unique student ID
* Unique email
* Valid email format
* Numeric mobile number
* Valid date
* Accepted gender values
* Valid image format
* Maximum image size

If the information does not pass validation, Laravel sends the user back to the registration form and displays the appropriate errors.

## Model

The `Student` model represents the `students` database table.

It provides Laravel with a structured way to interact with student records and separates database-related operations from the presentation layer.

## Database

After validation succeeds, the student's information is stored in the MySQL `students` table.

The database contains both personal and academic information, along with the location of the uploaded profile picture.

## Response

After successful registration, Laravel creates a flash message and redirects the student to their profile page.

The profile page then displays the student's stored information and uploaded profile picture.

## Laravel Components Used

| Component         | File / Location                                     | Purpose                                 |
| ----------------- | --------------------------------------------------- | --------------------------------------- |
| Route             | `routes/web.php`                                    | Connects requests to controller actions |
| Controller        | `app/Http/Controllers/StudentController.php`        | Handles registration processing         |
| Model             | `app/Models/Student.php`                            | Represents the students table           |
| Registration View | `resources/views/students/create.blade.php`         | Displays the registration form          |
| Profile View      | `resources/views/students/show.blade.php`           | Displays student information            |
| Layout            | `resources/views/layouts/app.blade.php`             | Provides the common application layout  |
| Migration         | `database/migrations/..._create_students_table.php` | Defines the database structure          |

---

# 4. Validation Rules and Their Purpose

Validation ensures that the information submitted by a student follows the requirements of the application.

| Field           | Validation Rules                                      | Purpose                                                          |
| --------------- | ----------------------------------------------------- | ---------------------------------------------------------------- |
| Student ID      | `required`, `unique:students`                         | Requires an ID and prevents duplicate student records.           |
| First Name      | `required`, `string`, `max:100`                       | Ensures the first name is provided and has a reasonable length.  |
| Middle Name     | `nullable`, `string`, `max:100`                       | Allows the student to leave the middle name blank.               |
| Last Name       | `required`, `string`, `max:100`                       | Requires the student's last name.                                |
| Email           | `required`, `email`, `unique:students`                | Checks the email format and prevents duplicate email addresses.  |
| Mobile Number   | `required`, `numeric`, `digits_between:10,15`         | Ensures the value contains numbers and has an acceptable length. |
| Date of Birth   | `required`, `date`                                    | Ensures a valid date is submitted.                               |
| Gender          | `required`, `in:Male,Female,Other`                    | Limits the value to the accepted gender options.                 |
| Program         | `required`, `string`, `max:100`                       | Ensures the student's program is recorded correctly.             |
| Year Level      | `required`, `string`, `max:20`                        | Records the student's current academic level.                    |
| Address         | `required`, `string`, `max:500`                       | Requires an address while limiting excessive input.              |
| Profile Picture | `required`, `image`, `mimes:jpeg,jpg,png`, `max:2048` | Allows only approved image types and limits the file to 2 MB.    |

## Required Fields

The `required` rule prevents important information from being submitted as empty values.

This helps ensure that each student record contains the minimum information necessary for registration.

## Unique Constraints

The `unique` validation rule is used for the student ID and email address.

This prevents multiple students from accidentally being registered using the same identifier or email address.

## Email Validation

The `email` rule checks whether the submitted value follows a valid email structure.

This is important because email addresses may be used for communication and account identification.

## Numeric Validation

The mobile number uses the `numeric` rule and a digit-length restriction.

This prevents alphabetic characters and helps ensure that the number follows an acceptable length.

## Image Validation

The profile-picture field uses Laravel's `image` and `mimes` rules.

The application only accepts:

* JPEG
* JPG
* PNG

Restricting the allowed formats helps reduce the risk of unwanted file types being uploaded.

## File Size Restriction

The `max:2048` rule restricts the profile picture to a maximum size of approximately **2 MB**.

This helps prevent users from consuming excessive server storage.

---

# 5. Database Design

## Entity Relationship Diagram

The registration system uses the `students` table as its primary database entity.

```text
┌──────────────────────────────────────────────────────┐
│                      STUDENTS                        │
├──────────────────────────────────────────────────────┤
│ PK  id                 BIGINT                        │
│     student_id         VARCHAR(255) UNIQUE          │
│     first_name         VARCHAR(100)                 │
│     middle_name        VARCHAR(100) NULL            │
│     last_name          VARCHAR(100)                 │
│     email              VARCHAR(255) UNIQUE          │
│     mobile_number      VARCHAR(15)                  │
│     date_of_birth      DATE                         │
│     gender             VARCHAR(10)                  │
│     program            VARCHAR(100)                 │
│     year_level         VARCHAR(20)                  │
│     address            TEXT                         │
│     profile_picture    VARCHAR(255) NULL            │
│     created_at         TIMESTAMP                    │
│     updated_at         TIMESTAMP                    │
└──────────────────────────────────────────────────────┘
```

## Table Structure

| Column            | Data Type    | Constraint                  |
| ----------------- | ------------ | --------------------------- |
| `id`              | BIGINT       | Primary Key, Auto Increment |
| `student_id`      | VARCHAR(255) | Unique, Not Null            |
| `first_name`      | VARCHAR(100) | Not Null                    |
| `middle_name`     | VARCHAR(100) | Nullable                    |
| `last_name`       | VARCHAR(100) | Not Null                    |
| `email`           | VARCHAR(255) | Unique, Not Null            |
| `mobile_number`   | VARCHAR(15)  | Not Null                    |
| `date_of_birth`   | DATE         | Not Null                    |
| `gender`          | VARCHAR(10)  | Not Null                    |
| `program`         | VARCHAR(100) | Not Null                    |
| `year_level`      | VARCHAR(20)  | Not Null                    |
| `address`         | TEXT         | Not Null                    |
| `profile_picture` | VARCHAR(255) | Nullable                    |
| `created_at`      | TIMESTAMP    | Automatic                   |
| `updated_at`      | TIMESTAMP    | Automatic                   |

## Primary Key

The `id` field is the primary key of the table.

Laravel automatically generates this value using an auto-incrementing BIGINT.

## Unique Constraints

The following columns have unique constraints:

```text
student_id
email
```

This prevents duplicate student identifiers and duplicate email addresses.

## Data Types

Different data types are used according to the kind of information being stored.

For example:

* `BIGINT` is used for the primary key.
* `VARCHAR` is used for names and short text.
* `DATE` is used for the date of birth.
* `TEXT` is used for the address.
* `TIMESTAMP` is used for creation and update dates.

## Nullable Fields

The `middle_name` and `profile_picture` columns are nullable.

This allows these fields to remain empty when no value is available.

## Laravel Migration

The database table is created through a Laravel migration:

```php
Schema::create('students', function (Blueprint $table) {
    $table->id();

    $table->string('student_id')->unique();
    $table->string('first_name', 100);
    $table->string('middle_name', 100)->nullable();
    $table->string('last_name', 100);
    $table->string('email')->unique();

    $table->string('mobile_number', 15);
    $table->date('date_of_birth');
    $table->string('gender', 10);
    $table->string('program', 100);
    $table->string('year_level', 20);

    $table->text('address');
    $table->string('profile_picture', 255)->nullable();

    $table->timestamps();
});
```

Laravel migrations allow the database structure to be maintained together with the application's source code.

---

# 6. Student Registration Flowchart

The complete registration process can be represented using the following flowchart:

```text
                 ┌───────────────────────┐
                 │ Open Registration Page │
                 │      GET /register    │
                 └───────────┬───────────┘
                             │
                             ▼
                 ┌───────────────────────┐
                 │    Complete Form      │
                 │ Personal + Academic   │
                 │ Information + Image   │
                 └───────────┬───────────┘
                             │
                             ▼
                 ┌───────────────────────┐
                 │ Submit Registration   │
                 │      POST /register   │
                 └───────────┬───────────┘
                             │
                             ▼
                 ┌───────────────────────┐
                 │ Laravel Validation    │
                 │ Check Submitted Data  │
                 └───────────┬───────────┘
                             │
                      ┌──────┴──────┐
                      │             │
                    VALID        INVALID
                      │             │
                      │             ▼
                      │    ┌──────────────────┐
                      │    │ Return to Form   │
                      │    │ Display Errors   │
                      │    └──────────────────┘
                      │
                      ▼
             ┌────────────────────────┐
             │ Process Profile Picture│
             │      Upload            │
             └────────────┬───────────┘
                          │
                          ▼
             ┌────────────────────────┐
             │ Save Student Information│
             │        to MySQL         │
             └────────────┬───────────┘
                          │
                          ▼
             ┌────────────────────────┐
             │   Create Flash Message │
             │ Registration Successful│
             └────────────┬───────────┘
                          │
                          ▼
             ┌────────────────────────┐
             │ Redirect to Student    │
             │      Profile Page      │
             └────────────────────────┘
```

The flowchart can also be recreated as a visual diagram using:

* Draw.io
* Lucidchart
* Figma
* Canva
* Microsoft Visio

---

# 7. Project Screenshots

|  # | Screenshot                          | Description                                               |
| -: | ----------------------------------- | --------------------------------------------------------- |
|  1 | Registration Form                   | Shows the completed student registration interface.       |
|  2 | Validation Errors                   | Demonstrates Laravel validation messages.                 |
|  3 | Successful Registration & Flash Msg | Shows the result of submitting valid information.         |
|  4 | Student Profile Page                | Displays the student's complete registration information. |
|  5 | MySQL Database                      | Shows the registered student record in MySQL Workbench.   |
|  6 | Project Structure 1                 | Shows the Laravel project's folders and files (part 1).   |
|  7 | Project Structure 2                 | Shows the Laravel project's folders and files (part 2).   |

### Registration Form
![Registration Form](screenshots/Registration%20Form.png)

### Validation Errors
![Validation Errors](screenshots/Validation%20Errors.png)

### Successful Registration & Flash Message
![Successful Registration & Flash Message](screenshots/Successful%20Registration%20%26%20Flash%20Message.png)

### Student Profile Page
![Student Profile Page](screenshots/Student%20Profile%20Page.png)

### MySQL Database
![MySQL Database](screenshots/MySQL%20Database.png)

### Project Structure 1
![Project Structure 1](screenshots/Project%20Structure%201.png)

### Project Structure 2
![Project Structure 2](screenshots/Project%20Structure%202.png)

---

# 8. Problems Encountered

## Problem 1: Profile Picture Was Not Displaying

During development, uploaded profile pictures could be stored successfully but could not be accessed from the browser.

The problem occurred because the Laravel public storage link had not yet been generated.

## Problem 2: Validation Errors Were Not Visible

When invalid information was submitted, Laravel returned the user to the registration page, but the validation messages were not appearing correctly.

The Blade template needed the appropriate `@error` directives to display Laravel's generated messages.

## Problem 3: CSRF Token Expired During Testing

While testing the registration request using `curl`, the application returned a **419 Page Expired** error.

The issue occurred because the session and CSRF token were not being maintained correctly between separate requests.

---

# 9. Solutions Implemented

## Solution 1: Create the Storage Link

The Laravel storage link was generated using:

```bash
php artisan storage:link
```

This connects Laravel's public storage directory with:

```text
storage/app/public
```

After creating the link, uploaded profile pictures could be accessed through the application's public storage path.

## Solution 2: Display Validation Messages

The Blade registration form was updated to display errors using Laravel's `@error` directive.

Example:

```blade
@error('field_name')
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
@enderror
```

This allows each validation message to appear beside the corresponding input field.

## Solution 3: Maintain the Testing Session

The CSRF problem during command-line testing was addressed by maintaining cookies between requests.

The `curl` testing process used:

```bash
-c cookies.txt -b cookies.txt
```

The cookie jar preserved the session information and allowed the CSRF token to remain associated with the correct session.

---

# 10. Reflection

Developing the Student Registration System gave me a better understanding of how important validation is when building a web application. At first, validation seemed like a simple process for checking whether required fields were empty. However, while developing the project, I realized that validation has a much larger role in maintaining data quality, preventing duplicate records, and protecting an application from inappropriate input.

One of the main lessons I learned was that every type of user input needs to be handled according to its purpose. For example, a student ID needs to be unique because it identifies a specific student. The email address also needs to be unique because using the same email for multiple records could create confusion. Other fields require different rules. A mobile number should contain an appropriate number of digits, a date of birth should contain a valid date, and the profile picture should only accept approved image formats.

The project also taught me that user input should never automatically be trusted. Information comes directly from users, and users can accidentally enter incorrect values or intentionally submit unexpected information. Because of this, validation must take place on the server before information is saved to the database. Laravel makes this process convenient by providing validation rules that can be applied directly to the submitted request.

Another important lesson was understanding the difference between client-side and server-side validation. Client-side validation can provide immediate feedback to users and make a form easier to use. However, client-side checks can be bypassed because they happen in the user's browser. Server-side validation is therefore essential because the server independently checks the submitted information before accepting it. This means that even if someone disables JavaScript or modifies the request, the Laravel application can still reject invalid information.

Handling the profile-picture upload also showed me why file security should be considered carefully. File uploads can become a security risk when applications accept unrestricted file types or very large files. In this project, the uploaded profile picture is checked using Laravel validation rules, including image type, accepted MIME formats, and file size. These restrictions reduce unnecessary storage usage and help prevent inappropriate files from being uploaded.

I also learned more about how the different parts of Laravel work together. The browser sends a request, the route determines where the request goes, the controller processes it, validation checks the information, the model interacts with the database, and Laravel eventually returns a response to the user. Understanding this lifecycle made the MVC architecture easier to understand because each component has a defined responsibility.

Finally, I realized that the registration process used in this project is similar to systems used in real organizations. Universities can use registration applications to manage student information, while businesses can use comparable systems for customers, employees, and members. Larger enterprise applications may contain more complex features, but the basic concept remains the same: collect information, validate it, process it, store it, and provide feedback to the user.

Overall, this activity improved my understanding of Laravel development and server-side programming. It taught me that creating a functional form is not enough. A reliable registration system must also validate information, protect uploaded files, maintain accurate database records, and communicate clearly with its users.

---

# 11. References

Laravel. (n.d.). *Validation*. Laravel Documentation.
https://laravel.com/docs/validation

Laravel. (n.d.). *File storage*. Laravel Documentation.
https://laravel.com/docs/filesystem

Laravel. (n.d.). *Database: Migrations*. Laravel Documentation.
https://laravel.com/docs/migrations

PHP Documentation Group. (n.d.). *PHP manual*. PHP.net.
https://www.php.net/manual/

Oracle. (n.d.). *MySQL 8.0 reference manual*. MySQL Documentation.
https://dev.mysql.com/doc/refman/8.0/en/

Tailwind Labs. (n.d.). *Tailwind CSS documentation*.
https://tailwindcss.com/docs

MDN Web Docs. (n.d.). *Forms*. Mozilla Developer Network.
https://developer.mozilla.org/en-US/docs/Learn/Forms

---

# Project Technology Stack

| Technology             | Purpose                          |
| ---------------------- | -------------------------------- |
| **Laravel 12**         | Backend web framework            |
| **PHP**                | Server-side programming language |
| **MySQL**              | Relational database              |
| **Blade**              | Laravel templating engine        |
| **Tailwind CSS**       | User interface styling           |
| **Laravel Storage**    | Profile-picture file management  |
| **Git**                | Version control                  |
| **GitHub**             | Repository and project hosting   |
| **Visual Studio Code** | Development environment          |

---

# Project Structure

```text
student-registration-system/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── StudentController.php
│   │
│   └── Models/
│       └── Student.php
│
├── database/
│   └── migrations/
│       └── create_students_table.php
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       │
│       └── students/
│           ├── create.blade.php
│           └── show.blade.php
│
├── routes/
│   └── web.php
│
├── storage/
│   └── app/
│       └── public/
│
├── screenshots/
│
├── .env
├── artisan
├── composer.json
└── README.md
```

# Conclusion

The Student Registration System demonstrates how Laravel can be used to create a structured, validated, and database-driven web application. By combining routing, controllers, validation, models, migrations, MySQL, file storage, and Blade views, the project demonstrates the fundamental workflow required for developing modern enterprise web applications.
