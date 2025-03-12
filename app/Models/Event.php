<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
