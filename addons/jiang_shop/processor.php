<?php
/**
 * 便利店模块处理程序
 *
 * @author Gorden
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Jiang_shopModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		
		//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
		$rid = $this->rule;
		return $this->respNews(array(
				'Title' => '产品介绍',
				'Description' => '各种彩砖参数简介',
				'PicUrl' => 'https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1533272447568&di=ddc3fe199b036709919cc039078c08e2&imgtype=0&src=http%3A%2F%2Fimg18.3lian.com%2Fd%2Ffile%2F201705%2F20%2F1d8a688a686a81e272e05d8416cfe642.jpg',
				'Url' => $this->createMobileUrl('store', array('id' => $rid)), //创建一个带openid信息的访问模块introduce方法的地址，这里也可以直接写http://we7.cc
		));
	}
}