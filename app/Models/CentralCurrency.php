<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CentralCurrency extends Model
{
    use Sluggable, HasFactory;
    
    protected $fillable = [
        'name', 'slug', 'rate', 'code', 'symbol', 'position', 'note', 'status',
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }
}
