<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Enums\RsvpStatus;
use App\Models\Club;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

/**
 * Demo clubs with leaders, members and upcoming events (plus school-wide events).
 */
class ClubSeeder extends Seeder
{
    public function run(): void
    {
        if (Club::exists()) {
            return;
        }

        $students = User::query()->whereIn('role', [Role::Student, Role::Moderator])->orderBy('id')->get();
        $admin = User::query()->where('role', Role::Admin)->first();

        if ($students->isEmpty()) {
            return;
        }

        $clubs = [
            ['Дебатен клуб', 'Учим се да аргументираме позиции, да слушаме и да спорим културно. Участваме в националното състезание по дебати.', [
                ['Тренировъчен дебат: „Да се забранят ли телефоните в час?“', 'Кабинет 214', 6, 18],
                ['Подготовка за регионалния турнир', 'Библиотека', 20, 15],
            ]],
            ['Клуб по роботика', 'Конструираме и програмираме роботи с Arduino и LEGO Mindstorms. Не е нужен предишен опит.', [
                ['Работилница: първият ни робот с Arduino', 'Компютърен кабинет 2', 4, 15],
            ]],
            ['Театрално студио „Сервантес“', 'Поставяме пиеси на испански и български език. Тази година репетираме „Дон Кихот“.', [
                ['Репетиция на „Дон Кихот“', 'Актова зала', 3, 16],
                ['Премиера на „Дон Кихот“', 'Актова зала', 40, 18],
            ]],
            ['Клуб по испанско кино', 'Гледаме и обсъждаме испански и латиноамерикански филми в оригинал със субтитри.', [
                ['Прожекция: „Мотоциклетни дневници“', 'Кабинет 305', 9, 15],
            ]],
            ['Еко клуб', 'Залесяване, разделно събиране и инициативи за по-зелено училище.', [
                ['Почистване на училищния двор', 'Училищен двор', 12, 10],
            ]],
        ];

        foreach ($clubs as $i => [$name, $description, $events]) {
            $leader = $students[$i % $students->count()];
            $club = Club::create(['name' => $name, 'slug' => Club::uniqueSlug($name), 'description' => $description, 'leader_id' => $leader->id, 'is_active' => true]);

            $members = $students->shuffle()->take(min(8, $students->count()))->push($leader)->unique('id');
            $club->members()->syncWithoutDetaching($members->mapWithKeys(fn (User $u) => [$u->id => ['joined_at' => now()->subDays(rand(1, 60))]])->all());

            foreach ($events as [$title, $location, $inDays, $hour]) {
                $this->event($club->id, $title, $location, $inDays, $hour, $leader->id, $members);
            }
        }

        Club::create(['name' => 'Клуб по шахмат', 'slug' => Club::uniqueSlug('Клуб по шахмат'), 'description' => 'Клубът е на пауза до намиране на нов ръководител.', 'is_active' => false]);

        foreach ([
            ['Ден на испанския език', 'Двор и физкултурен салон', 14, 11],
            ['Родителска среща', 'Актова зала', 25, 18],
            ['Коледен благотворителен базар', 'Фоайе', 45, 10],
        ] as [$title, $location, $inDays, $hour]) {
            $this->event(null, $title, $location, $inDays, $hour, $admin?->id, $students);
        }

        $this->event(null, 'Есенен крос', 'Южен парк', -10, 9, $admin?->id, $students);
    }

    /**
     * @param  Collection<int, User>  $audience
     */
    private function event(?int $clubId, string $title, string $location, int $inDays, int $hour, ?int $createdBy, $audience): void
    {
        $start = now()->startOfDay()->addDays($inDays)->setTime($hour, 0);

        $event = Event::create([
            'club_id' => $clubId,
            'title' => $title,
            'description' => 'Заповядайте! Повече информация ще получите от организаторите.',
            'starts_at' => $start,
            'ends_at' => $start->copy()->addHours(2),
            'location' => $location,
            'created_by' => $createdBy,
        ]);

        foreach ($audience->shuffle()->take(6)->values() as $j => $user) {
            $event->attendees()->attach($user->id, ['status' => $j % 3 === 0 ? RsvpStatus::Interested : RsvpStatus::Going]);
        }
    }
}
