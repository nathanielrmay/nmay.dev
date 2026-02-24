<?php
namespace lib\contracts;

abstract class aPage implements iPage {

    protected $css = ['/lib/nmay.css', '/lib/nmay.mob.css'];

    public function addCss($css): void
    {
        if (is_array($css)) {
            $this->css = array_merge($this->css, $css);
        } else {
            $this->css[] = $css;
        }
    }

    public function getCss()
    {
        return $this->css;
    }

    public function getVerticalMenu()
    {
        return null;
    }

    public function getHeader()
    {
        return null;
    }

    public function getFooter()
    {
        return null;
    }

    public function getPageTitle()
    {
        return null;
    }

    public function getBodyClass()
    {
        return null;
    }
}
