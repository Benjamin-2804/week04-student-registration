<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::paginate(15);

        return view('students.index', compact('students'));
    }

    public function create(): View
    {
        return view('students.create');
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Store the profile picture if provided
        $picturePath = null;
        $pictureStorageFailed = false;

        if ($request->hasFile('profile_picture')) {
            try {
                $picturePath = Storage::disk('public')->putFile(
                    'profile_pictures',
                    $request->file('profile_picture')
                );
            } catch (\Throwable) {
                $pictureStorageFailed = true;
            }
        }

        // Persist the student record
        try {
            $student = Student::create(array_merge(
                $validated,
                ['profile_picture' => $picturePath]
            ));
        } catch (QueryException $e) {
            // Duplicate key violation
            if ($e->getCode() === '23000') {
                $duplicateField = $this->detectDuplicateField($e->getMessage());

                return redirect()->back()
                    ->withInput()
                    ->withErrors([$duplicateField => 'This '.$duplicateField.' is already registered.']);
            }

            // Other DB error
            return redirect()->back()
                ->withInput()
                ->with('error', 'Registration could not be completed due to a database error. Please try again.');
        }

        if ($pictureStorageFailed) {
            session()->flash('picture_notice', 'The student was registered, but the profile picture could not be saved.');
        }

        return redirect()
            ->route('students.show', $student)
            ->with('success', 'Student registered successfully!');
    }

    public function show(Student $student): View
    {
        return view('students.show', compact('student'));
    }

    /**
     * Detect which unique field caused the duplicate key violation from the error message.
     */
    private function detectDuplicateField(string $message): string
    {
        if (str_contains(strtolower($message), 'student_id')) {
            return 'student_id';
        }

        if (str_contains(strtolower($message), 'email')) {
            return 'email';
        }

        return 'student_id';
    }
}
