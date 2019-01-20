<?php
namespace Korobochkin\FinancialInstruments\Options\Fixer;

use Korobochkin\FinancialInstruments\Plugin;
use Korobochkin\WPKit\Options\AbstractOption;
use Symfony\Component\Validator\Constraints;

class FixerBaseCurrencyOption extends AbstractOption
{
    public function __construct()
    {
        $this
            ->setName(Plugin::_NAME_ . '_fixer_base_currency')
            ->setDefaultValue('EUR');
    }

    /**
     * @inheritdoc
     */
    public function buildConstraint()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Type(array('type' => 'string')),
        );
    }
}
