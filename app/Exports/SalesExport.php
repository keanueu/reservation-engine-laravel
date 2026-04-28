<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SalesExport implements FromCollection, WithHeadings
{
    protected Collection $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Type',
            'Booking ID',
            'Guest Name',
            'Guest Email',
            'Start Date',
            'End Date',
            'Unit Name',
            'Total Amount',
            'Paid Amount',
            'Payment Status',
            'Paid At',
            'Created At',
        ];
    }
}
