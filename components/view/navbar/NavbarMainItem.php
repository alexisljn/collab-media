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
        ob_start();
        if($this->isDisplayed) {
            if($this->hasChildren()) {
                ?>
                <li class="nav-item dropdown<?= $this->isActive ? ' active' : '' ?>">
                    <a class="nav-link dropdown-toggle" href="<?= $this->url ?>" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?= $this->title ?>
                    </a>
                    <div class="dropdown-menu">
                        <?php
                        foreach($this->children as $child) {
                            ?>
                            <?= $child->getHTML(); ?>
                            <?php
                        }
                        ?>
                    </div>
                </li>
                <?php
            } else {
                ?>
                <li class="nav-item<?= $this->isActive ? ' active' : '' ?>">
                    <a class="nav-link" href="<?= $this->url ?>"><?= $this->title ?><?= $this->isActive ? ' <span class="sr-only">(current)</span>' : '' ?></a>
                </li>
                <?php
            }
        }
        return ob_get_clean();
    }

    /**
     * Returns true if item has at least one child, false otherwise
     *
     * @return bool
     */
    private function hasChildren(): bool
    {
        return !empty($this->children);
    }
}