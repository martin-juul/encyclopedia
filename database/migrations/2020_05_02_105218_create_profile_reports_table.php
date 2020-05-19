<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfileReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('profile_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('category');

            $table->jsonb('context');
            $table->jsonb('xhprof');

            $table->timestampsTz();

            $table->bigInteger('cpu')->nullable();
            $table->bigInteger('wall_time')->nullable();
            $table->bigInteger('memory_usage')->nullable();
            $table->bigInteger('peak_memory_usage')->nullable();
        });

        autogen_uuidv4('profile_reports');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
}
