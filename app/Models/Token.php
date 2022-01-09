<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Token extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id', 'access_token', 'refresh_token', 'expires_at'
    ];

    public function scopeValid($query, $client_id)
    {
        return $query->where('client_id', $client_id)
                     ->where('expires_at', '>', Carbon::now()->format('Y-m-d H:i:s'));
    }

}
