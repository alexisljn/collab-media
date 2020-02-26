<?php


namespace app\components\view\navbar;

use yii\base\InvalidConfigException;

/**
 * Represents the header navbar
 * @package app\components\view\navbar
 */
class Navbar
{
    /**
     * @var NavbarMainItem[] $items The navbar items
     */
    private $items;

    /**
     * Navbar constructor
     *
     * @param array $config The array representing the Navbar config
     *  Format:
     *  [
     *      'items' => [
     *          [
     *              'title'         => 'Item title',
     *              'url'           => 'URL when item clicked',
     *              'roleNeeded'    => 'role'   // Optionnal, if not set everyone will see it
     *              'activeActions' => [        // Optionnal, if not set the item will never be shown as active
     *                  'controller-name'   => '*'
     *                  // OR
     *                  'controller-name    => [
     *                      'action-name',
     *                      'action-name',
     *                  ],
     *                  ...
     *              ]
     *              'children'      => [        // Optionnal
     *                  [
     *                      'title'         => 'Child item 1',
     *                      'url'           => 'URL when item clicked',
     *                      'roleNeeded'    => 'role'   // Optionnal
     *                  ]
     *                  'divider' // A delimiter (horizontal grey line) in the dropdown
     *                  [
     *                      ...
     *                  ]
     *              ],
     *          ],
     *          [
     *              ...
     *          ]
     *      ]
     *  ]
     *
     *
     *
     * @throws InvalidConfigException
     */
    public function __construct(array $config)
    {
        if(!is_array($config)) {
            throw new InvalidConfigException('The Navbar config must be an array');
        }

        $this->items = [];

        if(!array_key_exists('items', $config)) {
            return;
        }

        foreach($config['items'] as $itemConfig) {
            $this->items[] = new NavbarMainItem($itemConfig);
        }
    }

    public function getHTML(): string
    {
        ob_start();
        ?>
            <ul class="navbar-nav mr-auto">
                <?php
                foreach($this->items as $item) {
                    ?>
                    <?= $item->getHTML(); ?>
                    <?php
                }
                ?>
            </ul>
        <?php
        return ob_get_clean();
    }
}