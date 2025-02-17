<?php

namespace App\Http\Controllers\API;

use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionPaymentResource;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator|\LaravelIdea\Helper\App\Models\_IH_Payment_C|Payment[]
     */
    public function index(Request $request)
    {
        $tenant = tenant();
        $payments= tenancy()->central(function () use ($tenant, $request) {
            return Payment::with(['tenant', 'plan', 'subscription'])
                ->where('tenant_id', $tenant->id)
                ->latest()
                ->paginate($request->perPage);
        });
        return SubscriptionPaymentResource::collection($payments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return string
     */
    public function download(Request $request)
    {
        $paymentId = $request->input('payment_id');
        $payment = tenancy()->central(function () use ($paymentId) {
            return Payment::with(['tenant', 'plan', 'subscription'])->findOrFail($paymentId);
        });
        $pdf = PDF::loadView('pdf.payment', compact('payment'));
        // download PDF file with download method
        return $pdf->download('payment.pdf');
    }
}