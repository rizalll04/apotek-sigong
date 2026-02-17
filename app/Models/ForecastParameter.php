<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForecastParameter extends Model
{
    use HasFactory;

    protected $table = 'forecast_parameters';
    protected $primaryKey = 'id';

    protected $fillable = [
        'product_id',
        'optimized_alpha',
        'optimized_beta',
        'optimized_gamma',
        'mape_value',
        'data_quality_warning',
    ];

    protected $casts = [
        'optimized_alpha' => 'float',
        'optimized_beta' => 'float',
        'optimized_gamma' => 'float',
        'mape_value' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Belongs to Produk model
     */
    public function product()
    {
        return $this->belongsTo(Produk::class, 'product_id', 'id');
    }
}
