<?php

namespace App\Http\Abilities;
use Illuminate\Support\Arr;
use App\Models\ConvertRequest;

trait FormatAbility
{
/*
     * Get formatted duration
     */
    public function getFormattedDuration ($duration)
    {
        if (empty($duration)) return null;
        
        $format_type = "minutes";
        $hours = 0;
        $minutes = 0;
        $seconds = 0;
        $duration_raw = $duration;
        $initial_division_by_minutes = $duration_raw / 60;
        
        /* If duration more than or equal an hour */
        if ($initial_division_by_minutes >= 60) {
            $format_type = "hours";
        }
        
        switch ($format_type) {
            case 'hours':
                $hour_raw = $initial_division_by_minutes / 60;
                $hours = floor($hour_raw);
                $decimal_minutes = $hour_raw - $hours;
                $minutes_raw = 0;
                if ($decimal_minutes != 0) {
                    $minutes_raw = $decimal_minutes * 60;
                    $minutes = floor($minutes_raw);
                }

                $decimal_seconds = $minutes_raw - $minutes;
                if ($decimal_seconds != 0) {
                    $seconds = round($decimal_seconds * 60);
                }
                break;
            case 'minutes':
            default:
                $minutes = floor($initial_division_by_minutes);
                $decimal_seconds = $initial_division_by_minutes - $minutes;
                if ($decimal_seconds != 0) {
                    $seconds = $decimal_seconds * 60;
                }

                break;
        }

        return vsprintf('%02d:%02d:%02d', [
            $hours,
            $minutes,
            $seconds
        ]);
    }

    public function formatFileSize($size, $precision = 2)
    {
        if ($size > 0) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');

            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        } else {

            return $size;
        }
    }
}
