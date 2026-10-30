<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Answer;
use App\Models\AuditLog;
use App\Models\Channel;
use App\Models\ChannelMute;
use App\Models\Message;
use App\Models\Question;
use App\Models\Report;
use App\Models\User;
use App\Services\ChannelService;
use Illuminate\Database\Seeder;

/**
 * Channels for the school structure, demo chat messages, a mute and a few open reports.
 */
class ChannelSeeder extends Seeder
{
    public function run(ChannelService $channels): void
    {
        $channels->syncAll();

        if (Message::exists()) {
            return;
        }

        $lines = [
            'Здравейте! Някой разбра ли домашното по математика?',
            'Да, задачите са от стр. 48, от 3 до 7.',
            'Благодаря! 🙏',
            'Утре имаме ли час по физическо или е отменен?',
            'Класната каза, че има. Носете екипи.',
            'Кой ще идва на репетицията на театралното студио в четвъртък?',
            'Аз! Ще донеса и костюмите.',
            'Напомням, че в петък е контролното по испански – pretérito indefinido.',
            '¡Gracias! Ще се видим в библиотеката да учим заедно?',
            'Може, в 14:30 след последния час.',
        ];

        Channel::query()->where('is_archived', false)->get()->each(function (Channel $channel) use ($lines) {
            $members = User::query()
                ->whereIn('role', [Role::Student, Role::Moderator])
                ->whereHas('schoolClass', fn ($q) => match ($channel->scope_type->value) {
                    'class' => $q->whereKey($channel->scope_id),
                    'grade' => $q->where('grade_id', $channel->scope_id),
                    'profile' => $q->where('profile_id', $channel->scope_id),
                })
                ->get();

            if ($members->isEmpty()) {
                return;
            }

            $count = $channel->scope_type->value === 'class' ? 8 : 4;
            $start = now()->subDays(2);

            foreach (array_slice($lines, 0, $count) as $i => $line) {
                $channel->messages()->create([
                    'user_id' => $members[$i % $members->count()]->id,
                    'body' => $line,
                    'created_at' => $start->copy()->addMinutes($i * 37),
                    'updated_at' => $start->copy()->addMinutes($i * 37),
                ]);
            }
        });

        $this->demoModeration();
    }

    private function demoModeration(): void
    {
        $moderator = User::query()->where('role', Role::Moderator)->first();
        $students = User::query()->where('role', Role::Student)->whereNotNull('class_id')->orderBy('id')->get();

        if (! $moderator || $students->count() < 3) {
            return;
        }

        $offender = $students->last();
        $channel = Channel::query()->where('scope_type', 'class')->where('scope_id', $offender->class_id)->first();

        if ($channel) {
            $spam = $channel->messages()->create(['user_id' => $offender->id, 'body' => 'КУПЕТЕ ЕВТИНИ ОТГОВОРИ ЗА КОНТРОЛНИТЕ!!! пишете ми на лично']);

            $mute = ChannelMute::create([
                'channel_id' => $channel->id,
                'user_id' => $offender->id,
                'muted_until' => now()->addDay(),
                'reason' => 'Спам в канала на класа',
                'created_by' => $moderator->id,
            ]);
            AuditLog::record($moderator, 'user_muted', $offender, ['until' => $mute->muted_until->toIso8601String(), 'channel' => $channel->name, 'reason' => $mute->reason]);

            $reporter = $students->firstWhere('class_id', $offender->class_id) ?? $students->first();
            Report::create(['reporter_id' => $reporter->id, 'reportable_type' => 'message', 'reportable_id' => $spam->id, 'reason' => 'Спам и предлагане на преписване.']);
        }

        if ($question = Question::query()->whereNull('scope_type')->first()) {
            Report::create(['reporter_id' => $students->first(fn ($s) => $s->id !== $question->user_id)->id, 'reportable_type' => 'question', 'reportable_id' => $question->id, 'reason' => 'Въпросът е публикуван два пъти.']);
        }

        if ($answer = Answer::query()->first()) {
            Report::create(['reporter_id' => $students->first(fn ($s) => $s->id !== $answer->user_id)->id, 'reportable_type' => 'answer', 'reportable_id' => $answer->id, 'reason' => 'Грешна информация, подвежда другите.']);
        }
    }
}
