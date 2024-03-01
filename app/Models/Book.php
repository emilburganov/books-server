<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Book extends Model
{
    use HasFactory;

    protected $appends = ['rating', 'is_selected'];

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(Genre::class);
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function getRatingAttribute()
    {
        return round($this->users()->avg('rating'), 2) > 0
            ? round($this->users()
                ->where('rating', '!=', 0)
                ->avg('rating'), 2)
            : 0;
    }

    public function getIsSelectedAttribute(): bool
    {
        return boolval($this
            ->users()
            ->withPivot('is_selected')
            ->firstWhere('user_id', Auth::id())
            ?->pivot->is_selected);
    }
}
