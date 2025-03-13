<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    public $fillable = ['name', 'description', 'location', 'capacity', 'waitListCapacity', 'role', 'starts_at', 'ends_at'];

    public $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime'
    ];

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user')->withTimestamps();
    }

    public function waitListUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_user_wait_list')->withTimestamps();
    }

    public function isParticipant(User $user): bool
    {
        return $this->participants()->where('user_id', $user->id)->exists();
    }

    public function isOnWaitList(User $user): bool
    {
        return $this->waitListUsers()->where('user_id', $user->id)->exists();
    }

    public function canAddNewParticipant(): bool
    {
        return $this->participants()->count() < $this->capacity;
    }

    public function canAddInWaitList(): bool
    {
        return $this->waitListUsers()->count() < $this->waitListCapacity;
    }

    public function isPast(): bool
    {
        return Carbon::now()->greaterThan($this->ends_at);
    }

    public function isHappening(): bool
    {
        $now = Carbon::now();
        return $now->greaterThanOrEqualTo($this->starts_at) &&
            $now->lessThanOrEqualTo($this->ends_at);
    }

    public function toArray(): array
    {
        $array = array_merge(parent::toArray(), [
            'is_past' => $this->isPast(),
            'is_happening' => $this->isHappening(),
            'can_add_in_waitList' => $this->canAddInWaitList(),
            'can_add_new_participant' => $this->canAddNewParticipant(),
        ]);
        /** @var User $user */
        $user = auth()->user();
        if($user){
            $array['is_participant'] = $this->isParticipant($user);
            $array['is_waiting'] = $this->isOnWaitList($user);
        }

        return $array;
    }
}
