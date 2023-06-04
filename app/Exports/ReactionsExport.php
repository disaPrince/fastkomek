<?php

namespace App\Exports;

use App\Models\ResultOfReaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ReactionsExport implements FromCollection, ShouldAutoSize, WithHeadings, WithEvents
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    public $id;

    public function __construct($id){
        $this->id = $id;
    }

    public function collection()
    {
        return ResultOfReaction::select('name', 'good', 'bad', 'whatever')->where('news_reactions_id', $this->id)->get();
    }

    public function headings(): array
    {
        return [
            'Пользователи',
            'Нравиться',
            'Не нравиться',
            'Все равно'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }

}
