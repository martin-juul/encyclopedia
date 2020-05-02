<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoryArticleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('category_article', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('category_id')->index();
            $table->foreign('category_id')
                ->references('id')->on('categories')
                ->cascadeOnDelete()
                ->deferrable();

            $table->uuid('article_id')->index();
            $table->foreign('article_id')
                ->references('id')->on('articles')
                ->cascadeOnDelete()
                ->deferrable();

            $table->timestampsTz();

            $table->index(['category_id', 'article_id']);
        });

        autogen_uuidv4('category_article');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('category_article');
    }
}
