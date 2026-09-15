<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tercero extends Model
{
    protected $table = 'terceros';
    protected $appends = ['nombre_completo'];

    protected $fillable = [
        'tipo',
        'nombre',
        'apellido',
        'cedula',
        'razon_social',
        'nit',
        'email',
        'celular',
        'direccion',
        'ciudad',
        // Código DANE/DIVIPOLA del municipio — Factus lo exige para
        // cualquier cliente que no sea "Consumidor Final" (ver migración
        // add_codigo_municipio_a_terceros_table). Tabla oficial:
        // https://www.dane.gov.co/index.php/estadisticas-por-tema/organizacion-territorial/divipola-codigos-municipios
        'codigo_municipio',
        'regimen_tributario',
        'codigo_ciiu',
        'estado',
        // Plazo de pago pactado para ventas a crédito con este cliente. Si
        // se deja vacío, la venta usa la política general por defecto (ver
        // FacturacionController::DIAS_CREDITO_POR_DEFECTO).
        'dias_credito',
    ];

    /** Valores válidos para régimen_tributario (exógena DIAN, Formato 1001). */
    public const REGIMENES_TRIBUTARIOS = [
        'no_responsable_iva' => 'No responsable de IVA',
        'responsable_iva' => 'Responsable de IVA',
        'gran_contribuyente' => 'Gran Contribuyente',
        'autorretenedor' => 'Autorretenedor',
        'regimen_simple' => 'Régimen Simple de Tributación',
    ];

    // =========================================
    // RELACIONES
    // =========================================

    public function ajustes()
    {
        return $this->hasMany(Ajuste::class);
    }

    // =========================================
    // ACCESOR (NOMBRE COMPLETO)
    // =========================================

    public function getNombreCompletoAttribute()
    {
        return $this->tipo === 'persona'
            ? "{$this->nombre} {$this->apellido}"
            : $this->razon_social;
    }

    /**
     * Deja una identificación (NIT o cédula) en solo dígitos, sin el guion
     * ni el DV — formato que exige Factus (y cualquier otro proveedor DIAN).
     * Antes esto solo se aplicaba al enviar la factura; el dato guardado en
     * `terceros` seguía como el usuario lo hubiera escrito (con puntos,
     * espacios o el DV pegado), así que otros reportes que leyeran el NIT
     * directo heredaban esa inconsistencia.
     */
    public static function soloDigitos(string $identificacion): string
    {
        $limpio = preg_replace('/[^\d-]/', '', $identificacion);

        return explode('-', $limpio)[0];
    }
}