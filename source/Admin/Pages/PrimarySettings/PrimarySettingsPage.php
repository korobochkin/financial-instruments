<?php
namespace Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings;

use Korobochkin\FinancialInstruments\Admin\Notices\SettingsSavedSuccessfullyNotice;
use Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames\OneDayDateInterval;
use Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames\OneMonthDateInterval;
use Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames\OneWeekDateInterval;
use Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames\OneYearDateInterval;
use Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\TimeFrames\SmartDateInterval;
use Korobochkin\FinancialInstruments\Options\Fixer\FixerAPIKeyOption;
use Korobochkin\FinancialInstruments\Options\TimeFrameOption;
use Korobochkin\FinancialInstruments\Plugin;
use Korobochkin\WPKit\Notices\NoticesStackInterface;
use Korobochkin\WPKit\Pages\SubMenuPage;
use Korobochkin\WPKit\Pages\Views\TwigPageView;

class PrimarySettingsPage extends SubMenuPage
{
    /**
     * @var NoticesStackInterface
     */
    protected $noticesStack;

    /**
     * @var FixerAPIKeyOption
     */
    protected $fixerAPIKeyOption;

    /**
     * @var TimeFrameOption
     */
    protected $timeFrameOption;

    public function __construct()
    {
        $this->setParentSlug('options-general.php');
        $this->setPageTitle(__('Financial Instruments', Plugin::NAME));
        $this->setMenuTitle($this->getPageTitle());
        $this->setCapability('manage_options');
        $this->setMenuSlug(Plugin::NAME);

        $this->setName('primary-settings');

        $view = new TwigPageView();
        $view->setTemplate('admin/primary-settings/page.html.twig');
        $this->setView($view);
    }

    /**
     * @inheritdoc
     */
    public function lateConstruct()
    {
        $this->setForm(
            $this->getFormFactory()->create(PrimarySettingsType::class, array(
                'fixer_api_key' => $this->getFixerAPIKeyOption()->get(),
                'time_frame' => $this->findTimeFrame(),
            ))
        );

        $this->handleRequest();

        $this
            ->getView()
            ->setContext(array(
                'page' => $this,
                'form' => $this->getForm()->createView(),
                'translations' => array(
                    'time_frame_caption' =>
                        __('Used to show changes in prices in percentages and prices.', Plugin::NAME),
                ),
            ));

        return $this;
    }

    /**
     * @throws \Exception
     * @return null|\DateInterval False if interval not found. \DateInterval if found.
     */
    protected function findTimeFrame()
    {
        /**
         * @var $current \DateInterval
         * @var $interval \DateInterval
         */
        $current       = $this->getTimeFrameOption()->get();
        $currentString = $current->format(SmartDateInterval::FORMAT);

        $intervals = array(
            new OneDayDateInterval(),
            new OneWeekDateInterval(),
            new OneMonthDateInterval(),
            new OneYearDateInterval(),
        );

        foreach ($intervals as $interval) {
            $intervalString = $interval->format(SmartDateInterval::FORMAT);
            if ($currentString === $intervalString) {
                return $interval;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function handleRequest()
    {
        $this->form->handleRequest($this->getRequest());

        if ($this->form->isSubmitted() && $this->form->isValid()) {
            $data = $this->form->getData();
            $this->getFixerAPIKeyOption()->updateValue($data['fixer_api_key']);
            $this->getTimeFrameOption()->updateValue($data['time_frame']);
            $this->getNoticesStack()->addNotice(new SettingsSavedSuccessfullyNotice());
        }
    }

    /**
     * @return NoticesStackInterface
     */
    public function getNoticesStack()
    {
        return $this->noticesStack;
    }

    /**
     * @param NoticesStackInterface $noticesStack
     *
     * @return $this
     */
    public function setNoticesStack(NoticesStackInterface $noticesStack)
    {
        $this->noticesStack = $noticesStack;
        return $this;
    }

    /**
     * @return FixerAPIKeyOption
     */
    public function getFixerAPIKeyOption()
    {
        return $this->fixerAPIKeyOption;
    }

    /**
     * @param FixerAPIKeyOption $fixerAPIKeyOption
     * @return $this
     */
    public function setFixerAPIKeyOption(FixerAPIKeyOption $fixerAPIKeyOption)
    {
        $this->fixerAPIKeyOption = $fixerAPIKeyOption;
        return $this;
    }

    /**
     * @return TimeFrameOption
     */
    public function getTimeFrameOption()
    {
        return $this->timeFrameOption;
    }

    /**
     * @param TimeFrameOption $timeFrameOption
     * @return $this
     */
    public function setTimeFrameOption(TimeFrameOption $timeFrameOption)
    {
        $this->timeFrameOption = $timeFrameOption;
        return $this;
    }
}
