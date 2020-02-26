<?php


namespace app\components\view\navbar;

/**
 * Represents a child item, i.e. an item which is under a {@see NavbarMainItem}
 * @package app\components\view\navbar
 */
class NavbarChildItem extends AbstractNavbarItem implements NavbarItemChildInterface
{
    /**
     * @inheritDoc
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * @inheritDoc
     */
    public function getHTML(): string
    {
        ob_start();
        if($this->isDisplayed) {
            ?>
            <a class="dropdown-item<?= $this->isActive ? ' active' : '' ?>" href="<?= $this->url ?>"><?= $this->title ?><?= $this->title ?><?= $this->isActive ? ' <span class="sr-only">(current)</span>' : '' ?></a>
            <?php
        }
        return ob_get_clean();
    }
}