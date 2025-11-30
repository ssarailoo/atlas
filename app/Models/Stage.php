<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stage extends Model
{


    protected $fillable = [
        'name', 'role', 'order', 'min_days', 'next_stage_id'
    ];

    public function nextStage(): BelongsTo
    {
        return $this->belongsTo(Stage::class, 'next_stage_id');
    }
}
