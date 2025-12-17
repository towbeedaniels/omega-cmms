<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryReferenceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference_code' => $this->reference_code,
            'item' => $this->whenLoaded('item', function () {
                return [
                    'id' => $this->item->id,
                    'code' => $this->item->code,
                    'name' => $this->item->name,
                    'description' => $this->item->description,
                    'unit_cost' => $this->item->unit_cost,
                    'current_stock' => $this->item->current_stock ?? 0,
                    'minimum_stock' => $this->item->minimum_stock,
                    'maximum_stock' => $this->item->maximum_stock,
                    'reorder_point' => $this->item->reorder_point,
                    'unit_of_measurement' => $this->item->unitOfMeasurement->symbol ?? null
                ];
            }),
            'supplier' => $this->whenLoaded('supplier', function () {
                return [
                    'id' => $this->supplier->id,
                    'name' => $this->supplier->name,
                    'code' => $this->supplier->code
                ];
            }),
            'asset' => $this->whenLoaded('asset', function () {
                return [
                    'id' => $this->asset->id,
                    'asset_tag' => $this->asset->asset_tag,
                    'name' => $this->asset->name
                ];
            }),
            'manufacturer_part_number' => $this->manufacturer_part_number,
            'supplier_part_number' => $this->supplier_part_number,
            'minimum_order_quantity' => $this->minimum_order_quantity,
            'supplier_price' => $this->supplier_price,
            'supplier_currency' => $this->supplier_currency,
            'delivery_lead_time' => $this->delivery_lead_time,
            'delivery_terms' => $this->delivery_terms,
            'compatibility_info' => $this->compatibility_info,
            'substitute_items' => $this->substitute_items,
            'notes' => $this->notes,
            'is_preferred' => $this->is_preferred,
            'is_active' => $this->is_active,
            'stock_status' => $this->getStockStatusAttribute(),
            'transactions' => $this->whenLoaded('transactions', function () {
                return $this->transactions->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'transaction_type' => $transaction->transaction_type,
                        'quantity' => $transaction->quantity,
                        'unit_cost' => $transaction->unit_cost,
                        'total_cost' => $transaction->total_cost,
                        'transaction_date' => $transaction->transaction_date,
                        'remarks' => $transaction->remarks
                    ];
                });
            }),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s')
        ];
    }
}