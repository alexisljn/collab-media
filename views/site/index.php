<?php

/* @var $this yii\web\View */

$this->title = "Collab'Media";
?>
<div class="site-index row">
    <div class="col-12">
        <h1 class="site-index-title">Welcome to Collab'Media !</h1>
    </div>
    <div class="col-12">
        <p>You can suggest content to be posted on your company's social networks. Start now:</p>
        <a class="site-index-start" href="/proposal/create-proposal">Create a proposal</a>
        <p class="mt-4 mb-4"><a href="/proposal/my-proposals">Proposals I have created</a></p>
    </div>
    <div class="col-12">
        <div class="site-index-twitter-timeline">
            <a class="twitter-timeline" href="https://twitter.com/media_collab?ref_src=twsrc%5Etfw">Tweets by media_collab</a>
            <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
        </div>
    </div>
</div>
