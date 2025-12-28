<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranslatableResource extends JsonResource
{
    /**
     * Transform the resource into an array with locale-appropriate values.
     */
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);

        // Get translatable attributes from model
        $translatableAttrs = $this->resource->translatable ?? [];

        foreach ($translatableAttrs as $attr) {
            if (isset($data[$attr]) && is_array($data[$attr])) {
                // Replace array with translated string
                $data[$attr] = $data[$attr][app()->getLocale()] ?? $data[$attr]['en'] ?? null;
            }
        }

        return $data;
    }
}
