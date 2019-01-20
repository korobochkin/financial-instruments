<?php
namespace Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames;

class SmartDateInterval extends \DateInterval
{
    const FORMAT = 'P%yY%mM%dDT%hH%iM%sS';

    /**
     * @var string
     */
    protected $label;

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->format(self::FORMAT);
    }
}
