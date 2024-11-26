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
        Schema::create('metric_history_runs', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->decimal('accessibility_metric', 5, 2)->nullable();
            $table->decimal('pwa_metric', 5, 2)->nullable();
            $table->decimal('performance_metric', 5, 2)->nullable();
            $table->decimal('seo_metric', 5, 2)->nullable();
            $table->decimal('best_practices_metric', 5, 2)->nullable();
            $table->unsignedBigInteger('strategy_id'); // Declarar como unsignedBigInteger
            $table->foreign('strategy_id')->references('id')->on('strategies')->onDelete('cascade'); // Definir relación manualmente
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metric_history_runs');
    }
};
