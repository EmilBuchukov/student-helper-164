<?php

namespace Database\Seeders;

use App\Enums\ScopeType;
use App\Models\Question;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo Q&A: school-wide and class questions with answers, votes and an accepted answer.
 */
class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        if (Question::exists()) {
            return;
        }

        $students = User::query()->whereIn('role', ['student', 'moderator'])->whereNotNull('class_id')->get();

        if ($students->count() < 3) {
            return;
        }

        $items = [
            ['Как се решава квадратно уравнение с параметър?', 'Имаме x² - 2mx + m - 1 = 0. Как да намеря стойностите на m, за които има два различни реални корена?', 'Математика', [
                'Условието е дискриминантата да е положителна: D = 4m² - 4(m - 1) > 0. Получаваш m² - m + 1 > 0, което е вярно за всяко m.',
                'Провери и в учебника на стр. 54 – там има почти същата задача.',
            ]],
            ['Кога се използва pretérito indefinido и кога imperfecto?', 'Постоянно бъркам двете минали времена на испански. Има ли лесно правило?', 'Испански език', [
                'Indefinido – за завършени действия в конкретен момент (ayer fui al cine). Imperfecto – за описания и повтарящи се действия в миналото (cuando era niño, jugaba…).',
                'Аз си ги запомням така: imperfecto е „фонът“, indefinido – „събитието“.',
            ]],
            ['Някой има ли конспекта по история за срока?', 'Пропуснах последния час и не знам кои теми влизат в контролното.', 'История и цивилизации', [
                'Темите са от Възраждането до Освобождението – уроци 12 до 20.',
            ]],
            ['Какви извънкласни клубове има тази година?', 'Нов съм в училището и искам да се включа в нещо след часовете.', null, [
                'Има дебатен клуб, клуб по роботика и театрално студио. Виж секцията „Клубове“.',
                'Препоръчвам театралното студио – ходим на фестивал в Испания през пролетта!',
            ]],
            ['Как се пресмята напрегнатост на електрично поле от два заряда?', 'Двата заряда са на разстояние 10 cm. Как се събират векторите?', 'Физика и астрономия', [
                'Пресмяташ E за всеки заряд поотделно по E = kq/r² и ги събираш векторно – внимавай за посоките.',
            ]],
        ];

        $classes = SchoolClass::query()->ordered()->get();

        foreach ($items as $i => [$title, $body, $subject, $answers]) {
            $author = $students[$i % $students->count()];
            $scoped = $i % 2 === 1 && $author->class_id;

            $question = Question::create([
                'user_id' => $author->id,
                'title' => $title,
                'body' => $body,
                'subject' => $subject,
                'scope_type' => $scoped ? ScopeType::Grade->value : null,
                'scope_id' => $scoped ? $classes->firstWhere('id', $author->class_id)?->grade_id : null,
                'created_at' => now()->subDays(10 - $i),
            ]);

            $responders = $students->where('id', '!=', $author->id)
                ->when($scoped, fn ($c) => $c->filter(fn (User $u) => $u->schoolClass?->grade_id === $question->scope_id))
                ->values();

            foreach ($answers as $j => $text) {
                $responder = $responders[$j % max(1, $responders->count())] ?? null;

                if (! $responder) {
                    continue;
                }

                $answer = $question->answers()->create([
                    'user_id' => $responder->id,
                    'body' => $text,
                    'created_at' => $question->created_at->addHours($j + 1),
                ]);

                foreach ($responders->where('id', '!=', $responder->id)->take(3 - $j) as $voter) {
                    $answer->votes()->create(['user_id' => $voter->id]);
                }

                if ($j === 0 && $i % 3 !== 2) {
                    $question->update(['accepted_answer_id' => $answer->id]);
                }
            }

            foreach ($students->where('id', '!=', $author->id)->take(5 - $i) as $voter) {
                $question->votes()->create(['user_id' => $voter->id]);
            }
        }
    }
}
