<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;

class LoginLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'cedula',
        'ip_address',
        'user_agent',
        'platform',
        'browser',
        'device_type',
        'country',
        'region',
        'city',
        'isp',
        'latitude',
        'longitude',
        'status',
        'failure_reason',
        'logged_in_at',
    ];

    protected function casts(): array
    {
        return [
            'logged_in_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Parsear información del User-Agent.
     */
    public static function parseUserAgent(string $userAgent): array
    {
        return [
            'platform' => self::detectPlatform($userAgent),
            'browser' => self::detectBrowser($userAgent),
            'device_type' => self::detectDeviceType($userAgent),
        ];
    }

    /**
     * Obtener ubicación geográfica a partir de la IP.
     */
    public static function getGeoLocation(string $ip): array
    {
        // IPs locales no tienen geolocalización
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost']) || str_starts_with($ip, '192.168.') || str_starts_with($ip, '10.')) {
            return [
                'country' => 'Local',
                'region' => 'Local',
                'city' => 'Local',
                'isp' => 'Local',
                'latitude' => null,
                'longitude' => null,
            ];
        }

        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?lang=es&fields=status,country,regionName,city,isp,lat,lon");

            if ($response->successful() && $response->json('status') === 'success') {
                $data = $response->json();
                return [
                    'country' => $data['country'] ?? null,
                    'region' => $data['regionName'] ?? null,
                    'city' => $data['city'] ?? null,
                    'isp' => $data['isp'] ?? null,
                    'latitude' => $data['lat'] ?? null,
                    'longitude' => $data['lon'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            // Si falla la geolocalización, no bloquear el login
        }

        return [
            'country' => null,
            'region' => null,
            'city' => null,
            'isp' => null,
            'latitude' => null,
            'longitude' => null,
        ];
    }

    private static function detectPlatform(string $ua): string
    {
        $platforms = [
            'Windows 11' => 'Windows NT 10.0; Win64',
            'Windows 10' => 'Windows NT 10.0',
            'Windows 8.1' => 'Windows NT 6.3',
            'Windows 7' => 'Windows NT 6.1',
            'macOS' => 'Macintosh',
            'Linux' => 'Linux',
            'Android' => 'Android',
            'iOS' => ['iPhone', 'iPad'],
            'Chrome OS' => 'CrOS',
        ];

        foreach ($platforms as $name => $signature) {
            if (is_array($signature)) {
                foreach ($signature as $sig) {
                    if (str_contains($ua, $sig)) return $name;
                }
            } else {
                if (str_contains($ua, $signature)) return $name;
            }
        }

        return 'Desconocido';
    }

    private static function detectBrowser(string $ua): string
    {
        $browsers = [
            'Edge' => 'Edg/',
            'Opera' => ['OPR/', 'Opera'],
            'Chrome' => 'Chrome/',
            'Firefox' => 'Firefox/',
            'Safari' => 'Safari/',
            'IE' => ['MSIE', 'Trident/'],
            'Postman' => 'PostmanRuntime',
            'Insomnia' => 'insomnia',
            'curl' => 'curl/',
        ];

        foreach ($browsers as $name => $signature) {
            if (is_array($signature)) {
                foreach ($signature as $sig) {
                    if (str_contains($ua, $sig)) return $name;
                }
            } else {
                if (str_contains($ua, $signature)) return $name;
            }
        }

        return 'Desconocido';
    }

    private static function detectDeviceType(string $ua): string
    {
        if (preg_match('/Mobile|Android.*Mobile|iPhone/i', $ua)) return 'mobile';
        if (preg_match('/iPad|Android(?!.*Mobile)|Tablet/i', $ua)) return 'tablet';
        return 'desktop';
    }
}
