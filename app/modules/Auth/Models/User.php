<?php
namespace App\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Auth
 *
 * @package App\Modules\Auth\Models
 *
 * @property int    $id
 * @property string $created_at
 * @property string $updated_at
 */
class User extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['email', 'password'];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password'];
    

    // Add your relationships, scopes, accessors/mutators below
}
