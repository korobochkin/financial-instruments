<?php
namespace Korobochkin\FinancialInstruments\Admin\Notices;

use Korobochkin\FinancialInstruments\Plugin;
use Korobochkin\WPKit\Notices\Notice;
use Korobochkin\WPKit\Notices\NoticeSuccessView;

class SettingsSavedSuccessfullyNotice extends Notice
{
    public function __construct()
    {
        $this->setName(Plugin::_NAME_ . '_settings_saved_successfully');
    }

    /**
     * @inheritdoc
     */
    public function lateConstruct()
    {
        $this
            ->setView(new NoticeSuccessView())
            ->setContent('<p>' . __('Settings saved successfully.', Plugin::NAME) . '</p>');

        return $this;
    }
}
