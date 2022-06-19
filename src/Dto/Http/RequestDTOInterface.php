<?php

namespace App\Dto\Http;

use Symfony\Component\HttpFoundation\Request;

interface RequestDTOInterface
{
    public function __construct(Request $request);

    public function asObject(): object;
}