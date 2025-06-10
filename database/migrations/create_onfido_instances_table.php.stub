<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('onfido_instances', function (Blueprint $table) {
            $table->id();
            if (config('onfido.database.primary_key') == "uuid") {
                $table->nullableUuidMorphs('model');
            }
            else {
                $table->nullableMorphs('model');
            }
            $table->string('applicant_id')->nullable();
            $table->string('workflow_run_id')->nullable();
            $table->string('workflow_id')->nullable();
            $table->string('sdk_token')->nullable();
            $table->boolean('started')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->boolean('verified')->nullable()->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('onfido_instances');
    }
};
