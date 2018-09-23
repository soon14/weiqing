<?php
/**
 * 简易五子棋模块处理程序
 *
 * @author tg
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Tg_forfiveModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
		$news = array(
				'Title' => '简易五子棋',
				'Description' => '在轻松的休闲之余，就来玩一下简易五子棋吧！',
				'PicUrl' => 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1521310653243&di=ce87caa62f018b71eea530debe71521d&imgtype=jpg&src=http%3A%2F%2Fsrc.onlinedown.net%2Fnew_img%2Fapk_logo%2F2016%2F0420%2F257251_1461139778_1773.png',
				'Url' => $this->createMobileUrl("index"),
			);
		//return $this->respText($content);
		return $this->respNews($news);
	}
}