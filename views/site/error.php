<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php
    if(!empty($message)) {
        ?>
        <div class="alert alert-danger">
            <?= nl2br(Html::encode($message)) ?>
        </div>

        <p>
            The above error occurred while the Web server was processing your request.
        </p>
        <?php
    } else {
        ?>
        <p>
            An error occurred while the Web server was processing your request.
        </p>
        <?php
    }
    ?>

    <p><a href="/">Go home</a></p>
</div>
