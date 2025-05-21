<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('metric_history_runs', function (Blueprint $table) {
            // Campos para métricas detalladas de rendimiento
            $table->decimal('first_contentful_paint', 10, 2)->nullable()->after('best_practices_metric');
            $table->decimal('largest_contentful_paint', 10, 2)->nullable()->after('first_contentful_paint');
            $table->decimal('cumulative_layout_shift', 10, 4)->nullable()->after('largest_contentful_paint');
            $table->decimal('total_blocking_time', 10, 2)->nullable()->after('cumulative_layout_shift');
            $table->decimal('time_to_interactive', 10, 2)->nullable()->after('total_blocking_time');
            $table->decimal('speed_index', 10, 2)->nullable()->after('time_to_interactive');
            $table->decimal('total_byte_weight', 10, 2)->nullable()->after('speed_index');
            
            // Campos para la experiencia de carga
            $table->string('loading_experience_metric', 50)->nullable()->after('total_byte_weight');
            $table->string('loading_experience_category', 50)->nullable()->after('loading_experience_metric');
            $table->integer('loading_experience_percentile')->nullable()->after('loading_experience_category');
            
            // Metadatos adicionales
            $table->string('lighthouse_version', 50)->nullable()->after('loading_experience_percentile');
            $table->string('final_url')->nullable()->after('lighthouse_version');
            $table->timestamp('analysis_utc_timestamp')->nullable()->after('final_url');
            
            // Índices para mejorar el rendimiento de las consultas
            $table->index('analysis_utc_timestamp');
            $table->index('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('metric_history_runs', function (Blueprint $table) {
            // Eliminar los campos agregados
            $table->dropColumn([
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
                'analysis_utc_timestamp'
            ]);
            
            // Eliminar índices
            $table->dropIndex(['analysis_utc_timestamp']);
            $table->dropIndex(['url']);
        });
    }
};
