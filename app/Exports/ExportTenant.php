<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Tenant;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Http\Resources\Tenant\TenantResource;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportTenant implements FromCollection, WithHeadings, ShouldAutoSize, WithEvents
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
        $query = Tenant::query();

        if ($this->startDate && $this->endDate) {
            $startDate = Carbon::createFromFormat('Y-m-d', $this->startDate)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $this->endDate)->endOfDay();
            $query = $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $query->where(function ($query) use ($term) {
            $query->where('data->name', 'Like', '%' . $term . '%')
                ->orWhere('data->domain', 'Like', '%' . $term . '%')
                ->orWhere('data->email', 'Like', '%' . $term . '%')
                ->orWhere('data->company', 'Like', '%' . $term . '%');
        });

        $tenants = TenantResource::collection($query->latest()->get())->map(function ($tenant) {
            return [
                $tenant->domain,
                $tenant->name,
                $tenant->email,
                $tenant->plan?->name,
                $tenant->on_trial ? 'True' : 'False',
                $tenant->email_verified_at ? 'True' : 'False',
                $tenant->is_subscribed ? 'True' : 'False',
                $tenant->Banned ? 'True' : 'False',
            ];
        });

        return $tenants;
    }

    public function headings(): array
    {
        return [
            'Domain',
            'Name',
            'Email',
            'Plan',
            'On Trial',
            'Is Verified',
            'Is Subscribed',
            'Banned',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Style the header row (headings)
                $event->getSheet()->getDelegate()->getStyle('A1:H1')->applyFromArray([
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
            },
        ];
    }
}
