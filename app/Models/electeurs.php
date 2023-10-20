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
        'ddn',
        'ldn',
        'sexe',
        'cin',
        'delivrance',
        'filieres',
        'niveau',
        'adresse',
        'contact',
        'axes',
        'sympathisant',
        'facebook',
        'date_inscription',
        'secteurs',
        'status',
        'votes'
    ];

    public $timestamps = false;
}
