<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class permissions extends Model
{
    #Columns Name 
    protected $fillable = [
        'title',
        'deleted_at'
    ];
}
