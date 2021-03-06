<?php

namespace GetShitDone;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Objective extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_complete' => 'boolean',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'due_at',
    ];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => \GetShitDone\Events\ObjectiveCreated::class,
        'updated' => \GetShitDone\Events\ObjectiveUpdated::class,
        'deleted' => \GetShitDone\Events\ObjectiveDeleted::class,
    ];

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * An objective belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format(Carbon::ATOM);
    }

    /**
     * Returns objectives without a set priority.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithoutPriority(Builder $query)
    {
        return $query->whereNull('priority');
    }

    /**
     * Returns objectives without a set schedule.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeWithoutSchedule(Builder $query)
    {
        return $query->whereNull('due_at');
    }

    /**
     * Returns objectives with a due date on a given week.
     *
     * @param Builder $query
     * @param Carbon $week
     * @return Builder
     */
    public function scopeDueOnWeek(Builder $query, Carbon $week)
    {
        return $query
            ->whereNotNull('due_at')
            ->where('due_at', '>=', $week->copy()->startOfWeek())
            ->where('due_at', '<=', $week->copy()->endOfWeek());
    }
}
