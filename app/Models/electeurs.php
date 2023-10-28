<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class electeurs extends Model
{
    use HasFactory;

    protected $table = "electeurs"; 
    
    protected $fillable = [
        'photo',
        'numero_carte',
        'nom',
        'prenom',
        'sexe',
        'cin',
        'axes',
        'sympathisant',
        'date_inscription',
        'secteurs',
        'status',
        'votes'
    ];

    public $timestamps = false;
}
