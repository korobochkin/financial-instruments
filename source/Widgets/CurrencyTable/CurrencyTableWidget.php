<?php
namespace Korobochkin\FinancialInstruments\Widgets\CurrencyTable;

use Korobochkin\FinancialInstruments\Plugin;
use Korobochkin\FinancialInstruments\Services\CurrencyExchange;
use Korobochkin\FinancialInstruments\Services\CurrencyTable;
use Korobochkin\FinancialInstruments\Services\WidgetCaption;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class CurrencyTableWidget extends \WP_Widget
{
    /**
     * @var CurrencyExchange
     */
    protected $currencyExchange;

    /**
     * @var array
     */
    protected $flagSizes;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var WidgetCaption
     */
    protected $pluginDeveloper;

    /**
     * @var array Default settings.
     */
    protected $default;

    /**
     * CurrencyTableWidget constructor.
     *
     * @param CurrencyExchange $currencyExchange
     * @param array $flagSizes
     * @param FormFactoryInterface $formFactory
     * @param \Twig_Environment $twig
     * @param Request $request
     * @param WidgetCaption $pluginDeveloper
     */
    public function __construct(
        CurrencyExchange $currencyExchange,
        $flagSizes,
        FormFactoryInterface $formFactory,
        \Twig_Environment $twig,
        Request $request,
        WidgetCaption $pluginDeveloper
    ) {
        $this->currencyExchange = $currencyExchange;
        $this->flagSizes        = $flagSizes;
        $this->formFactory      = $formFactory;
        $this->twig             = $twig;
        $this->request          = $request;
        $this->pluginDeveloper  = $pluginDeveloper;

        parent::__construct(
            Plugin::NAME . '_table',
            __('Financial Instruments Table', Plugin::NAME),
            array(
                'description' => __('A table with currency rates.', Plugin::NAME),
            )
        );
    }

    /**
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        $instance = $this->mergeWithDefault($instance);

        echo $args['before_widget'];
        $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if (!empty($instance['currency_list'])) {
            if (!is_array($instance['currency_list'])) {
                $instance['currency_list'] = str_replace(' ', '', $instance['currency_list']);
                $instance['currency_list'] = explode(',', $instance['currency_list']);
            }

            if (!empty($instance['currency_list']) && !empty($instance['base_currency'])) {
                $table = new CurrencyTable($this->currencyExchange);
                $table->setParameters(array(
                    'base_currency' => $instance['base_currency'],
                    'currency_list' => $instance['currency_list'],
                    'flag_size' => (int) $instance['flag_size'],
                    'table_headers_currencies' => $instance['table_headers_currencies'],
                    'table_headers_price' => $instance['table_headers_price'],
                    'table_headers_change' => $instance['table_headers_change']
                ));
                $markup = $table->getTableMarkup();
                echo $markup;
            }
        }

        if ($instance['caption']) {
            echo '<p class="financial-instruments--support-info-container">' .
                 $this->pluginDeveloper->getCaption($instance['base_currency']).
                 '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * @param array $new
     * @param array $old
     * @return array
     */
    public function update($new, $old)
    {
        $form = $this->buildForm($old);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->mergeWithDefault($this->unSerializeData($form->getData()));
        }

        return $this->buildDefault();
    }

    public function form($instance)
    {
        static $first = true;
        $instance     = $this->serializeData($this->mergeWithDefault($instance));

        $form = $this->buildForm($instance);

        echo $this->twig->render(
            'admin/widgets/currency-table/form.html.twig',
            array(
                'form' => $form->createView(),
                'translations' => array(
                    'base_currency_caption' =>
                        __('The currency in which will be settled other currencies.', Plugin::NAME),
                    'currency_list_caption' =>
                        __('The currencies which will be displayed in table. Separate by commas.', Plugin::NAME),
                    'table_headers' => __('Table headers', Plugin::NAME),
                    'caption' => __('Caption', Plugin::NAME)
                ),
            )
        );
    }

    /**
     * @param array $data
     * @return FormInterface
     */
    protected function buildForm(array $data)
    {
        return $this->formFactory->createNamed(
            $this->id_base,
            WidgetSettingsType::class,
            $data,
            array('widget' => $this)
        );
    }

    /**
     * @param array $data
     * @return array
     */
    protected function serializeData(array $data)
    {
        $data['currency_list'] = implode(', ', $data['currency_list']);
        return $data;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function unSerializeData(array $data)
    {
        $currencyList = explode(',', $data['currency_list']);
        if (is_array($currencyList)) {
            $data['currency_list'] = array();
            foreach ($currencyList as $key => $item) {
                $item = strtoupper(sanitize_text_field($item));
                if (!empty($item)) {
                    $data['currency_list'][] = $item;
                }
            }
        } else {
            unset($data['currency_list']);
        }

        return $data;
    }

    /**
     * @param $instance
     * @return array
     */
    public function mergeWithDefault($instance)
    {
        if (!is_array($this->default)) {
            $this->default = $this->buildDefault();
        }
        return wp_parse_args($instance, $this->default);
    }

    /**
     * @return array Default settings.
     */
    public function buildDefault()
    {
        return array(
            'title' => __('Exchange table', Plugin::NAME),
            'base_currency' => __('USD', Plugin::NAME),
            'currency_list' => array('CAD', 'AUD', 'GBP'),
            'flag_size' => 16,
            'table_headers_currencies' => __('Currencies', Plugin::NAME),
            'table_headers_price' => __('Rate', Plugin::NAME),
            'table_headers_change' => __('Change %', Plugin::NAME),
            'caption' => true
        );
    }

    /**
     * @return CurrencyExchange
     */
    public function getCurrencyExchange()
    {
        return $this->currencyExchange;
    }

    /**
     * @return array
     */
    public function getFlagSizes()
    {
        return $this->flagSizes;
    }
}
