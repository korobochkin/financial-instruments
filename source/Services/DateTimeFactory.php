<?php
namespace Korobochkin\FinancialInstruments\Services;

class DateTimeFactory
{
    /**
     * @see https://github.com/Rarst/wpdatetime/blob/master/src/WpDateTimeZone.php
     *
     * @throws \Exception If time zone was not detected.
     *
     * @return \DateTimeZone
     */
    public static function createTimeZone()
    {
        $timezoneString = get_option('timezone_string');

        if (!empty($timezoneString)) {
            return new \DateTimeZone($timezoneString);
        }

        $offset  = get_option('gmt_offset');
        $hours   = (int) $offset;
        $minutes = abs(($offset - (int) $offset) * 60);
        $offset  = sprintf('%+03d:%02d', $hours, $minutes);

        return new \DateTimeZone($offset);
    }
}
