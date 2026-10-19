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

        // La activación de cobros se controla estrictamente desde la base de datos (system_configs)
        if (in_array($key, ['payments_enabled', 'enabled'])) {
            return $default ?? 'true';
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
     * Lee de forma exclusiva la configuración en la base de datos MySQL (tabla system_configs, clave 'payments_enabled').
     * Si no existe registro aún en base de datos, retorna true por defecto.
     */
    public static function isStripeEnabled(): bool
    {
        try {
            $config = self::whereIn('key', ['payments_enabled', 'enabled'])->first();
            if ($config && trim((string)$config->value) !== '') {
                return filter_var($config->value, FILTER_VALIDATE_BOOLEAN);
            }
        } catch (\Exception $e) {
            // Manejo silencioso en caso de error temporal de BD
        }

        return true;
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
