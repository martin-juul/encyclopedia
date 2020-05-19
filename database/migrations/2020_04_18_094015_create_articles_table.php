<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->text('title')->unique();

            $table->text('article_id')->unique();
            $table->text('article_parent_id')->nullable();

            $table->text('text')->nullable();

            $table->text('description')->nullable();

            $table->text('redirect')->nullable();
            $table->text('comment')->nullable();
            $table->bigInteger('contributor_id')->nullable();
            $table->text('contributor_username')->nullable();

            $table->timestampTz('revision_time');

            $table->text('sha1');

            $table->timestampsTz();
        });

        autogen_uuidv4('articles');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
}
