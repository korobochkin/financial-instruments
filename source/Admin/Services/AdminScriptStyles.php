<?php
namespace Korobochkin\FinancialInstruments\Admin\Services;

use Korobochkin\WPKit\ScriptsStyles\AbstractScriptsStyles;

class AdminScriptStyles extends AbstractScriptsStyles
{
    /**
     * @inheritdoc
     */
    public function register()
    {
        return $this;
    }

    /**
     * @param $hook string Current WordPress admin page slug (file name).
     * @return $this For chain calls.
     */
    public function enqueue($hook)
    {
        return $this;
    }

    /**
     * @return $this For chain calls.
     */
    public function enqueueCustomizer()
    {
        return $this;
    }
}
