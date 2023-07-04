<?php

namespace App\Models;

use App\Enums\NotificationChannelEnum;
use App\Enums\NotificationTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'team_id',
        'type',
        'number',
        'channel',
        'receiver',
        'message',
    ];

    protected $casts = [
        'type' => NotificationTypeEnum::class,
        'channel' => NotificationChannelEnum::class,
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    //    public function tables(): MorphToMany
    //    {
    //        return $this->morphedByMany(Table::class, 'notificationable');
    //    }

    public function rooms(): MorphToMany
    {
        return $this->morphedByMany(Room::class, 'notificationable');
    }
}
