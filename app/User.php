<?php

namespace CVAdmin;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'acc_id',
        'nombre',
        'email',
        'login',
        'contrasenia',
        'avatar',
        'estado',
        'nick',
        'remember_token',

        'permisos',
        'crm',
        'asesor',
        'modulo_id',
        'fecha',
        'roles',
        'crm',
        'rold_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

}
