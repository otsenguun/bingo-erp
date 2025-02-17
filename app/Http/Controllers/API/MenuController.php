<?php

namespace App\Http\Controllers\API;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function searchMenu(Request $request)
    {
        $menuSearchQuery = $request->query('menuSearchQuery');
        $result = Menu::where('name', 'LIKE', '%' . $menuSearchQuery . '%')
            ->where('status', true)->get();

        return response()->json([
            'result' => $result
        ]);
    }
}
