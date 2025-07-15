<?php

if (!function_exists('calculateDistance')) {
    /**
     * Menghitung jarak antara dua titik koordinat (latitude, longitude)
     * menggunakan formula Haversine. Hasil dalam meter.
     *
     * @param float $lat1 Latitude titik pertama
     * @param float $lon1 Longitude titik pertama
     * @param float $lat2 Latitude titik kedua
     * @param float $lon2 Longitude titik kedua
     * @return float Jarak dalam meter
     */
    function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}