<?php
/**
 * 投篮小游戏模块处理程序
 *
 * @author 姜海蕤
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Jiang_basketballModuleProcessor extends WeModuleProcessor {
	public function respond() {
	    $rid = $this->rule;
        $content = $this->message['content'];
        //这里定义此模块进行消息处理时的具体过程, 请查看微信文档来编写你的代码
        return $this->respNews(array(
            'Title' => '小游戏',
            'Description' => 'h5小游戏,不带排行榜',
            'PicUrl' => 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1526111174875&di=9f7076fb13b73b1d274ba62f3e3ecca7&imgtype=0&src=http%3A%2F%2Fimg.zcool.cn%2Fcommunity%2F01f8ed554990aa0000019ae9627e3c.jpg%401280w_1l_2o_100sh.png',
            'Url' => $this->createMobileUrl('index', array('id' => $rid)), //创建一个带openid信息的访问模块introduce方法的地址，这里也可以直接写http://we7.cc
        ));
	}
}