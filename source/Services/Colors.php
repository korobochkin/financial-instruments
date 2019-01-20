<?php
namespace Korobochkin\FinancialInstruments\Services;

class Colors
{
    /**
     * @param $hex
     * @return array
     */
    public static function hex2rgb($hex)
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return array($r, $g, $b);
    }

    /**
     * @param $hex
     * @param $opacity
     * @return array
     */
    public static function hex2rgba($hex, $opacity)
    {
        $rgba   = self::hex2rgb($hex);
        $rgba[] = (int) $opacity / 100;

        return $rgba;
    }

    /**
     * @param array $rgba
     * @return string
     */
    public static function rgbaAsString(array $rgba)
    {
        return implode(',', $rgba);
    }
}
