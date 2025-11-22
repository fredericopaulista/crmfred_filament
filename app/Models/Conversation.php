<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $guarded = [];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    //
}
