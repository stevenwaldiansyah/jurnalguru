<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(): View
    {
        $classes = SchoolClass::withCount('students')->orderBy('name')->paginate(15);

        return view('admin.classes.index', compact('classes'));
    }

    public function create(): View
    {
        return view('admin.classes.form', ['class' => new SchoolClass]);
    }

    public function store(Request $request): RedirectResponse
    {
        SchoolClass::create($request->validate([
            'name' => ['required', 'string', 'max:50'],
            'major' => ['required', 'string', 'max:50'],
        ]));

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(SchoolClass $schoolClass): View
    {
        return view('admin.classes.form', ['class' => $schoolClass]);
    }

    public function update(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $schoolClass->update($request->validate([
            'name' => ['required', 'string', 'max:50'],
            'major' => ['required', 'string', 'max:50'],
        ]));

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $schoolClass): RedirectResponse
    {
        if ($schoolClass->students()->exists() || $schoolClass->schedules()->exists()) {
            return back()->with('error', 'Kelas masih digunakan oleh siswa atau jadwal.');
        }
        $schoolClass->delete();

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dihapus.');
    }
}
