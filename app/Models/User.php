<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'plaza',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function plazaRef()
    {
        return $this->belongsTo(Plaza::class, 'plaza', 'plaza');
    }

    public function getPlazaNombreAttribute()
    {
        return $this->plazaRef ? "{$this->plaza} - {$this->plazaRef->plaza_nom}" : $this->plaza;
    }

    public function tiendas()
    {
        return $this->belongsToMany(Tienda::class, 'user_tienda');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'user_modules');
    }

    /**
     * Verificar si el usuario tiene acceso a un módulo
     */
    public function hasModuleAccess($moduleName): bool
    {
        // Los administradores tienen acceso a todos los módulos
        if ($this->isAdmin()) {
            return true;
        }

        return $this->modules()
            ->where('name', $moduleName)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Verificar si el usuario tiene acceso a una ruta
     */
    public function hasRouteAccess($routeName): bool
    {
        // Los administradores tienen acceso a todas las rutas
        if ($this->isAdmin()) {
            return true;
        }

        return $this->modules()
            ->where('route_name', $routeName)
            ->where('is_active', true)
            ->exists();
    }
}









