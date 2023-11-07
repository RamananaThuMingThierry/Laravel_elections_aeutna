<?php

namespace App\Exports;

use App\Models\electeurs;
use Maatwebsite\Excel\Concerns\FromCollection;

class ListeMembresExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return electeurs::select('nom', 'prenom')->get();
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Prénom',
        ];
    }
}
