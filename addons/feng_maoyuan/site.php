<?php
/**
 * feng_maoyuan模块微站定义
 *
 * @author 姜海蕤
 * @url 
 */
defined('IN_IA') or exit('Access Denied');
function p($arr) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}
class Feng_maoyuanModuleSite extends WeModuleSite {


	public function doMobileIndex() {
		//这个操作被定义用来呈现 功能封面
        global $_W,$_GPC;
        echo '<h1>这是首页啊</h1>';
        echo '<img src="'. tomedia('op.jpg') .'">';

	}
	public function doWebRule() {
		//这个操作被定义用来呈现 规则列表
	}
		public function doWebMenu() {
		//这个操作被定义用来呈现 管理中心导航菜单
	}
	

}