<?php
/**
 * feng_maoyuan模块APP接口定义
 *
 * @author 姜海蕤
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Feng_maoyuanModulePhoneapp extends WeModulePhoneapp {
	public function doPageTest(){
		global $_GPC, $_W;
		$errno = 0;
		$message = '返回消息';
		$data = array();
		return $this->result($errno, $message, $data);
	}
	
	
}