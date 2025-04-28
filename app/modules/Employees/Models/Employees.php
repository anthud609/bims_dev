<?php
namespace App\Modules\Employees\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Employees
 *
 * @package App\Modules\Employees\Models
 *
 * @property int    $id
 * @property string $created_at
 * @property string $updated_at
 */
class Employees extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        //
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        //
    ];

    // Add your relationships, scopes, accessors/mutators below
}
