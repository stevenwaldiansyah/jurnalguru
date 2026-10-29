<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::orderBy('name')->paginate(15);

        return view('admin.subjects.index', compact('subjects'));
    }

    public function create(): View
    {
        return view('admin.subjects.form', ['subject' => new Subject]);
    }

    public function store(Request $request): RedirectResponse
    {
        Subject::create($request->validate(['name' => ['required', 'string', 'max:100']]));

        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function edit(Subject $subject): View
    {
        return view('admin.subjects.form', compact('subject'));
    }

    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validate(['name' => ['required', 'string', 'max:100']]));

        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        if ($subject->schedules()->exists()) {
            return back()->with('error', 'Mata pelajaran masih digunakan jadwal.');
        }
        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
