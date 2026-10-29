<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $students = Student::query()
            ->with('schoolClass')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('nis', 'like', "%{$s}%"))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        return view('admin.students.form', [
            'student' => new Student,
            'classes' => SchoolClass::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Student::create($this->validated($request));

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Student $student): View
    {
        return view('admin.students.form', [
            'student' => $student,
            'classes' => SchoolClass::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->update($this->validated($request, $student));

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        if ($student->attendances()->exists() || $student->scores()->exists()) {
            return back()->with('error', 'Siswa masih memiliki data absensi atau nilai.');
        }
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Student $student = null): array
    {
        return $request->validate([
            'nis' => ['required', 'string', 'max:20', Rule::unique('students', 'nis')->ignore($student?->id)],
            'name' => ['required', 'string', 'max:100'],
            'class_id' => ['required', 'exists:classes,id'],
        ]);
    }
}
