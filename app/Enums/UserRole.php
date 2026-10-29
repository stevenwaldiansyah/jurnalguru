<?php

namespace App\Enums;

enum UserRole: string
{
    case Guru = 'guru';
    case Kepsek = 'kepsek';
    case Tu = 'tu';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Guru => 'Guru',
            self::Kepsek => 'Kepala Sekolah',
            self::Tu => 'Staf Tata Usaha',
            self::Admin => 'Pengelola Sistem',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::Guru => 'dashboard.guru',
            self::Kepsek => 'dashboard.kepsek',
            self::Tu => 'dashboard.tu',
            self::Admin => 'dashboard.admin',
        };
    }
}
