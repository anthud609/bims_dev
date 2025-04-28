<?php
namespace App\Modules\Employees\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employee extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table   = 'employees';

    protected $fillable = [
      'id','user_id','first_name','last_name','title'
    ];

    protected static function booted()
    {
        static::creating(function($m){
            if (! $m->id) {
                $m->id = Str::uuid()->toString();
            }
        });
    }

    /** one‐to‐one back to your Auth User */
    public function user()
    {
        return $this->belongsTo(
          \App\Modules\Auth\Models\User::class,
          'user_id',
          'id'
        );
    }

    /** helper: full name */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
