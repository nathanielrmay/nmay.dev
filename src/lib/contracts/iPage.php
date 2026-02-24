<?php
  namespace lib\contracts;
  interface iPage {
      public function getPageTitle();
      public function getVerticalMenu();
      public function getHeader();
      public function getFooter();
      public function addCss($css);
      public function getCss();
      public function getBodyClass();
  }
?>