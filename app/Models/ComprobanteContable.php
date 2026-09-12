<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprobanteContable extends Model
{
    protected $table = 'comprobantes_contables';

    protected $fillable = [
        'tipo_documento_contable_id',
        'tipo','prefijo','tercero_id','descripcion','total_debito','total_credito',
        'documento_id',
        'numero',
        'fecha',
        'observacion',
        'payload',
        'usuario_id',
        'documento_origen',
        'documento_origen_id',
        'proceso_contable_id',
        'referencia_grupo',
        'estado','registrado_por','registrado_at','anulado_por','anulado_at','motivo_anulacion','comprobante_reversion_id'
    ];

    protected $casts = [
        'fecha' => 'datetime',
        'payload' => 'array',
        'registrado_at' => 'datetime',
        'anulado_at' => 'datetime',
    ];

    /**
     * Punto único de control de períodos cerrados: sin importar cuál de los
     * varios servicios contables (AccountingService, ContabilidadService,
     * CompraContableService, ClienteContableService, AjusteContableService,
     * TesoreriaService...) esté creando o modificando el comprobante, todos
     * pasan por Eloquent create()/update(), así que este hook los cubre a
     * todos sin duplicar la validación en cada uno.
     *
     * Excepción: las actualizaciones hechas con el query builder estático
     * (Modelo::where(...)->update(...)) NO disparan estos eventos — los
     * pocos sitios que anulan así (AjusteContableService, CompraContableService)
     * llevan su propia validación explícita antes de esa llamada.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $comprobante) {
            if ($comprobante->fecha) {
                app(\App\Services\PeriodoContableService::class)->assertAbierto($comprobante->fecha);
            }
        });

        static::updating(function (self $comprobante) {
            if ($comprobante->fecha) {
                app(\App\Services\PeriodoContableService::class)->assertAbierto($comprobante->fecha);
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumentoContable::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientoContable::class);
    }

    public function documento()
    {
        return $this->belongsTo(Documento::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Métodos útiles
    |--------------------------------------------------------------------------
    */

    public function getTotalDebitoAttribute()
    {
        return $this->movimientos()->sum('debito');
    }

    public function getTotalCreditoAttribute()
    {
        return $this->movimientos()->sum('credito');
    }

    public function estaCuadrado(): bool
    {
        return round($this->total_debito, 2) === round($this->total_credito, 2);
    }

    public function procesoContable()
    {
        return $this->belongsTo(ProcesoContable::class, 'proceso_contable_id');
    }
    public function tercero() { return $this->belongsTo(Tercero::class); }
    public function registradoPor() { return $this->belongsTo(User::class, 'registrado_por'); }
    public function anuladoPor() { return $this->belongsTo(User::class, 'anulado_por'); }
    public function reversion() { return $this->belongsTo(self::class, 'comprobante_reversion_id'); }
}
