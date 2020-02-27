<?php


namespace app\components\view\navbar;


class NavbarItemDropdownDivider implements NavbarItemChildInterface
{
    /**
     * @inheritDoc
     */
    public function getHTML(): string
    {
        ob_start();
        ?>
        <div class="dropdown-divider"></div>
        <?php
        return ob_get_clean();
    }
}