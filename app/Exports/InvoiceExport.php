<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use App\Http\Resources\InvoiceListResource;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class InvoiceExport implements FromCollection, WithHeadings, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $term;

    public function __construct($startDate, $endDate, $term)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->term = $term;
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $term = $this->term;
        $query = Invoice::with('client', 'invoicePayments', 'user');

        if ($this->startDate && $this->endDate) {
            $query = $query->whereBetween('invoice_date', [$this->startDate, $this->endDate]);
        }

        $query = $query->where(function ($query) use ($term) {
            $query->where('invoice_no', 'LIKE', '%' . $term . '%')
                ->where('reference', 'LIKE', '%' . $term . '%')
                ->orWhere('sub_total', 'LIKE', '%' . $term . '%')
                ->orWhere('po_reference', 'LIKE', '%' . $term . '%')
                ->orWhere('payment_terms', 'LIKE', '%' . $term . '%')
                ->orWhere('delivery_place', 'LIKE', '%' . $term . '%')
                ->orWhereHas('client', function ($newQuery) use ($term) {
                    $newQuery->where('name', 'LIKE', '%' . $term . '%')
                        ->orWhere('client_id', 'LIKE', '%' . $term . '%');
                });
        });

        $invoices = InvoiceListResource::collection($query->latest()->get())->map(function ($invoice) {
            $currencySymbol = getGeneralSettingsInfo()['currency']['symbol'];
            return [
                config('config.invoicePrefix') . ' - ' . $invoice->invoice_no,
                date('jS M, Y', strtotime($invoice->invoice_date)),
                $invoice->status ? 'Active' : 'Inactive',
                $invoice->client->name ?? 'N/A',
                $currencySymbol . strval($invoice->invoiceTotal()),
                $currencySymbol . strval($invoice->invoiceTotalPaid() > 0 ? $invoice->invoiceTotalPaid() : '0'),
                $currencySymbol . strval($invoice->totalDue() > 0 ? $invoice->totalDue() : '0'),
            ];
        });

        // Calculate the total of the amount related columns
        $netTotal = $invoices->sum(function ($row) {
            return floatval(str_replace(getGeneralSettingsInfo()['currency']['symbol'], '', $row[4]  ?? 0));
        });

        $paidTotal = $invoices->sum(function ($row) {
            return floatval(str_replace(getGeneralSettingsInfo()['currency']['symbol'], '', $row[5]  ?? 0));
        });

        $totalDue = $invoices->sum(function ($row) {
            return floatval(str_replace(getGeneralSettingsInfo()['currency']['symbol'], '', $row[6]  ?? 0));
        });

        // Add the total paid as a new row
        $invoices->push([
            '', '', '', '', 'Net Total = ' . getGeneralSettingsInfo()['currency']['symbol'] . $netTotal, 'Total Paid = ' . getGeneralSettingsInfo()['currency']['symbol'] . $paidTotal, 'Total Due = ' . getGeneralSettingsInfo()['currency']['symbol'] . $totalDue,
        ]);

        return $invoices;
    }

    // excel file columns
    public function headings(): array
    {
        return [
            'Invoice No',
            'Invoice Date',
            'Status',
            'Client',
            'Net Total',
            'Total Paid',
            'Total Due',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Get the last row index
                $lastRow = $event->getSheet()->getDelegate()->getHighestRow();

                // Style the last row (total amount row)
                $event->getSheet()->getDelegate()->getStyle('A' . $lastRow . ':G' . $lastRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 13,
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => [
                            'rgb' => '00FF00',
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);

                // Style the header row (headings)
                $event->getSheet()->getDelegate()->getStyle('A1:G1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 13,
                    ],
                    'fill' => [
                        'fillType' => 'solid',
                        'startColor' => [
                            'rgb' => '00FF00',
                        ],
                    ],
                ]);

                // Style the header row (headings)
                $event->getSheet()->getDelegate()->getStyle('E1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);
                $event->getSheet()->getDelegate()->getStyle('F1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);
                $event->getSheet()->getDelegate()->getStyle('G1')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                    ],
                ]);

                // Align column to the right
                $columnE = 'E';
                $event->getSheet()->getDelegate()->getStyle("{$columnE}2:{$columnE}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $columnF = 'F';
                $event->getSheet()->getDelegate()->getStyle("{$columnF}2:{$columnF}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $columnG = 'G';
                $event->getSheet()->getDelegate()->getStyle("{$columnG}2:{$columnG}{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
