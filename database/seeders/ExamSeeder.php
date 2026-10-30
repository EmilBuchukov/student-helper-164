<?php

namespace Database\Seeders;

use App\Enums\ExamType;
use App\Enums\ScopeType;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\MaterialStorage;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

/**
 * Demo exams for every class plus a few grade/profile-wide ones, with small sample materials.
 */
class ExamSeeder extends Seeder
{
    public function run(MaterialStorage $storage): void
    {
        if (Exam::exists()) {
            return;
        }

        $classes = SchoolClass::query()->ordered()->with('profile')->get();
        $teacher = User::where('email', 'teacher@example.com')->first();

        $templates = [
            ['Математика', 'Контролна работа по тригонометрия', ExamType::Test, 'Синусова и косинусова теорема, формули за събиране.'],
            ['Испански език', 'Класна работа – Unidad 4', ExamType::Exam, 'Pretérito indefinido и лексика от урок 4.'],
            ['Български език и литература', 'Устно изпитване по „Под игото“', ExamType::Oral, null],
            ['Физика и астрономия', 'Тест – електрично поле', ExamType::Test, 'Закон на Кулон, напрегнатост, потенциал.'],
            ['История и цивилизации', 'Проект – Българското възраждане', ExamType::Other, 'Презентация в групи по 3-4 ученици.'],
        ];

        foreach ($classes as $i => $class) {
            $author = User::where('class_id', $class->id)->where('role', 'student')->first();

            foreach ([0, 1] as $offset) {
                [$subject, $title, $type, $description] = $templates[($i + $offset) % count($templates)];

                $exam = Exam::create([
                    'subject' => $subject,
                    'title' => $title,
                    'description' => $description,
                    'exam_date' => now()->addDays(3 + $i + $offset * 9)->toDateString(),
                    'type' => $type,
                    'scope_type' => ScopeType::SchoolClass,
                    'scope_id' => $class->id,
                    'created_by' => $author?->id ?? $teacher?->id,
                ]);

                if ($author && $offset === 0) {
                    $this->attach($storage, $exam, $author, 'sample.txt', 'Конспект', 'konspekt.txt');
                    $this->attach($storage, $exam, $author, 'sample.pdf', 'Примерни задачи', 'primerni-zadachi.pdf');
                }
            }

            // One past exam per class, so the list shows both upcoming and past items.
            Exam::create([
                'subject' => 'Английски език',
                'title' => 'Vocabulary test – Unit 2',
                'exam_date' => now()->subDays(5 + $i)->toDateString(),
                'type' => ExamType::Test,
                'scope_type' => ScopeType::SchoolClass,
                'scope_id' => $class->id,
                'created_by' => $author?->id,
            ]);
        }

        foreach ($classes->pluck('grade')->unique('id') as $grade) {
            Exam::create([
                'subject' => 'Математика',
                'title' => "Входно ниво – {$grade->number} клас",
                'exam_date' => now()->addDays(20)->toDateString(),
                'type' => ExamType::Exam,
                'scope_type' => ScopeType::Grade,
                'scope_id' => $grade->id,
                'created_by' => $teacher?->id,
            ]);
        }

        foreach ($classes->pluck('profile')->filter()->unique('id') as $profile) {
            Exam::create([
                'subject' => 'Профилиращ предмет',
                'title' => "Олимпиада – вътрешен кръг ({$profile->name})",
                'exam_date' => now()->addDays(30)->toDateString(),
                'type' => ExamType::Other,
                'scope_type' => ScopeType::Profile,
                'scope_id' => $profile->id,
                'created_by' => $teacher?->id,
            ]);
        }
    }

    private function attach(MaterialStorage $storage, Exam $exam, User $author, string $file, string $title, string $name): void
    {
        $upload = new UploadedFile(__DIR__.'/files/'.$file, $name, null, null, true);

        $storage->store($exam, $author, $upload, $title);
    }
}
