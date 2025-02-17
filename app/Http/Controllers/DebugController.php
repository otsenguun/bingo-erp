<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebugController extends Controller
{
    public function getTableNames()
    {
        $tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();

        return response()->json([
            'data' => implode(',', $tables),
        ]);
    }

    function clean($string) {
        // Replaces all spaces.
        $string = str_replace(' ', '', $string);

        // Removes special chars except '\' (backslash).
        return preg_replace('/[^A-Za-z0-9\\\-]/', '', $string);
    }

    /*
     * This function is used to update the validation rules from "pipe" format to "array" format.
     */
    /**
     * @throws \Exception
     */
    public function updateValidationRulesToNewFormat(Request $request)
    {
        $className = $this->clean($request->get('class'));
        $className = 'App\\Http\\Requests\\' . $className;
        $class = new $className();
        if (!$class) {
            throw new \Exception('Class not found');
        }
        $rules = $class->rules();
        $newRules = collect([]);
        foreach ($rules as $key => $rule) {
            // dd($rule);
            if (is_array($rule)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Already in new format',
                ]);
            }
            $newRules[$key] = explode('|', $rule);
        }

        // dd($newRules);

        // convert rules to collection
        $formattedRules = $newRules->map(function ($item) {
            return collect($item);
        });

        // dd($formattedRules);

        // update the request class
        $file = file_get_contents(app_path(str_replace('App', '', $className) . '.php'));
        // replace old rules with new rules by matching the array keys and values
        $formattedRules->each(function ($item, $key) use (&$file) {
            // dd($key);
            // dd($item->implode('|'));
            // dd($item->toJson());
            $file = str_replace("'{$key}' => '{$item->implode('|')}'", "'{$key}' => {$item->toJson()}", $file);
            // use single quotes for the values
            $file = str_replace('"', "'", $file);
        });

        $file = str_replace('\',', "', ", $file);
        file_put_contents(app_path(str_replace('App', '', $className) . '.php'), $file);
        return response()->json([
            'success' => true,
            'message' => 'Rules updated successfully',
        ]);
    }

    public function data()
    {
        $data = json_decode('{ "manual": { "is_active": "1", "name": "Manual Payment", "identifier": "manual", "description": "Manual Payment" }, "stripe": { "is_active": "1", "name": "Stripe", "identifier": "stripe", "description": "Stripe Payment Gateway", "key": "hello", "secret": "sk_test_51Kx7NpFAlaKtQaWM2I2Bmy36kMZ5p8r4iUudRBSEfA31vFOaWPUAf63oWEEsQLrHBLqBQHTDrtiNcnW1xr3MXaFB00AbO7Ii0j", "webhook_secret": "webhook-secret", "sandbox": true, "success_url": "/success", "fail_url": "/fail", "cancel_url": "/cancel", "ipn_url": "/ipn" } }');

        $data = array_values($data);

        dd($data);
    }

    public function version()
    {
        $current = '2.1';
        $latest = '3.5';
        // 1 means current one is greater than latest
        // 0 means equal
        // -1 means current one is less than latest
        return version_compare($current, $latest);
    }
}
