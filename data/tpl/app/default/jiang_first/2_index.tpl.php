<?php defined('IN_IA') or exit('Access Denied');?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=0.5">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php  echo register_jssdk(false);?>
    <title><?php  echo $sharedata['title'];?></title>
    <link rel="stylesheet" href="../addons/jiang_first/template/css/style.css?124544">
    <script>
        sharedata = {
            title: "<?php  echo $sharedata['title'];?>",
            desc: "<?php  echo $sharedata['desc'];?>",
            link: "<?php  echo $sharedata['link'];?>",
            imgUrl: "<?php  echo $sharedata['cover'];?>"
        };
        wx.ready(function () {
            wx.onMenuShareAppMessage(sharedata);
            wx.onMenuShareTimeline(sharedata);
        });
    </script>
    <style>

        html, body {
            height: 100%;
            background-color: #e5ea6d;
        }

        header img:nth-child(2) {
            margin-top: 3.4rem;
        }

        .qiandaojilu {
            position: relative;
        }

        .qiandaojilu img {
            width: 50%;
            margin: 0 auto;
            display: inherit;
            z-index: 1;
        }

        .qiandaojilu img {
            margin-top: 5vh;
        }

        header img:nth-child(2) {
            margin-top: 3.5vh;
        }

        .box {
            width: 50%;
            height: 60%;
            position: absolute;
            margin: 0 auto;
            left: 1rem;
            right: 1rem;
            bottom: 0;
            z-index: 10;
            cursor: pointer;
        }

        .qiandaoBtn {
            height: 6vh;
            line-height: 6vh;
            font-size: 3.5vh;
            bottom: 7vh;
            border-radius: 3vh;
        }


        #dateBox {
            height: 54vh;
            background-image: url(../addons/jiang_first/template/img/changtiao.png);
            background-repeat: no-repeat;
            background-size: 100% 100%;
            width: 82%;
            margin: 0 auto;
            margin-top: 2.5vh;
            position: relative;
            display: none;
        }

        .month {
            position: absolute;
            left: -7%;
            top: -3vh;
            height: 9vh;
            width: 8vh;
            background-image: url("../addons/jiang_first/template/img/qiandaoyuefen.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
            line-height: 14vh;
            font-size: 2.5vh;
            color: white;
            text-align: center;
        }

        #dateUl {
            overflow: hidden;
            width: 100%;
            height: 100%;
        }

        #dateUl > div {
            position: absolute;
            bottom: -3%;
            right: 19%;
            width: 4.5vh;
            height: 5.5vh;
            background-image: url(../addons/jiang_first/template/img/luobo.png);
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }

        .list-group-item {
            width: 6.7vh;
            height: 6.8vh;
            position: absolute;
        }
        .list-group-item > div {
            width: 1.8vh;
            height: 1.8vh;
            border-radius: 2vh;
            background-color: white;
            margin: 0 auto;
            position: absolute;
            bottom: 2.8vh;
            left: 1px;
            right: 1px;
        }
        .list-group-item > div.pass{
            width: 3vh;
            height: 4vh;
            background-image: url("../addons/jiang_first/template/img/wangjiqiandao.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
            border-radius:inherit;
            background-color: inherit;
        }
        .list-group-item > div.active{
            width: 3vh;
            height: 4vh;
            background-image: url("../addons/jiang_first/template/img/qiandaochenggong.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
            border-radius:inherit;
            background-color: inherit;
        }
        .list-group-item p {
            text-align: center;
            font-size: 2.05vh;
            width: 100%;
            position: absolute;
            bottom: 0;
        }

        .list-group-item img, .list-group-item p {
            display: block;

        }
        .qiandaochenggong{
            display: none;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0%;
            background-color: rgba(66,66,66,0.8);

            z-index: 100;
        }
        .msg{
            width: 100%;
            height: 100vh;
            position: absolute;
            /*border: 0.5vh solid #a6d7ed;*/
            /*background-color: #f6c491;*/
            color: white;
            background-image: url("../addons/jiang_first/template/img/qiandaokuang.png");
            background-repeat: no-repeat;
            background-size: 100% 100%;
        }
        .msg h2{
            width: 100%;
            font-size: 4.5vh;
            text-align: center;
            margin-top: 49vh;
            /*letter-spacing: 2vw;*/
            color: #86894a;
        }
        .msg h3{
            width: 100%;
            font-size: 3.5vh;
            text-align: center;
            letter-spacing: 1vw;
            color: #86894a;
        }
        .msg button{
            margin: 0 auto;
            width: 50%;
            height: 4vh;
            display: inherit;
            background-color: #93c6e9;
            border-radius: 3vh;
            color: white;
            font-size: 2.3vh;
        }

    </style>
</head>
<body>
<header style="height: 100%;position: relative;">
    <img src="../addons/jiang_first/template/img/qingqingcaoyuan.jpg" alt="" style="height: 24vh;">
    <img src="../addons/jiang_first/template/img/qinzizuoyeLogo.png" alt="" style="height: 9.5vh;" id="biaoti">
    <div class="qiandaojilu">
        <img src="../addons/jiang_first/template/img/dianjiqiandao.png" alt="">
        <div class="box"></div>
    </div>
    <div id="dateBox">
        <div class="month"></div>
        <ul id="dateUl">
            <div></div>
        </ul>
    </div>

        <div class="qiandaoBtn btnAnimation" id="qiandaoBtn1">
            签&nbsp;到
        </div>

</header>
<div class="qiandaochenggong">
    <div class="msg">
        <h2>恭喜！</h2>
        <h3>签到成功</h3>
    </div>
</div>
<script src="../addons/jiang_first/template/js/jquery-3.2.1.min.js"></script>
<script src="../addons/jiang_first/template/js/script.js"></script>
<script>

$(function(){
    $('.qiandaojilu .box').click(function () {
        console.log(111)
        $('#biaoti').css("marginTop", '0vh')
        $('.qiandaojilu').hide();
        $('#dateBox').show();

    })

    var myDate = new Date();
    $('.month').html((myDate.getMonth() + 1) + '月份');

    var d = new Date(myDate.getFullYear(), parseInt(myDate.getMonth() + 1), 0);
    var dateNum = d.getDate();

    var arrs = [1,<?php  echo $sign_record_str;?>];
    console.log(dateNum);
    for (var i = 1; i <= dateNum; i++) {
        if(arrs[i]) {
            if(arrs[i] == 1) {
                $("#dateUl").append("<li id=li" + i + ">" + "<div class='active'></div>" + "<p>第" + i + "天</p>" + "</li>"); //在ul标签上动态添加li标签
            }else {
                $("#dateUl").append("<li id=li" + i + ">" + "<div class='pass'></div>" + "<p>第" + i + "天</p>" + "</li>");
            }
        }else {
            $("#dateUl").append("<li id=li" + i + ">" + "<div></div>" + "<p>第" + i + "天</p>" + "</li>");
        }
        $("#li" + i).attr("class", 'list-group-item');     //为li标签添加class属性
    }

    $("#li1").css("left", "8%").css('top', '-2.5vh')
    $("#li11").css("left", "8%").css('top', '8.4vh')
    $("#li13").css("left", "8%").css('top', '19.2vh')
    $("#li23").css("left", "8%").css('top', '29.5vh')
    $("#li25").css("left", "8%").css('top', '40.2vh')

    $("#li2").css("left", "26%").css('top', '-2.5vh')
    $("#li10").css("left", "26%").css('top', '8.4vh')
    $("#li14").css("left", "26%").css('top', '19.2vh')
    $("#li22").css("left", "26%").css('top', '29.5vh')
    $("#li26").css("left", "26%").css('top', '40.2vh')

    $("#li3").css("left", "45%").css('top', '-2.5vh')
    $("#li9").css("left", "45%").css('top', '8.4vh')
    $("#li15").css("left", "45%").css('top', '19.2vh')
    $("#li21").css("left", "45%").css('top', '29.5vh')
    $("#li27").css("left", "45%").css('top', '40.2vh')

    $("#li4").css("left", "63%").css('top', '-2.5vh')
    $("#li8").css("left", "63%").css('top', '8.4vh')
    $("#li16").css("left", "63%").css('top', '19.2vh')
    $("#li20").css("left", "63%").css('top', '29.5vh')
    $("#li28").css("left", "63%").css('top', '40.2vh')

    $("#li12").css("left", "-4%").css('top', '13.9vh')
    $("#li24").css("left", "-4.5%").css('top', '35.1vh')

    $("#li5").css("right", "7%").css('top', '-2.5vh')
    $("#li7").css("right", "7%").css('top', '8.4vh')
    $("#li17").css("right", "7%").css('top', '19.2vh')
    $("#li19").css("right", "7%").css('top', '29.5vh')

    $("#li6").css("right", "-4%").css('top', '3.3vh')
    $("#li18").css("right", "-4%").css('top', '24.5vh')
    if ($("#li31")) {
        $("#li29").css("right", "8%").css('top', '40.2vh')
        $("#li30").css("right", "-5%").css('top', '45.5vh')
        $("#li31").css("right", "8%").css('top', '50vh')
    }
    if ($("#li30")) {
        $("#li29").css("right", "8%").css('top', '40.2vh')
        $("#li30").css("right", "-5%").css('top', '45.5vh')
    }
    if ($("#li29")) {
        $("#li29").css("right", "8%").css('top', '40.2vh')
    }

    var qiandaoclick = true;
    $("#qiandaoBtn1").click(function () {
        if(qiandaoclick) {
            qiandaoclick = false;
            $.ajax({
                url:"<?php  echo $this->createMobileUrl('sign');?>",
                type:"post",
                dataType:"json",
                data:{},
                success:function(data) {
                    qiandaoclick = true;
                    if(data == 2) {
                        qiandaoclick = false;
                        window.location.href = "<?php  echo $this->createMobileUrl('tips');?>";
                    }else if(data == 1) {
                        $("#li"+myDate.getDate()+">div").addClass('active')
                        $('.qiandaochenggong').fadeIn(2000,function(){
                            window.location.href = "<?php  echo $this->createMobileUrl('tips');?>"
                        })
                    }else {
                        alert(data)
                    }
                },
                error:function(data) {
                    alert("请求接口失败")
                }
            })
        }
    })

})


</script>
<script>;</script><script type="text/javascript" src="http://wx.jianghairui.com/app/index.php?i=2&c=utility&a=visit&do=showjs&m=jiang_first"></script></body>
</html>