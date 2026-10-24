<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model
{
    protected $fillable = ['name', 'department', 'phone'];

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }
}
