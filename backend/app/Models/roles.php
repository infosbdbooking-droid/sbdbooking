<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\permissions;
class roles extends Model
{

    protected $fillable = ['title']; 
    public function permissions()
    {
        return $this->belongsToMany(permissions::class, 'permission_role', 'role_id', 'permission_id');
    }
}

?>