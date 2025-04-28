<?php
namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Auth
 *
 * @package App\Modules\Auth\Models
 *
 * @property string    $id
 * @property string $created_at
 * @property string $updated_at
 */
// app/modules/Auth/Models/User.php
class User extends Model
{
    // disable auto‐incrementing
    public $incrementing = false;

    // treat primary key as a string
    protected $keyType = 'string';

    // if your column is literally named "uuid" instead of "id"
    protected $primaryKey = 'id';

    protected $fillable = ['email','password','uuid'];
    protected $hidden   = ['password'];
    
    // automatically generate a UUID on create:
    protected static function boot()
    {
        parent::boot();
        static::creating(function($model){
            if (!$model->{$model->getKeyName()}) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }
}
