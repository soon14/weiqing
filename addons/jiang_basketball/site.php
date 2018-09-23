<?php
/**
 * 投篮小游戏模块微站定义
 *
 * @author 姜海蕤
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Jiang_basketballModuleSite extends WeModuleSite {

	public function doMobileIndex() {
		//这个操作被定义用来呈现 功能封面
        include $this->template('index');
	}

	public function doMobileRankinglist() {
	    message('待开发');
    }
	public function doWebRule() {
		//这个操作被定义用来呈现 规则列表
	}
	public function doWebMenu() {
		//这个操作被定义用来呈现 管理中心导航菜单
        echo '<h1 align="center">待开发</h1>';
	}

}