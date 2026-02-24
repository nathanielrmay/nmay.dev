<?php
namespace lib;

use lib\contracts\iPage;

class content
{
    public $stPage;

    // $stPage is the requested url, passed from front controller index
    function __construct($stPage)
    {
        $this->stPage = $stPage;
    }

    function output()
    {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . $this->stPage;
        $interfaceFqn = iPage::class;

        $declaredBefore = get_declared_classes();

        ob_start();
        include $filePath;
        $html = ob_get_clean();

        echo $html;
    }

    function classListContainsInterface(array $classNames, string $interfaceFqn): bool
    {
        foreach ($classNames as $className) {
            $interfaces = class_implements($className);
            if ($interfaces && in_array($interfaceFqn, $interfaces, true)) {
                return true;
            }
        }
        return false;
    }
}

?>
