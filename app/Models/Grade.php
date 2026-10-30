<?php

namespace App\Models;

use Database\Factories\GradeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['number'])]
class Grade extends Model
{
    /** @use HasFactory<GradeFactory> */
    use HasFactory;

    public const MIN = 8;

    public const MAX = 12;

    /**
     * @return HasMany<SchoolClass, $this>
     */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    protected function casts(): array
    {
        return [
            'number' => 'integer',
        ];
    }
}
