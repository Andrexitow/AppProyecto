<?php

namespace App\DataTransferObjects;

class ContabilidadData
{
    public function __construct(

        // Módulo origen
        public ?string $modulo = null,

        // Valores
        public array $valores = [],

        // Tercero
        public ?int $terceroId = null,

        // Usuario
        public ?int $usuarioId = null,

        // Documento
        public string $documento = '',

        public int $documentoId = 0,

        public ?string $observacion = null

    ) {}

    public function valor(string $clave): float
    {
        return (float) ($this->valores[$clave] ?? 0);
    }
}