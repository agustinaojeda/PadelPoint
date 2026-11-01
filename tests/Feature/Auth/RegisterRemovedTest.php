<?php

test('the registration routes no longer exist', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register')->assertNotFound();
});
