<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Settings extends Model
{
    #Columns Name 
    protected $fillable = [
        'firebase_key',
        'currency',
        'logo',
        'copyright',
        'address',
        'contact',
        'facebook',
        'twitter',
        'instagram',
        'instagram',
        'youtube',
        'faqs',
    ];
}
