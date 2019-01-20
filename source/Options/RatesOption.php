<?php
namespace Korobochkin\FinancialInstruments\Options;

use Korobochkin\FinancialInstruments\Plugin;
use Korobochkin\WPKit\Options\Special\AbstractArrayOption;
use Symfony\Component\Validator\Constraints;

class RatesOption extends AbstractArrayOption
{
    public function __construct()
    {
        $this
            ->setName(Plugin::_NAME_ . '_rates')
            ->setDefaultValue(array(
                array(
                    'timestamp' => 1535673599,
                    'historical' => true,
                    'base' => 'EUR',
                    'date' => '2018-08-30',
                    'rates' => array(
                        'USD' => 1.166432,
                        'AUD' => 1.606352,
                        'CAD' => 1.514782,
                        'PLN' => 4.300794,
                        'MXN' => 22.283047
                    )
                ),
                array(
                    'timestamp' => 1535759999,
                    'base' => 'EUR',
                    'date' => '2018-08-31',
                    'rates' => array(
                        'USD' => 1.162149,
                        'AUD' => 1.616089,
                        'CAD' => 1.516314,
                        'PLN' => 4.310066,
                        'MXN' => 22.178569
                    ),
                ),
            ));
    }

    /**
     * @inheritdoc
     */
    public function buildConstraint()
    {
        return new Constraints\All(array(
            'constraints' => new Constraints\Collection(array(
                'fields' => array(
                    'timestamp' => array(
                        new Constraints\Type(array('type' => 'int')),
                        new Constraints\Range(array('min' => 0)),
                    ),
                    'base' => new Constraints\Type(array('type' => 'string')),
                    'date' => new Constraints\Date(),
                    'rates' => new Constraints\All(array(
                        new Constraints\NotBlank(),
                        new Constraints\Type(array('type' => 'numeric')),
                        new Constraints\Range(array('min' => 0)),
                    )),
                ),
                'allowExtraFields' => true,
            ))
        ));
    }
}
