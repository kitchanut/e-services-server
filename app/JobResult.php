<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobResult extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function uploads()
    {
        return $this->hasMany(Upload::class, 'ref_id')->where('type', 'job_result');
    }
}
