<?php

Route::group(['prefix' => 'articles'], function () {
    Route::get('', 'ArticleController@index')->name('api.articles.index');
    Route::get('{title}', 'ArticleController@show')->name('api.articles.show');

    Route::get('search', 'SearchController@articles')
        ->name('api.articles.search');
});
