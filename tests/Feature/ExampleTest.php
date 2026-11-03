<?php

test('guests can see the home page', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('home/index'));
});
