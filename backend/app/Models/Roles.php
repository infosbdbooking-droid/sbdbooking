<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Permissions;
class Roles extends Model
{

    protected $fillable = ['title']; 
    public function permissions()
    {
        return $this->belongsToMany(Permissions::class, 'permission_role', 'role_id', 'permission_id');
    }
}

?>