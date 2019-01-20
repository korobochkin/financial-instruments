<?php
namespace Korobochkin\FinancialInstruments\Services;

use Korobochkin\FinancialInstruments\Models\Currency;

class Text
{

    public static function formatPlusMinusSigns($number, $formatted_number)
    {
        if ($number > 0) {
            $formatted_number = '+' . $formatted_number;
        } elseif ($number < 0) {
            $formatted_number = str_replace('-', '&ndash;', $formatted_number);
        }
        return $formatted_number;
    }

    public static function numberFormatI18nPlusMinus($number, $decimals = 2)
    {
        $number   = (float) $number;
        $decimals = absint($decimals);

        $formatted_number = number_format_i18n($number, $decimals);

        $formatted_number = self::formatPlusMinusSigns($number, $formatted_number);

        return $formatted_number;
    }

    /**
     * Some of currencies (units) are very small. For example 1 US dollar (USD) = 0.0026528435830000001 bitcoins (BTC).
     * Sometimes we round this to 0.00. To avoid this small currencies (units) recalculated by multiplying "small"
     * number by 1000 or 1000000. And after this: 1000 USD = 0.26528435830000001 BTC
     *
     * @param Currency $currency_obj
     *
     * @return array Filtered values
     */
    public static function currencyInfoForRound(Currency $currency_obj, $preciese = 2)
    {
        $out = array(
            'rate' => $currency_obj->get_rate(),
            'per' => 1,
            'change_percentage' => $currency_obj->get_change_percentage(),
            'change' => $currency_obj->get_change(),
            'trend' => $currency_obj->get_trend()
        );

        $out['change_percentage'] = (float) round($out['change_percentage'], $preciese);

        if ($out['rate'] >= 0.01) {
        } elseif ($out['rate'] >= 0.001) {
            $out['rate']   = $out['rate'] * 1000;
            $out['per']    = 1000;
            $out['change'] = $out['change'] * 1000;
        } elseif ($out['rate'] >= 0.000001) {
            $out['rate']   = $out['rate'] * 1000000;
            $out['per']    = 1000000;
            $out['change'] = $out['change'] * 1000000;
        } elseif ($out['rate'] >= 0.00000001) {
            $out['rate']   = $out['rate'] * 100000000;
            $out['per']    = 100000000;
            $out['change'] = $out['change'] * 100000000;
        }

        if ($out['change_percentage'] === 0.0) {
            $out['trend']  = 'flat';
            $out['change'] = 0.0;
        }

        return $out;
    }

    public static function addRightOrLeft($string, $add, $side = true)
    {
        if (is_string($string) || is_numeric($string)) {
            if (( is_string($add) || is_numeric($add) )) {
                if (( is_bool($side) && $side == true ) || ( is_string($side) && $side == 'right' )) {
                    $string .= $add;
                } else {
                    $string = $add . $string;
                }
            }
            return $string;
        }
        return false;
    }
}
