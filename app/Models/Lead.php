<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $guarded = [];

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }
}
