<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('employee');
    }

    protected $fillable = [
        'full_name',
        'position_id',
        'department_id',
        'hire_date',
        'education',
        'phone',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
        ];
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function qualifications(): HasMany
    {
        return $this->hasMany(Qualification::class);
    }

    /**
     * Дата следующего обязательного повышения квалификации
     * рассчитывается от даты последнего повышения + 3 года
     * (ст. 47 ФЗ «Об образовании в Российской Федерации»).
     */
    protected function nextQualificationDate(): Attribute
    {
        return Attribute::make(
            get: function (): ?CarbonImmutable {
                $latest = $this->qualifications->sortByDesc('date')->first()
                    ?? $this->qualifications()->orderByDesc('date')->first();

                return $latest?->next_date?->toImmutable()
                    ?? $latest?->date?->toImmutable()?->addYears(3);
            },
        );
    }
}
