<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class RichEditorController extends Controller
{
    private $parentFolderForEditorFiles = 'RichEditorFiles';

    public function handleUpload(Request $request){
        
        $request->validate([
            'image' => ['required'],
            'target_dir' => ['required', 'string'],
        ]);


        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $extension = $image->getClientOriginalExtension();

                $path = store_in_tenant("$this->parentFolderForEditorFiles/$request->target_dir", $image, $extension);

                return response()->json(['url' => public_tenant_path($path)]);
            }
        } catch (\Exception $e) {
            return response()->json(['url' => '']);
        }
        
    }
}
