<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'image', 'description', 'category', 'component_category_id'];

    public function componentCategory(): BelongsTo
    {
        return $this->belongsTo(ComponentCategory::class, 'component_category_id');
    }

    public function imageUrl(): string
    {
        if (str_starts_with((string) $this->image, 'private/products/')) {
            return route('media.private', [
                'type' => 'products',
                'filename' => basename($this->image),
            ]);
        }

        return asset('images/'.basename((string) $this->image));
    }
}
