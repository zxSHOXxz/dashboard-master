<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Image\Manipulations;

class Donor extends Authenticatable implements HasMedia
{
    use HasRoles;
    use HasFactory;
    use Notifiable;
    use InteractsWithMedia;

    public function user()
    {
        return $this->morphOne(User::class, 'actor', 'actor_type', 'actor_id', 'id');
    }
    public function item_seens()
    {
        return $this->hasMany(\App\Models\ItemSeen::class, 'type_id', 'id')->where('type', "USER");
    }
    public function is_online()
    {
        if ($this->last_activity < \Carbon::now()->subMinutes(10)->format('Y-m-d H:i:s'))
            return 0;
        return 1;
        //return $this->last_activity;
    }
    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('tiny')
            ->fit(Manipulations::FIT_MAX, 120, 120)
            ->width(120)
            ->format(Manipulations::FORMAT_WEBP)
            ->nonQueued();

        $this
            ->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_MAX, 350, 1000)
            ->width(350)
            ->format(Manipulations::FORMAT_WEBP)
            ->nonQueued();

        $this
            ->addMediaConversion('original')
            ->fit(Manipulations::FIT_MAX, 1200, 10000)
            ->width(1200)
            ->format(Manipulations::FORMAT_WEBP)
            ->nonQueued();
    }
    public function donates()
    {
        return $this->hasMany(Donate::class);
    }
}
