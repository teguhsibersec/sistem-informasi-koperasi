<?php

namespace App\Exceptions;

use DomainException;

class StokTidakCukupException extends DomainException
{
    public function __construct(public string $namaBarang, public int $tersedia, public int $diminta)
    {
        parent::__construct("Stok {$namaBarang} tidak cukup (tersedia {$tersedia}, diminta {$diminta}).");
    }
}
