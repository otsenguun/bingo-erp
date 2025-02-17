<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $causerName = null;

        // Fetch the user associated with the causer_id
        if ($this->causer_id) {
            $causer = User::find($this->causer_id);
            $causerName = $causer ? $causer->name : null;
        }

        return [
            'id' => $this->id,
            'log_name' => $this->log_name,
            'description' => $this->description,
            'event' => $this->properties['event'],
            'causer_name' => $causerName,
            'performedAt' => Carbon::parse($this->created_at)->format('j F, Y g:i A'),
            'related_data_code' => $this->properties['code'],
            'routeName' => $this->properties['routeName'] ?? null,
            'slug' => $this->properties['slug'] ?? null,
        ];
    }
}
