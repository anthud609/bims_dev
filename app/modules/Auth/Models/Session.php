<?php
namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    public $incrementing = false;
    protected $keyType  = 'string';
    protected $table    = 'sessions';
    protected $fillable = [
      'id','user_id','token','ip_address','user_agent','expires_at','last_activity','is_revoked'
    ];
    protected $casts = [
      'is_revoked'    => 'boolean',
      'expires_at'    => 'datetime',
      'last_activity' => 'datetime',
    ];

    public $timestamps = false; // we manually handle created_at

    // In case you want to auto-generate the UUID id:
    protected static function boot()
    {
        parent::boot();
        static::creating(function($m){
            if (! $m->id) {
                $m->id = (string)\Illuminate\Support\Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
