<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;

class ServerController extends Controller
{
    public function ip()
    {
        return $this->responseWithSuccess('Server IP', [
            'server_ip' => request()->server('SERVER_ADDR'),
        ]);
    }

    public function runCommand(Request $request)
    {
        $validated = $request->validate([
            'command' => ['required', Rule::in(['optimize:clear'])]
        ]);

        $call = Artisan::call($validated['command']);

        if ($call === 0) {
            return response()->json([
                'message' => 'Cache cleared successfully!',
            ]);
        }

        return response()->json([
            'message' => 'Failed',
        ]);
    }
}