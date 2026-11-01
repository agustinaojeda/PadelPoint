<?php

test('guests are redirected from the root to the login page', function () {
    $this->get('/')->assertRedirect('/login');
});
