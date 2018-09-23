<?php
/**
 * 测试模块模块定义
 *
 * @author 姜海蕤
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');

class Jiang_firstModule extends WeModule {
    public function fieldsFormDisplay($rid = 0) {
        //要嵌入规则编辑页的自定义内容，这里 $rid 为对应的规则编号，新增时为 0
        global $_W,$_GPC;
        load()->func('tpl');

        $info = pdo_fetch("SELECT * FROM ".tablename("buhouwa_activity")." WHERE rid=:rid",array(':rid'=>$rid));
        if($info) {
            $action = 'update';
        }
        include $this->template('form');
    }

    public function fieldsFormValidate($rid = 0) {
        //规则编辑保存时，要进行的数据验证，返回空串表示验证无误，返回其他字符串将呈现为错误提示。这里 $rid 为对应的规则编号，新增时为 0
        return '';
    }

    public function fieldsFormSubmit($rid) {
        //规则验证无误保存入库时执行，这里应该进行自定义字段的保存。这里 $rid 为对应的规则编号
        global $_W,$_GPC;
        $data = array(
            'rid'=>$rid,
            'uniacid'=>$_W['uniacid'],
            'title'=>$_GPC['title'],
            'desc'=>$_GPC['description'],
            'cover'=>$_GPC['cover']
        );
        if($_GPC['action'] == 'update') {
            pdo_update("buhouwa_activity",$data,array('rid'=>$rid));
        }else {
            pdo_insert("buhouwa_activity",$data);
        }
    }

    public function ruleDeleted($rid) {
        //删除规则时调用，这里 $rid 为对应的规则编号
        global $_W,$_GPC;
        pdo_delete('buhouwa_activity', array('rid' => $rid,'uniacid'=>$_W['uniacid']));
        return true;
    }

    public function settingsDisplay($settings) {
        global $_W, $_GPC;

//        load()->classs('cloudapi');
//        $api = new CloudApi(true);
//        $iframe = $api->url('debug', 'settingsDisplay', array(
//            'referer' => urlencode($_W['siteurl']),
//            'version' => $this->module['version'],
//            'v' => random(3),
//        ), 'html');
//        if (is_error($iframe)) {
//            message($iframe['message'], '', 'error');
//        }
//
//        if($_W['ispost']) {
//            $setting = $_GPC['setting'];
//            $setting = $api->post('debug', 'saveSettings', array('setting' => $setting, 'version' => $this->module['version'], 'v' => random(3),), 'json');
//            if (is_error($setting)) {
//                die("<script>alert('{$setting['message']}');location.href = '{$iframe}';</script>");
//            }
//            $this->saveSettings($setting);
//            die("<script>location.href = '{$iframe}';</script>");
//        }

        include $this->template('setting');
    }

//	public function welcomeDisplay($menus = array()) {
//        global $_W;
//		//这里来展示DIY管理界面
//		include $this->template('welcome');
//	}
}