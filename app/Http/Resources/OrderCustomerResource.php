<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class OrderCustomerResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'customer_name' => $this->ocHeaderInvoice->customer->name,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
            'payment_status' => $this->ocHeaderInvoice->payment_status,
            'status' => $this->ocHeaderInvoice->status

        ];
    }
}
