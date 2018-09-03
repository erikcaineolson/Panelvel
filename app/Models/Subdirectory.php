<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Subdirectory
 * @package App\Models
 */
class Subdirectory extends Model
{
    use SoftDeletes;

    /**
     * @var array
     */
    protected $fillable = [
        'domain_id',
        'moniker',
        'is_word_press',
    ];


    /**
     * Each subdirectory must belong to a domain.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
}
