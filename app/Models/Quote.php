<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $guarded = [];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }
}
