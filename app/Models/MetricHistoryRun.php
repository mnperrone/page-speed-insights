<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetricHistoryRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'accessibility_metric',
        'pwa_metric',
        'performance_metric',
        'seo_metric',
        'best_practices_metric',
        'first_contentful_paint',
        'largest_contentful_paint',
        'cumulative_layout_shift',
        'total_blocking_time',
        'time_to_interactive',
        'speed_index',
        'total_byte_weight',
        'loading_experience_metric',
        'loading_experience_category',
        'loading_experience_percentile',
        'lighthouse_version',
        'final_url',
        'analysis_utc_timestamp',
        'strategy_id',
    ];
    
    protected $dates = [
        'analysis_utc_timestamp',
        'created_at',
        'updated_at'
    ];

    public function strategy()
    {
        return $this->belongsTo(Strategy::class);
    }
}
