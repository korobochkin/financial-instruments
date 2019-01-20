<?php
namespace Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings;

use Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames\OneDayDateInterval;
use Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames\OneMonthDateInterval;
use Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames\OneWeekDateInterval;
use Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames\OneYearDateInterval;
use Korobochkin\FinancialInstruments\Plugin;
use Korobochkin\FinancialInstruments\Services\Constraints\WordPressNonceConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Validator\Constraints;

class PrimarySettingsType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fixer_api_key', Type\TextType::class, array(
                'label' => __('Fixer API Key', Plugin::NAME),
                'attr' => array(
                    'class' => 'regular-text code',
                ),
            ));

        $builder
            ->add('time_frame', Type\ChoiceType::class, array(
                'label' => __('Time frame', Plugin::NAME),
                'choices' => array(
                    new OneDayDateInterval(),
                    new OneWeekDateInterval(),
                    new OneMonthDateInterval(),
                    new OneYearDateInterval(),
                ),
                'choice_label' => 'label',
                'choice_value' => 'value',
                'multiple' => false,
                'attr' => array(
                    'class' => 'regular-text',
                ),
            ));

        $builder->add('nonce', Type\HiddenType::class, array(
            'data' => wp_create_nonce(Plugin::NAME .'-save-settings'),
            'constraints' => array(
                new Constraints\NotBlank(),
                new WordPressNonceConstraint(array('name' => Plugin::NAME .'-save-settings')),
            ),
        ));

        $builder->add('submit', Type\SubmitType::class, array(
            'label' => __('Save', Plugin::NAME),
            'attr' => array('class' => 'button button-primary'),
        ));
    }
}
