<?php

/**
 * MODELO BARBERIA
 * ===============
 * Representa una barbería en la base de datos.
 * Este modelo hereda de la clase Eloquent Model, que nos permite
 * interactuar con la tabla 'barberias' de forma orientada a objetos.
 */

use Illuminate\Database\Eloquent\Model;

class Barberia extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'barberias';

    // Campos que pueden ser asignados mediante mass assignment (creación rápida)
    protected $fillable = ['nombre', 'email', 'telefono', 'direccion', 'estado'];

    // Desactivar timestamps automáticos (created_at, updated_at)
    // Ya usamos timestamp en SQL manualmente
    public $timestamps = false;

    /**
     * Relación con Barberos
     * Una barbería tiene muchos barberos
     * Retorna una colección de Barberos vinculados a esta barbería
     */
    public function barberos()
    {
        // hasMany(Modelo, clave_foranea, clave_local)
        // Una barbería tiene muchos barberos a través de barberia_id
        return $this->hasMany('Barbero', 'barberia_id', 'id');
    }
}
