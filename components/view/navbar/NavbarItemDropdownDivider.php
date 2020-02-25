<?php


namespace app\components\view\navbar;


class NavbarItemDropdownDivider implements NavbarItemChildInterface
{
    /**
     * @inheritDoc
     */
    public function getHTML(): string
    {
        return '<div>DIVIDER</div>';
    }
}