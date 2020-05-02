<?php

Route::get('/job', function () {
    $path = '/Volumes/Dev/personal/encyclopedia/storage/dumps/enwiki-20200401-pages-articles-multistream.xml';

    dd(Queue::push(new \App\Jobs\WikipediaArticleImportJob($path)));

});

Route::get('/', 'ArticleController@index')
    ->middleware('profile')
    ->name('articles.index');

Route::get('/-/statistics', 'StatisticController@index')
    ->name('statistics.index');

Route::get('/-/profiles/{id}', 'StatisticController@showProfile')
    ->name('statistics.profile.show');

Route::get('/-/search', 'ArticleController@searchView')
    ->name('articles.search');

Route::get('{title}/source', 'ArticleController@showSource')
    ->name('articles.show-source');

Route::get('{title}', 'ArticleController@show')
    ->middleware('cache.response')
    ->name('articles.show');

Auth::routes();
