<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Profile;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class SchoolStructureSeeder extends Seeder
{
    /**
     * Grades 8-12, a few subject profiles and demo classes.
     */
    public function run(): void
    {
        foreach (range(Grade::MIN, Grade::MAX) as $number) {
            Grade::firstOrCreate(['number' => $number]);
        }

        $profiles = collect([
            'Испански и английски език',
            'Испански език и информатика',
            'Испански език и математика',
            'Испански език и биология',
        ])->mapWithKeys(fn (string $name) => [$name => Profile::firstOrCreate(['name' => $name])]);

        $classes = [
            [8, 'А', 'Испански и английски език'],
            [8, 'Б', 'Испански език и информатика'],
            [9, 'А', 'Испански и английски език'],
            [9, 'Б', 'Испански език и математика'],
            [10, 'А', 'Испански език и биология'],
            [10, 'Б', 'Испански език и информатика'],
            [11, 'А', 'Испански и английски език'],
            [11, 'Б', 'Испански език и математика'],
            [12, 'А', 'Испански език и информатика'],
            [12, 'Б', 'Испански и английски език'],
        ];

        foreach ($classes as [$grade, $letter, $profile]) {
            SchoolClass::updateOrCreate(
                ['grade_id' => Grade::where('number', $grade)->value('id'), 'letter' => $letter],
                ['profile_id' => $profiles[$profile]->id],
            );
        }
    }
}
