<?php

it('redirects the home page to the article list', function () {
    $this->get('/')->assertRedirect(route('articles.index'));
});
