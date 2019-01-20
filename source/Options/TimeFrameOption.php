<?php
namespace Korobochkin\FinancialInstruments\Options;

use Korobochkin\FinancialInstruments\Plugin;
use Korobochkin\WPKit\Options\AbstractOption;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateIntervalToStringTransformer;
use Symfony\Component\Validator\Constraints;

class TimeFrameOption extends AbstractOption
{
    /**
     * TimeFrameOption constructor.
     * @throws \Exception when the interval_spec cannot be parsed as an interval.
     */
    public function __construct()
    {
        $this
            ->setName(Plugin::_NAME_ . '_time_frame')
            ->setDefaultValue(new \DateInterval('P1D')) // One day
            ->setDataTransformer(new DateIntervalToStringTransformer());
    }

    /**
     * @inheritdoc
     */
    public function buildConstraint()
    {
        return array(
            new Constraints\NotNull(),
            new Constraints\Type(
                array(
                    'type' => \DateInterval::class,
                )
            ),
        );
    }
}
