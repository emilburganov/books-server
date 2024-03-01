<?php

namespace App\Models;

use http\Env\Request;
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
        return round($this->users()->avg('rating'), 1) > 0
            ? round($this->users()
                ->where('rating', '!=', 0)
                ->avg('rating'), 1)
            : 0;
    }

    public function getIsSelectedAttribute()
    {
        $user = User::query()->firstWhere('token', request()->bearerToken());

        if (!$user) {
            return null;
        }

        return boolval($this
            ->users()
            ->withPivot('is_selected')
            ->firstWhere('user_id', $user->id)
            ?->pivot->is_selected);
    }
}
