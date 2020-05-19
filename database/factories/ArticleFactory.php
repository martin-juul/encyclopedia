<?php
declare(strict_types=1);

use App\Models\Article;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Article::class, function (Faker $faker) {
    $parent = Article::inRandomOrder()->first();
    $article_parent_id = null;

    if ($parent) {
        $article_parent_id = $parent->article_parent_id;
    }

    $text = $faker->paragraphs(6, true);

    return [
        'article_id'           => random_int(10000, 100000),
        'article_parent_id'    => $article_parent_id,
        'title'                => $faker->words(4, true),
        'description'          => $faker->words(16, true),
        'comment'              => $faker->words(15, true),
        'contributor_id'       => null,
        'contributor_username' => null,
        'redirect'             => null,
        'revision_time'        => now(),
        'sha1'                 => sha1($text),
        'text'                 => $text,
    ];
});
