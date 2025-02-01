<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('emotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('ユーザーID');
            $table->string('emotion_type')->comment('感情タイプ（例: 喜び、悲しみなど）');
            $table->float('confidence')->comment('分析結果の信頼度');
            $table->float('intensity')->comment('感情の強度');
            $table->text('text')->nullable()->comment('感情を記録したテキスト');
            $table->date('recorded_date')->comment('記録日');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emotions');
    }
};
