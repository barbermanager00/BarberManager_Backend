<?php
/**
 * MODELO TURNO
 * ============
 * Representa un turno (cita) agendada en la base de datos.
 * Un turno es una reserva de un cliente con un barbero en una fecha y hora.
 * Este modelo hereda de Eloquent Model.
 */

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'turnos';
    
    // Campos que pueden ser asignados mediante mass assignment
    // Estos son los datos que se pueden llenar directamente al crear un turno
    protected $fillable = ['clienteNombre', 'clienteTelefono', 'barberoId', 'fecha', 'hora', 'servicio'];
    
    // Desactivar timestamps automáticos
    // Ya usamos created_at en SQL manualmente
    public $timestamps = false;

    /**
     * Relación con Barbero
     * Un turno pertenece a un barbero
     * Retorna el barbero asignado a este turno
     */
    public function barbero()
    {
        // belongsTo(Modelo, clave_foranea, clave_local)
        // Un turno pertenece a un barbero a través de barberoId
        return $this->belongsTo('Barbero', 'barberoId', 'id');
    }
}

