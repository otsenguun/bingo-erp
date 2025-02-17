<?php

namespace App\Http\Controllers\Central;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\DomainResource;
use Stancl\Tenancy\Database\Models\Domain;

class DomainController extends Controller
{
    public function index(Request $request)
    {
        $domains = Domain::with('tenant');
        return DomainResource::collection($domains->latest()->paginate($request->perPage ?? 10));
    }

    public function destroy(Domain $domain)
    {

        // add activity log
        activity()
            ->causedBy(Auth::user())
            ->performedOn($domain)
            ->withProperties([
                'name' => $domain->domain,
                'code' =>  '[' . $domain->domain . ']',
                'event' => 'Delete',
                'slug' => '/',
                'routeName' => ''
            ])
            ->useLog('Domain Deleted')
            ->log('Domain Deleted');

        $domain->delete();
        return $this->responseWithSuccess('Domain deleted successfully.');
    }
}
