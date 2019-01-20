<?php
namespace Korobochkin\FinancialInstruments\Admin\Pages;

use Korobochkin\WPKit\Pages\PageInterface;
use Symfony\Component\Form\FormFactoryInterface;

class AdminPages
{
    /**
     * @var PageInterface[]
     */
    protected $pages = array();

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * AdminPages constructor.
     *
     * @param $twig \Twig_Environment
     * @param $formFactory FormFactoryInterface
     * @param $pages array
     */
    public function __construct(\Twig_Environment $twig, FormFactoryInterface $formFactory, $pages)
    {
        $this->twig        = $twig;
        $this->formFactory = $formFactory;
        $this->pages       = $pages;
        $this->initializePages();
    }

    /**
     * Initialize pages.
     *
     * @return $this For chain calls.
     */
    protected function initializePages()
    {
        $pagesAssoc = array();
        foreach ($this->pages as $page) {
            $pagesAssoc[$page->getName()] = $page;
            $page->getView()->setTwigEnvironment($this->twig);
            $page->setFormFactory($this->formFactory);
        }
        $this->pages = $pagesAssoc;
        return $this;
    }

    /**
     * Register pages.
     *
     * @return $this For chain calls.
     */
    public function register()
    {
        foreach ($this->pages as $page) {
            $page->register();
        }
        return $this;
    }
}
