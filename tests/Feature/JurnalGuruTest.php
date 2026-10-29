<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Journal;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class JurnalGuruTest extends TestCase
{
    use RefreshDatabase;

    private function seedMinimal(User $teacher): Schedule
    {
        $semester = Semester::create(['name' => 'Ganjil', 'academic_year' => '2026/2027']);
        $class = SchoolClass::create(['name' => 'X TKJ 1', 'major' => 'TKJ']);
        $subject = Subject::create(['name' => 'Matematika']);
        Student::create(['nis' => '100', 'name' => 'Siswa A', 'class_id' => $class->id]);

        return Schedule::create([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $semester->id,
            'day' => 'Kamis',
            'start_time' => '07:00:00',
            'end_time' => '08:00:00',
        ]);
    }

    public function test_login_success_and_role_dashboard(): void
    {
        $user = User::factory()->create([
            'username' => 'guru1',
            'password' => Hash::make('secret1234'),
            'role' => UserRole::Guru,
        ]);

        $this->post('/login', ['username' => 'guru1', 'password' => 'secret1234'])
            ->assertRedirect(route('dashboard'));

        $this->actingAs($user)->get('/dashboard')->assertOk();
    }

    public function test_login_failure(): void
    {
        User::factory()->create(['username' => 'guru1', 'password' => Hash::make('secret1234')]);

        $this->post('/login', ['username' => 'guru1', 'password' => 'salah'])
            ->assertSessionHasErrors('username');
    }

    public function test_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_guru_cannot_view_other_teachers_journal(): void
    {
        $guruA = User::factory()->create(['role' => UserRole::Guru]);
        $guruB = User::factory()->create(['role' => UserRole::Guru]);
        $scheduleB = $this->seedMinimal($guruB);
        $journal = Journal::create([
            'schedule_id' => $scheduleB->id,
            'date' => now()->toDateString(),
            'topic' => 'Topik',
        ]);

        $this->actingAs($guruA)->get(route('journals.show', $journal))->assertForbidden();
    }

    public function test_duplicate_journal_rejected(): void
    {
        $guru = User::factory()->create(['role' => UserRole::Guru]);
        $schedule = $this->seedMinimal($guru);
        $student = Student::first();
        $date = now()->toDateString();

        $payload = [
            'date' => $date,
            'topic' => 'Materi A',
            'notes' => 'Catatan',
            'attendance' => [$student->id => 'hadir'],
        ];

        $this->actingAs($guru)->post(route('journals.store', $schedule), $payload)->assertRedirect();
        $this->actingAs($guru)->post(route('journals.store', $schedule), $payload)->assertSessionHasErrors();
    }

    public function test_score_out_of_range_rejected(): void
    {
        $guru = User::factory()->create(['role' => UserRole::Guru]);
        $schedule = $this->seedMinimal($guru);
        $student = Student::first();

        $this->actingAs($guru)->post(route('journals.store', $schedule), [
            'date' => now()->toDateString(),
            'topic' => 'Materi',
            'attendance' => [$student->id => 'hadir'],
            'scores' => [['student_id' => $student->id, 'type' => 'Tugas', 'score' => 150]],
        ])->assertSessionHasErrors();
    }

    public function test_kepsek_cannot_update_journal(): void
    {
        $guru = User::factory()->create(['role' => UserRole::Guru]);
        $kepsek = User::factory()->create(['role' => UserRole::Kepsek]);
        $schedule = $this->seedMinimal($guru);
        $journal = Journal::create([
            'schedule_id' => $schedule->id,
            'date' => now()->toDateString(),
            'topic' => 'Topik',
        ]);

        $this->actingAs($kepsek)->get(route('journals.show', $journal))->assertOk();
        $this->actingAs($kepsek)->get(route('journals.edit', $journal))->assertForbidden();
    }

    public function test_backup_page_and_log_listing(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        \App\Models\BackupLog::create([
            'back_time' => now(),
            'status' => 'berhasil',
            'file_name' => 'backup_sqlite_test.sql',
        ]);

        $this->actingAs($admin)->get(route('admin.backups.index'))->assertOk()->assertSee('backup_sqlite_test.sql');
    }

    public function test_recap_pdf_filename_sanitizes_slashes(): void
    {
        $guru = User::factory()->create(['role' => UserRole::Guru, 'username' => 'guru.sri']);
        $schedule = $this->seedMinimal($guru);
        $semester = Semester::first();

        $response = $this->actingAs($guru)->get(route('recap.pdf', [
            'semester_id' => $semester->id,
            'teacher_id' => $guru->id,
        ]));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('rekap_guru.sri_2026-2027.pdf', $response->headers->get('content-disposition'));
    }

    public function test_search_by_class(): void
    {
        $guru = User::factory()->create(['role' => UserRole::Guru]);
        $schedule = $this->seedMinimal($guru);
        Journal::create(['schedule_id' => $schedule->id, 'date' => now()->toDateString(), 'topic' => 'T']);

        $this->actingAs($guru)->get(route('journals.search', ['class_id' => $schedule->class_id]))
            ->assertOk()
            ->assertSee('T');
    }
}
