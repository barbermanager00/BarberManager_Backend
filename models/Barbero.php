<?php

/**
 * MODELO BARBERO
 * ==============
 * Representa un barbero en la base de datos.
 * Este modelo hereda de Eloquent Model, permitiendo interactuar
 * con la tabla 'barberos' de forma orientada a objetos.
 */

use Illuminate\Database\Eloquent\Model;

class Barbero extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'barberos';

    // Campos que pueden ser asignados mediante mass assignment
    // Estos campos se pueden llenar directamente al crear un barbero
    protected $fillable = ['barberia_id', 'nombre', 'email', 'telefono', 'especialidad', 'experiencia', 'estado'];

    // Desactivar timestamps automáticos
    public $timestamps = false;

    /**
     * Relación inversa con Barberia
     * Un barbero pertenece a una barbería
     * Retorna la barbería a la que está asignado este barbero
     */
    public function barberia()
    {
        // belongsTo(Modelo, clave_foranea, clave_local)
        // Un barbero pertenece a una barbería a través de barberia_id
        return $this->belongsTo('Barberia', 'barberia_id', 'id');
    }
}
