<?php


namespace app\components\view\navbar;

/**
 * An interface implemented by any element which is a child of a {@see NavbarMainItem}
 * @package app\components\view\navbar
 */
interface NavbarItemChildInterface
{
    /**
     * Returns the HTML of the element
     * @return string
     */
    public function getHTML(): string;
}