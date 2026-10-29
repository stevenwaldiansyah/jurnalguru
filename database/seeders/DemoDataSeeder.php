<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\SchoolClass;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing') && ! filter_var(env('SEED_DEMO', false), FILTER_VALIDATE_BOOL)) {
            $this->command?->warn('Demo seeder dilewati. Set SEED_DEMO=true atau gunakan APP_ENV=local/testing.');

            return;
        }

        $password = env('SEED_DEMO_PASSWORD');
        if (! $password) {
            $this->command?->error('SEED_DEMO_PASSWORD belum diatur di .env. Seeder demo tidak dijalankan.');

            return;
        }

        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            ['name' => 'Pengelola Sistem', 'password' => Hash::make($password), 'role' => UserRole::Admin]
        );

        $kepsek = User::updateOrCreate(
            ['username' => 'kepsek'],
            ['name' => 'Kepala Sekolah', 'password' => Hash::make($password), 'role' => UserRole::Kepsek]
        );

        $tu = User::updateOrCreate(
            ['username' => 'tu'],
            ['name' => 'Staf Tata Usaha', 'password' => Hash::make($password), 'role' => UserRole::Tu]
        );

        $guru = User::updateOrCreate(
            ['username' => 'guru.sri'],
            ['name' => 'Bu Sri Rahayu', 'password' => Hash::make($password), 'role' => UserRole::Guru]
        );

        $semester = Semester::firstOrCreate(
            ['name' => 'Ganjil', 'academic_year' => '2026/2027'],
            []
        );

        $classX = SchoolClass::firstOrCreate(['name' => 'X TKJ 1'], ['major' => 'Teknik Komputer dan Jaringan']);
        $classXI = SchoolClass::firstOrCreate(['name' => 'XI TKJ 2'], ['major' => 'Teknik Komputer dan Jaringan']);

        $matematika = Subject::firstOrCreate(['name' => 'Matematika']);
        $web = Subject::firstOrCreate(['name' => 'Pemrograman Web']);

        foreach (
            [
                ['001', 'Ahmad Fauzi'],
                ['002', 'Budi Santoso'],
                ['003', 'Citra Lestari'],
                ['004', 'Dewi Anggraini'],
                ['005', 'Eko Prasetyo'],
            ] as [$nis, $name]
        ) {
            Student::firstOrCreate(['nis' => $nis], ['name' => $name, 'class_id' => $classX->id]);
        }

        foreach (
            [
                ['011', 'Fajar Nugroho'],
                ['012', 'Gita Maharani'],
                ['013', 'Hendra Wijaya'],
            ] as [$nis, $name]
        ) {
            Student::firstOrCreate(['nis' => $nis], ['name' => $name, 'class_id' => $classXI->id]);
        }

        Schedule::firstOrCreate(
            [
                'teacher_id' => $guru->id,
                'class_id' => $classX->id,
                'subject_id' => $matematika->id,
                'semester_id' => $semester->id,
                'day' => 'Selasa',
            ],
            ['start_time' => '07:00:00', 'end_time' => '08:30:00']
        );

        Schedule::firstOrCreate(
            [
                'teacher_id' => $guru->id,
                'class_id' => $classXI->id,
                'subject_id' => $matematika->id,
                'semester_id' => $semester->id,
                'day' => 'Selasa',
            ],
            ['start_time' => '09:00:00', 'end_time' => '10:30:00']
        );

        Schedule::firstOrCreate(
            [
                'teacher_id' => $guru->id,
                'class_id' => $classXI->id,
                'subject_id' => $web->id,
                'semester_id' => $semester->id,
                'day' => 'Kamis',
            ],
            ['start_time' => '07:30:00', 'end_time' => '09:00:00']
        );

        unset($admin, $kepsek, $tu, $guru);
    }
}
