@extends('template')

@section('title')
    <title>@lang('Subscription  Payment Invoice')</title>
@endsection

@section('receiver-info')
    <h1>@lang('Invoice')</h1>
    <h4>@lang('Date') : {{ $payment->created_at->format('Y-m-d') }}</h4>
    <p><strong>@lang('Name'):</strong> {{ $payment->tenant->name }}</p>
    <p><strong>@lang('Company'):</strong> {{ $payment->tenant->company }}</p>
    <p><strong>@lang('Email'):</strong> {{ $payment->tenant->email }}</p>
    <p><strong>@lang('Domain'):</strong> {{ $payment->tenant->domain_url }}</p>
@endsection


@section('pdf-content')
    <br />
    <table class="invoice-table">
        <tr>
            <th>@lang('TrxID')</th>
            <th>@lang('From')</th>
            <th>@lang('To')</th>
            <th>@lang('Payment Status')</th>
            <th>@lang('Payment Method')</th>
            <th>@lang('Currency')</th>
        </tr>
        <tr>
            <td>#{{ $payment->system_trx_id }}</td>
            <td>{{ $payment->subscription->created_at->format('d-M-Y') }}</td>
            <td>{{ $payment->subscription->ends_at->format('d-M-Y') }}</td>
            <td>{{ Str::ucfirst($payment->status) }}</td>
            <td>{{ Str::ucfirst($payment->method) }}</td>
            <td>{{ Str::upper($payment->currency) }}</td>

        </tr>
    </table>
    <table class="invoice-table">
        <tr>
            <th>@lang('Plan')</th>
            <th>@lang('Number of Months')</th>
            <th>@lang('Amount')</th>
            <th>@lang('Subtotal')</th>
        </tr>
        <tr>
            <td>{{ $payment->plan->name }}</td>
            <td>{{ $payment->quantity }}</td>
            <td>{{ centralCurrencyCodeFormat($payment->plan->amount)}}</td>
            <td class="text-right"> 
                {{ centralCurrencyCodeFormat($payment->default_amount_rate * $payment->quantity) }} <br>
                (${{ $payment->amount * $payment->quantity }})
             </td>
        </tr>
        <tr>
            <td class="total" colspan="3">@lang('Subtotal')</td>
            <td class="total">
                {{ centralCurrencyCodeFormat($payment->default_amount_rate * $payment->quantity) }} <br>
                (${{ $payment->amount * $payment->quantity }})
            </td>
        </tr>
        <tr>
            <td colspan="3" class="total">@lang('Total')</td>
            <td class="total">
                {{ centralCurrencyCodeFormat($payment->default_amount_rate * $payment->quantity) }} <br>
                (${{ $payment->amount * $payment->quantity }})
            </td>
        </tr>

    </table>
@endsection
