<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $guarded = [];

    public function permission()
    {
        return $this->hasMany(userPermission::class, 'page_id');
    }
}
