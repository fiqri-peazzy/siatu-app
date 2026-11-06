<?php

it('endpoint versi publik, tidak di-cache, dan sama dengan meta di halaman', function () {
    $res = $this->get('/versi')->assertOk()->assertJsonStructure(['versi']);
    expect($res->headers->get('Cache-Control'))->toContain('no-store');

    $versi = $res->json('versi');
    $this->get('/login')->assertSee('<meta name="siatu-versi" content="'.$versi.'">', false);
});
