<?php

test('guest visiting the root is redirected to login', function () {
    $this->get('/')->assertRedirect();
});
