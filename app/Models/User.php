<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'site_name', 'stripe_customer_id', 'stripe_subscription_id', 'subscription_status', 'subscription_ends_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscription_ends_at' => 'datetime',
        ];
    }

    /**
     * Verifica si el usuario cuenta con acceso autorizado al sistema.
     * El Administrador Máximo (rol master) ingresa siempre sin necesidad de pago.
     * Si los cobros con Stripe están desactivados en el sistema (.env o BD),
     * todos los usuarios tienen acceso libre sin pasar por el muro de pago.
     * Si los cobros están activos, los usuarios con rol editor o viewer requieren suscripción activa.
     */
    public function hasActiveSubscription(): bool
    {
        if ($this->role === 'master') {
            return true;
        }

        // Si los cobros de Stripe están desactivados (.env: STRIPE_PAYMENTS_ENABLED=false)
        if (!SystemConfig::isStripeEnabled()) {
            return true;
        }

        // Si su estado es de prueba y ya pasó la fecha límite
        if (in_array($this->subscription_status, ['trialing', 'trialling']) && $this->subscription_ends_at && $this->subscription_ends_at->isPast()) {
            return false;
        }

        return in_array($this->subscription_status, ['active', 'trialing', 'trialling']);
    }
}

