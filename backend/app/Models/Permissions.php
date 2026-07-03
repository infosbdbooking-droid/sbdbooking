<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    #Columns Name 
    protected $fillable = [
        'title',
        'route_prefix',
        'deleted_at'
    ];
}
