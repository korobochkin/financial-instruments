<?php
namespace Korobochkin\FinancialInstruments\Options\Fixer;

use Korobochkin\FinancialInstruments\Plugin;
use Korobochkin\WPKit\Options\AbstractOption;
use Symfony\Component\Validator\Constraints;

class FixerSymbolsOption extends AbstractOption
{
    public function __construct()
    {
        $this
            ->setName(Plugin::_NAME_ . '_fixer_symbols')
            ->setDefaultValue(array('CAD', 'EUR', 'GBP', 'XAU'));
    }

    /**
     * @inheritdoc
     */
    public function buildConstraint()
    {
        return array(
            new Constraints\NotBlank(),
            new Constraints\Type(array('type' => 'array')),
            new Constraints\All(array(
                'constraints' => array(
                    new Constraints\NotBlank(),
                    new Constraints\Type(array('type' => 'string')),
                ),
            ))
        );
    }
}
