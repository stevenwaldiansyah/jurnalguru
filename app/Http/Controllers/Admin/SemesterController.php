<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SemesterController extends Controller
{
    public function index(): View
    {
        $semesters = Semester::orderByDesc('academic_year')->paginate(15);

        return view('admin.semesters.index', compact('semesters'));
    }

    public function create(): View
    {
        return view('admin.semesters.form', ['semester' => new Semester]);
    }

    public function store(Request $request): RedirectResponse
    {
        Semester::create($request->validate([
            'name' => ['required', 'string', 'max:50'],
            'academic_year' => ['required', 'string', 'max:20'],
        ]));

        return redirect()->route('admin.semesters.index')->with('success', 'Semester berhasil ditambahkan.');
    }

    public function edit(Semester $semester): View
    {
        return view('admin.semesters.form', compact('semester'));
    }

    public function update(Request $request, Semester $semester): RedirectResponse
    {
        $semester->update($request->validate([
            'name' => ['required', 'string', 'max:50'],
            'academic_year' => ['required', 'string', 'max:20'],
        ]));

        return redirect()->route('admin.semesters.index')->with('success', 'Semester berhasil diperbarui.');
    }

    public function destroy(Semester $semester): RedirectResponse
    {
        if ($semester->schedules()->exists()) {
            return back()->with('error', 'Semester masih digunakan jadwal.');
        }
        $semester->delete();

        return redirect()->route('admin.semesters.index')->with('success', 'Semester berhasil dihapus.');
    }
}
