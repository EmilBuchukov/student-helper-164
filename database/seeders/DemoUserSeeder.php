<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\AllowedStudent;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    public const DEMO_PASSWORD = 'password';

    /**
     * Admin (from .env), a teacher, a moderator, 25 students and some unclaimed whitelist rows.
     */
    public function run(): void
    {
        $this->seedAdmin();

        $classes = SchoolClass::query()->ordered()->get();
        $password = Hash::make(self::DEMO_PASSWORD);

        $this->upsertUser('teacher@example.com', 'Мария Петрова', Role::Teacher, null, $password);

        $this->upsertStudent('moderator@example.com', 'Георги Иванов', Role::Moderator, $classes->firstWhere('name', '11А'), $password);

        $names = [
            'Иван Георгиев', 'Елена Димитрова', 'Николай Стоянов', 'Мартина Колева', 'Петър Николов',
            'Виктория Попова', 'Александър Христов', 'Габриела Тодорова', 'Даниел Маринов', 'Йоана Ангелова',
            'Кристиян Василев', 'Симона Илиева', 'Борис Атанасов', 'Деница Петкова', 'Стефан Йорданов',
            'Никол Методиева', 'Радослав Кирилов', 'Теодора Митева', 'Калоян Русев', 'Михаела Борисова',
            'Владимир Симеонов', 'Анна-Мария Славова', 'Пламен Генов', 'Цветелина Найденова', 'Мартин Добрев',
        ];

        foreach ($names as $i => $name) {
            $number = $i + 1;
            $this->upsertStudent("student{$number}@example.com", $name, Role::Student, $classes[$i % $classes->count()], $password);
        }

        foreach (range(1, 8) as $i) {
            AllowedStudent::firstOrCreate(
                ['email' => "newstudent{$i}@example.com"],
                ['class_id' => $classes[$i % $classes->count()]->id],
            );
        }
    }

    private function seedAdmin(): void
    {
        $email = config('school.seed_admin.email');
        $password = config('school.seed_admin.password');

        if (! $email || ! $password) {
            $this->command?->warn('SEED_ADMIN_EMAIL / SEED_ADMIN_PASSWORD are not set; skipping admin account.');

            return;
        }

        $this->upsertUser($email, 'Администратор', Role::Admin, null, Hash::make($password));
    }

    private function upsertStudent(string $email, string $name, Role $role, SchoolClass $class, string $password): void
    {
        $user = $this->upsertUser($email, $name, $role, $class, $password);

        AllowedStudent::updateOrCreate(
            ['email' => $email],
            ['class_id' => $class->id, 'claimed_at' => now(), 'claimed_by' => $user->id],
        );
    }

    private function upsertUser(string $email, string $name, Role $role, ?SchoolClass $class, string $password): User
    {
        $user = User::firstOrNew(['email' => $email]);

        $user->forceFill([
            'name' => $name,
            'password' => $password,
            'role' => $role,
            'class_id' => $class?->id,
            'is_active' => true,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        return $user;
    }
}
