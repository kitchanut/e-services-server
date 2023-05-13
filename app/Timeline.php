<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class Timeline extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user_line()
    {
        return $this->belongsTo(UserLine::class, 'user_line_id');
    }

    public function user_technician()
    {
        return $this->belongsTo(UserTechnician::class, 'user_technician_id');
    }
}
