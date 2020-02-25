<?php


namespace app\components\view\navbar;

use yii\base\InvalidConfigException;

/**
 * Represents an item in the navbar
 * @package app\components\view\navbar
 */
class NavbarMainItem extends AbstractNavbarItem
{
    /**
     * @var NavbarItemChildInterface[] $children Any children of the items, displayed in a dropdown
     */
    private array $children = [];

    /**
     * @inheritDoc
     */
    public function __construct(array $config)
    {
        parent::__construct($config);

        if(array_key_exists('children', $config)) {
            foreach($config['children'] as $childConfig) {
                if($childConfig === 'divider') {
                    $this->children[] = new NavbarItemDropdownDivider();
                    continue;
                }

                if(is_array($childConfig)) {
                    $this->children[] = new NavbarChildItem($childConfig);
                    continue;
                }

                throw new InvalidConfigException("Item child config must be 'delimiter' or array");
            }
        }
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