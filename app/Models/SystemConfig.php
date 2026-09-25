<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemConfig extends Model
{
    protected $table = 'system_configs';
    protected $fillable = ['key', 'value'];

    /**
     * Obtener el valor de configuración priorizando la base de datos MySQL,
     * y haciendo fallback al archivo de entorno (.env o config).
     */
    public static function getValue(string $key, $default = null)
    {
        try {
            $config = self::where('key', $key)->first();
            if ($config && trim((string)$config->value) !== '') {
                return trim((string)$config->value);
            }
        } catch (\Exception $e) {
            // Si hay error de conexión al consultar DB, seguir a .env
        }

        // Fallback hacia variable del .env (ej. STRIPE_PK, STRIPE_SUBSCRIPTION_AMOUNT)
        $envKey = 'STRIPE_' . strtoupper($key);
        $envValue = env($envKey);

        if ($envValue === null) {
            $envValue = config('services.stripe.' . strtolower($key), $default);
        }

        if ($envValue !== null && trim((string)$envValue) !== '') {
            return is_bool($envValue) ? ($envValue ? 'true' : 'false') : trim((string)$envValue);
        }

        return $default;
    }

    /**
     * Verifica si los pagos con Stripe y el paywall están activados.
     * Prioriza la configuración en BD (claves 'payments_enabled' o 'enabled')
     * y como fallback consulta las variables de entorno STRIPE_PAYMENTS_ENABLED o STRIPE_ENABLED.
     */
    public static function isStripeEnabled(): bool
    {
        try {
            $config = self::whereIn('key', ['payments_enabled', 'enabled'])->first();
            if ($config && trim((string)$config->value) !== '') {
                return filter_var($config->value, FILTER_VALIDATE_BOOLEAN);
            }
        } catch (\Exception $e) {
            // Fallback a config / env si falla la base de datos
        }

        $val = config('services.stripe.payments_enabled');
        if ($val === null) {
            $val = env('STRIPE_PAYMENTS_ENABLED');
        }
        if ($val === null) {
            $val = env('STRIPE_ENABLED', true);
        }

        return filter_var($val, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Guardar o actualizar la configuración en la tabla de MySQL.
     */
    public static function setValue(string $key, $value): self
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
