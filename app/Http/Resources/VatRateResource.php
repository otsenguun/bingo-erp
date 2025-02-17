<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VatRateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'slug' => $this->slug,
            'rate' => $this->rate,
            'note' => $this->note,
            'isGroupTax' => $this->is_group_tax,
            'status' => (int) $this->status,
        ];

        if ($this->is_group_tax && $this->group_tax_ids) {
            $data['groupTaxIds'] = $this->group_tax_ids;
            if ($this->group_tax_details) {
                $data['groupTaxDetails'] = $this->group_tax_details->toArray();
            }
        }

        return $data;
    }
}
