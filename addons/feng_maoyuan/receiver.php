<?php
/**
 * feng_maoyuan模块订阅器
 *
 * @author 姜海蕤
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Feng_maoyuanModuleReceiver extends WeModuleReceiver {
	public function receive() {
		$type = $this->message['type'];
		//这里定义此模块进行消息订阅时的, 消息到达以后的具体处理过程, 请查看微擎文档来编写你的代码
	}
}