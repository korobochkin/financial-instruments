<?php
namespace Korobochkin\FinancialInstruments\Admin\Pages;

use Korobochkin\FinancialInstruments\Admin\Pages\PrimarySettings\PrimarySettingsPage;
use Korobochkin\FinancialInstruments\Plugin;

class Plugins
{
    /**
     * @var PrimarySettingsPage
     */
    protected $primarySettingsPage;

    /**
     * Plugins constructor.
     * @param PrimarySettingsPage $primarySettingsPage
     */
    public function __construct(PrimarySettingsPage $primarySettingsPage)
    {
        $this->primarySettingsPage = $primarySettingsPage;
    }

    /**
     * Adds plugin action links (along with Deactivate | Edit | Delete).
     *
     * @param $links array Default links setted up by WordPress.
     *
     * @return array Default links + our custom links.
     */
    public function addActionLinks(array $links)
    {
        $links['settings'] = sprintf(
            '<a href="%1$s">%2$s</a>',
            esc_url($this->primarySettingsPage->getURL()),
            esc_html(__('Settings', Plugin::NAME))
        );

        return $links;
    }
}
