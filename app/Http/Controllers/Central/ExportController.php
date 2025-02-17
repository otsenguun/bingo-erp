<?php

namespace App\Http\Controllers\Central;

use App\Models\Tenant;
use Illuminate\Http\Request;
use App\Exports\ExportTenant;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\Tenant\TenantResource;

class ExportController extends Controller
{
    public function tenantsPdf()
    {
        $tenants = Tenant::latest()->get();
        view()->share('tenants', $tenants);
        $pdf = PDF::loadView('pdf.tenants');
        return $pdf->download('tenants-list.pdf');
    }

    public function tenantsExportExcel(Request $request){
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $term = $request->input('term');

        return Excel::download(new ExportTenant($startDate, $endDate, $term), 'Tenants.xlsx');
    }
}