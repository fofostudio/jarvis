<?php



namespace App;


use Image;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Phattarachai\LaravelMobileDetect\Agent;
use Illuminate\Support\Facades\DB;


class Helper
{
    public static function formatDate($date, $time = false)
    {
        if (empty($date)) {
            return 'N/A';
        }

        if ($time == false) {
            $date = strtotime($date);
        }

        $day    = date('d', $date);
        $_month = date('m', $date);
        $month  = __("months.$_month");
        $year   = date('Y', $date);

        $dateFormat = config('settings.date_format', 'd/m/Y'); // Valor por defecto si no está definido

        switch ($dateFormat) {
            case 'M d, Y':
                return $month.' '.$day.', '.$year;
            case 'd M, Y':
                return $day.' '.$month.', '.$year;
            default:
                return date($dateFormat, $date);
        }
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date, $time = false) {
        return Helper::formatDate($date, $time);
    }
}
