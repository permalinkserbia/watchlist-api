<?php

namespace App;

interface MovieProvider
{
    public function findByImdbId(string $imdbId): array;
}
