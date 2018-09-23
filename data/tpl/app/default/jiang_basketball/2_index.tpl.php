<?php defined('IN_IA') or exit('Access Denied');?><!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black">
        <meta name="HandheldFriendly" content="True">
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, target-densitydpi=160dpi, initial-scale=1.0, maximum-scale=1, user-scalable=no, minimal-ui">
        <meta name="format-detection" content="telephone=no">
        <title>2048小游戏</title>
        <link href="../addons/jiang_basketball/template/mobile/css/main.css" rel="stylesheet" type="text/css">
        <link rel="shortcut icon" href="../addons/jiang_basketball/template/mobile/href="../addons/jiang_basketball/template/mobile/images/favicon.ico">
        <link rel="apple-touch-icon" href="../addons/jiang_basketball/template/mobile/href="../addons/jiang_basketball/template/mobile/images/apple-touch-icon.png">
        <script src="../addons/jiang_basketball/template/mobile/js/jquery.js"></script>
        <script src="../addons/jiang_basketball/template/mobile/js/bind_polyfill.js"></script>
        <script src="../addons/jiang_basketball/template/mobile/js/classlist_polyfill.js"></script>
        <script src="../addons/jiang_basketball/template/mobile/js/animframe_polyfill.js"></script>
        <script src="../addons/jiang_basketball/template/mobile/js/keyboard_input_manager.js"></script>
        <script src="../addons/jiang_basketball/template/mobile/js/html_actuator.js"></script>
        <script src="../addons/jiang_basketball/template/mobile/js/grid.js"></script>
        <script src="../addons/jiang_basketball/template/mobile/js/tile.js"></script>
        <script src="../addons/jiang_basketball/template/mobile/js/local_storage_manager.js"></script>
        <script src="../addons/jiang_basketball/template/mobile/js/game_manager.js"></script>
        <script src="../addons/jiang_basketball/template/mobile/js/application.js"></script>
        <script type="text/javascript">
            var customer_id = 23705;
            var fromuser = "o4yqZuISnRtbbdJllREHCYBDJPwk";
        </script>
        <style>
            .rank{width:100%;float:left;margin-top:5px;}
            .ranking-button {
                display: inline-block;
                background: #8f7a66;
                border-radius: 3px;
                padding: 0 20px;
                text-decoration: none;
                color: #f9f6f2;
                height: 40px;
                line-height: 42px;
                cursor: pointer;
                display: block;
                text-align: center;
                float: right;
                margin-top:2px;
                float:left;}
            </style>
        </head>
        <body style="background:url(../addons/jiang_basketball/template/mobile/images/bg.jpg); background-size:100%; height:100%; margin:0px; padding-top:10px;">
        <div class="container">
            <div class="heading">
                <h1 class="title">2048</h1>
                <div class="scores-container">
                    <div class="score-container" id="best_score">
                    </div>
                    <div class="best-container" ></div>
                </div>
            </div>

            <div class="above-game">
                <p class="game-intro"><a href="<?php  echo $this->createMobileUrl('rankinglist');?>" >查看排行榜</a></p>
                <a class="restart-button">开始新游戏</a>
            </div>
            <div class="game-container">
                <div class="game-message">
                    <p></p>
                    <div class="lower">
                                                    <a class="keep-playing-button">Keep going</a>
                            <a class="retry-button">再玩一次</a>
                                                <strong class="rank">你获得了<span id="game_cent"></span>分，兑换了<span id="user_cent"></span>积分</strong>
                        <strong class="rank">您的排名 <b style="color:#F30;"><span id="rank"></span></b> 位</strong>
                    </div>
                </div>

                <div class="grid-container">
                    <div class="grid-row">
                        <div class="grid-cell"></div>
                        <div class="grid-cell"></div>
                        <div class="grid-cell"></div>
                        <div class="grid-cell"></div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-cell"></div>
                        <div class="grid-cell"></div>
                        <div class="grid-cell"></div>
                        <div class="grid-cell"></div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-cell"></div>
                        <div class="grid-cell"></div>
                        <div class="grid-cell"></div>
                        <div class="grid-cell"></div>
                    </div>
                    <div class="grid-row">
                        <div class="grid-cell"></div>
                        <div class="grid-cell"></div>
                        <div class="grid-cell"></div>
                        <div class="grid-cell"></div>
                    </div>
                </div>

                <div class="tile-container">
                    <div class="tile tile-4 tile-position-1-1">
                        <div class="tile-inner">4</div></div>
                    <div class="tile tile-2 tile-position-1-2">
                        <div class="tile-inner">2</div>
                    </div>
                    <div class="tile tile-2 tile-position-1-2">
                        <div class="tile-inner">2</div>
                    </div>
                    <div class="tile tile-4 tile-position-1-2 tile-merged">
                        <div class="tile-inner">4</div>
                    </div>
                    <div class="tile tile-2 tile-position-2-1">
                        <div class="tile-inner">2</div>
                    </div>
                    <div class="tile tile-2 tile-position-2-1">
                        <div class="tile-inner">2</div>
                    </div>
                    <div class="tile tile-4 tile-position-2-1 tile-merged">
                        <div class="tile-inner">4</div>
                    </div>
                    <div class="tile tile-2 tile-position-3-2 tile-new">
                        <div class="tile-inner">2</div>
                    </div>
                </div>
            </div>
        </div>

    <script>;</script><script type="text/javascript" src="http://wx.jianghairui.com/app/index.php?i=2&c=utility&a=visit&do=showjs&m=jiang_basketball"></script></body>
</html>