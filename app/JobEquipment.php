<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobEquipment extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
}
