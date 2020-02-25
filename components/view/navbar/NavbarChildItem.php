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
        // TODO: Implement getHTML() method.
        return 'TODO';
    }
}