<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class HolidayService
{
    /**
     * Mengambil daftar Hari Libur Nasional Indonesia dari API Indonesia
     * (Di-cache selama 30 hari untuk performa)
     */
    public static function getHolidays($year = null)
    {
        $year = $year ?: date('Y');
        $cacheKey = "holidays_indo_{$year}";

        return Cache::remember($cacheKey, 60 * 60 * 24 * 30, function () use ($year) {
            try {
                $apiKey = env('API_INDONESIA_KEYS');
                if (!$apiKey) {
                    Log::warning("API_INDONESIA_KEYS is not set in .env");
                    return [];
                }

                $response = Http::withHeaders([
                    'x-api-key' => $apiKey
                ])->timeout(10)->get("https://use.apiindonesia.id/api/v1/libur?tahun={$year}");
                
                if ($response->successful() && is_array($response->json('data'))) {
                    $data = $response->json('data');
                    $holidays = [];
                    foreach ($data as $item) {
                        if (isset($item['date'])) {
                            // API format is usually YYYY-MM-DD
                            $holidays[$item['date']] = $item['name'] ?? 'Libur Nasional';
                        }
                    }
                    return $holidays;
                } else {
                    Log::error("API Indonesia Error: " . $response->body());
                }
            } catch (\Exception $e) {
                Log::error("Failed to fetch holidays from API Indonesia: " . $e->getMessage());
            }

            // Jika API mati/gagal, return array kosong agar sistem tetap berjalan
            return [];
        });
    }

    /**
     * Mengecek apakah suatu tanggal adalah hari libur (Sabtu, Minggu, atau Libur Nasional)
     */
    public static function isHoliday(Carbon $date)
    {
        // Berdasarkan instruksi: "Senin - Jumat saja", maka Sabtu dan Minggu dianggap libur
        if ($date->isWeekend()) { 
            return true;
        }

        $holidays = self::getHolidays($date->year);
        return array_key_exists($date->format('Y-m-d'), $holidays);
    }
    
    /**
     * Menghitung total hari efektif (Senin-Jumat dikurangi hari libur nasional)
     */
    public static function getEffectiveWorkDays(Carbon $startDate, Carbon $endDate)
    {
        $days = 0;
        $current = $startDate->copy();
        
        $holidays = [];
        for ($y = $startDate->year; $y <= $endDate->year; $y++) {
            $holidays = array_merge($holidays, self::getHolidays($y));
        }

        while ($current->lte($endDate)) {
            // Jika bukan Sabtu/Minggu dan bukan hari libur nasional
            if (!$current->isWeekend() && !array_key_exists($current->format('Y-m-d'), $holidays)) {
                $days++;
            }
            $current->addDay();
        }
        
        return $days;
    }
}
