<?php
namespace Korobochkin\FinancialInstruments\Widgets\CurrencyTable;

use Korobochkin\FinancialInstruments\Plugin;
use Korobochkin\FinancialInstruments\Services\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class WidgetSettingsType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var $widget CurrencyTableWidget
         */
        $widget = $options['widget'];

        $baseCurrencies = $widget->getCurrencyExchange()->getAllAvailable();

        $builder
            ->add('title', TextType::class, array(
                'label' => __('Title', Plugin::NAME),
                'attr' => array(
                    'class' => 'widefat',
                ),
            ))
            ->add('base_currency', ChoiceType::class, array(
                'label' => __('Base currency', Plugin::NAME),
                'choices' => array_combine($baseCurrencies, $baseCurrencies),
                'attr' => array(
                    'class' => 'widefat',
                ),
            ))
            ->add('currency_list', TextType::class, array(
                'label' => __('Currencies list', Plugin::NAME),
                'attr' => array(
                    'class' => 'widefat',
                ),
            ))
            ->add('flag_size', ChoiceType::class, array(
                'label' => __('Flag icons', Plugin::NAME),
                'choices' => array_flip($widget->getFlagSizes()),
                'placeholder' => __('No flag icon', Plugin::NAME),
                'required' => false,
                'attr' => array(
                    'class' => 'widefat',
                ),
            ))
            ->add('table_headers_currencies', TextType::class, array(
                'label' => __('Currencies names col', Plugin::NAME),
                'attr' => array(
                    'class' => 'widefat',
                ),
            ))
            ->add('table_headers_price', TextType::class, array(
                'label' => __('Price col', Plugin::NAME),
                'attr' => array(
                    'class' => 'widefat',
                ),
            ))
            ->add('table_headers_change', TextType::class, array(
                'label' => __('Change col', Plugin::NAME),
                'attr' => array(
                    'class' => 'widefat',
                ),
            ))
            ->add('caption', CheckboxType::class, array(
                'label' => __('Show last update date of currency exchange rate.', Plugin::NAME),
                'required' => false,

            ));


        /*$builder->add('nonce', Type\HiddenType::class, array(
            'data' => wp_create_nonce(Plugin::NAME .'-save-settings'),
            'constraints' => array(
                new Constraints\NotBlank(),
                new WordPressNonceConstraint(array('name' => Plugin::NAME .'-save-settings')),
            ),
        ));*/

        /*$builder->add('submit', Type\SubmitType::class, array(
            'label' => __('Save', Plugin::NAME),
            'attr' => array('class' => 'button button-primary'),
        ));*/
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setRequired('widget');
        $resolver->setAllowedTypes('widget', CurrencyTableWidget::class);
    }
}
