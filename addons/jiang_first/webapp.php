<?php
/**
 * 测试模块模块PC接口定义
 *
 * @author 姜海蕤
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Jiang_firstModuleWebapp extends WeModuleWebapp {
	public function doPageTest(){
		global $_GPC, $_W;
		$errno = 0;
		$message = '返回消息';
		$data = array();
		return $this->result($errno, $message, $data);
	}
}