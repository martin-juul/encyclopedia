<?php

Route::get('/articles/search', 'ArticleController@searchApi')
    ->name('api.articles.search');
