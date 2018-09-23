<?php
/**
 * 简易五子棋模块处理程序
 *
 * @author tg
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Feng_maoyuanModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
		$news = array(
				'Title' => '冯茂源',
				'Description' => '这是冯茂源的第一个模块!',
				'PicUrl' => 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1528958638559&di=0608918665397f3502ea305f438e136f&imgtype=0&src=http%3A%2F%2Fwww.m1ok.com%2Fupload%2Fnetpic2%2F2017-07-11-10-08-3157962548299s.jpg',
				'Url' => $this->createMobileUrl("index"),
			);
//		return $this->respText($content);
		return $this->respNews($news);
	}
}