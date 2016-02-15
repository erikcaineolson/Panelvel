<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\User;

/**
 * Class Domain
 * @package app\Models
 */
class Domain extends Model
{
    protected $fillable = [
        'name',
        'is_word_press',
        'user_id',
        'username',
        'password',
    ];

    /**
     * Each domain was created by a user
     *
     * @return mixed
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}