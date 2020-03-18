<?php


namespace app\commands\mainController;

class MainController extends \yii\console\Controller
{
    protected function print(string $text, ...$formatOptions)
    {
        $this->stdout($this->ansiFormat($text, ...$formatOptions));
    }

    protected function println(string $text, ...$formatOptions)
    {
        $this->print($text . PHP_EOL, ...$formatOptions);
    }
}