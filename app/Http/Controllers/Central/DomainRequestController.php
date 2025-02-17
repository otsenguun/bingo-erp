<?php

namespace App\Http\Controllers\Central;

use Illuminate\Http\Request;
use App\Models\DomainRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Stancl\Tenancy\Database\Models\Domain;
use App\Http\Resources\DomainRequestResource;

class DomainRequestController extends Controller
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $domainRequests = DomainRequest::with(['tenant', 'modifiedBy'])
            ->orderBy('id', 'desc')
            ->paginate($request->perPage ?? 10);

        return DomainRequestResource::collection($domainRequests);
    }

    /**
     * @param  \App\Models\DomainRequest  $domainRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DomainRequest $domainRequest, Request $request)
    {
        $domainRequest->update($request->only('status'));

        if ($domainRequest->status == DomainRequest::STATUS_CONNECTED) {
            $domain = Domain::firstOrCreate([
                'tenant_id' => $domainRequest->tenant_id,
                'domain' => $domainRequest->requested_domain,
            ]);

            // add activity log
            activity()
            ->causedBy(Auth::user())
            ->withProperties([
                'name' => $domainRequest->requested_domain,
                'code' =>  '[' . $domainRequest->requested_domain . ']',
                'event' => 'Update',
                'slug' => '/',
                'routeName' => 'domain-requests.index'
            ])
            ->useLog('Domain Request')
            ->log('Domain Request');

            return $this->responseWithSuccess('Domain request updated successfully.', $domain);
        }

        Domain::where([
            'tenant_id' => $domainRequest->tenant_id,
            'domain' => $domainRequest->requested_domain,
        ])->delete();

          // add activity log
          activity()
          ->causedBy(Auth::user())
          ->withProperties([
              'name' => $domainRequest->requested_domain,
              'code' =>  '[' . $domainRequest->requested_domain . ']',
              'event' => 'Update',
              'slug' => '/',
              'routeName' => 'domain-requests.index'
          ])
          ->useLog('Domain Request Updated')
          ->log('Domain Request Updated');

        return $this->responseWithSuccess('Domain request denied successfully.');
    }

    /**
     * @param  \App\Models\DomainRequest  $domainRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(DomainRequest $domainRequest)
    {
        $domainRequest->delete();

        return response()->noContent();
    }
}