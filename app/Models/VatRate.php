<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VatRate extends Model
{
    use Sluggable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug', 'code', 'rate', 'note', 'status',  'is_group_tax', 'group_tax_ids',
    ];

    protected $casts = [
        'group_tax_ids' => 'array',
    ];

    protected $appends = ['group_tax_details'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

      // Accessor to get group tax details
      public function getGroupTaxDetailsAttribute()
      {
          if ($this->is_group_tax && !empty($this->group_tax_ids)) {
              return VatRate::whereIn('id', $this->group_tax_ids)->get();
          }

          return null;
      }
}
