<?php
/**
 * 测试模块模块微站定义
 *
 * @author 姜海蕤
 * @url http://bbs.we7.cc/
 */
defined('IN_IA') or exit('Access Denied');
function p($arr) {
    echo '<pre>';
    print_r($arr);
    echo '</pre>';
}
function json($data) {
    exit(json_encode($data));
}
function include_class($className) {
    require_once '../addons/jiang_first/class/' . $className . '.php';
}
class Jiang_firstModuleSite extends WeModuleSite {

    public function doMobileIndex() {
        //这个操作被定义用来呈现 功能封面
        global $_W,$_GPC;
//		echo '<pre>';
//		print_r($_W['fans']);
//		echo '</pre>';die;
        $this->checkWeixin();
        $this->addMember();
        $sharedata = $this->getSharedata();
        $today_num = intval(date('d'));
        $list = pdo_fetchall("SELECT stime FROM ims_buhouwa_sign WHERE DATE_FORMAT(stime,'%Y-%m')=DATE_FORMAT(NOW(),'%Y-%m') AND openid=:openid",array(':openid'=>$_W['openid']));
        $my_sign_arr = [];
        foreach($list as $v) {
            $my_sign_arr[] = intval(date('d',strtotime($v['stime'])));
        }
        $my_sign_record = [];
        foreach(range(1,$today_num) as $v) {
            if(in_array($v,$my_sign_arr)) {
                $my_sign_record[] = 1;
            }else {
                $my_sign_record[] = 2;
            }
        }
        $sign_record_str = implode(',',$my_sign_record);
        include $this->template('index');
    }

    public function doMobileSign() {
        global $_W;
        $this->checkWeixin();
        $exist = pdo_fetch("SELECT * FROM ims_buhouwa_sign WHERE openid=:openid AND TO_DAYS(stime)=TO_DAYS(NOW())",array(':openid'=>$_W['openid']));
        if($exist) {
            json(2);
        }else {
            $insert_data = array(
                'openid'=>$_W['openid'],
            );
            $result = pdo_insert("buhouwa_sign",$insert_data);
            if($result) {
                pdo_query("UPDATE ims_buhouwa_member SET sign_count=sign_count+1 WHERE openid=:openid",array(':openid'=>$_W['openid']));
                json(1);
            }else {
                json('签到失败');
            }
        }
    }

    public function doMobileTips() {
        $this->checkWeixin();
        if(!$this->checkSign()) {
            message("请先签到",$this->createMobileUrl('index'),'info');
        }
        $sharedata = $this->getSharedata();
        include $this->template('tips');
    }

    public function doMobilePrepare() {
        global $_W,$_GPC;
        $this->checkWeixin();
        if(!$this->checkSign()) {
            message("请先签到",$this->createMobileUrl('index'),'info');
        }
        $sharedata = $this->getSharedata();
//		$ifend = $this->checkIfEnd();
        $exist = pdo_fetchcolumn("SELECT prepare_detail FROM ims_buhouwa_sign WHERE TO_DAYS(stime)=TO_DAYS(NOW()) AND openid=:openid",array(':openid'=>$_W['openid']));
        if($exist) {
            $detail = json_decode($exist,true);
            $key_arr = array_keys($detail);
        }
        $list = pdo_fetchall("SELECT * FROM ims_buhouwa_prepare WHERE is_recommend=1");
        include $this->template('zhunbei');
    }

    public function doMobilePrepareAjax() {
        global $_W,$_GPC;
//		$ifend = $this->checkIfEnd();
//		if($ifend) {
//			json(2);
//		}
        $list = pdo_fetchall("SELECT * FROM ims_buhouwa_prepare WHERE is_recommend=1");
        $sub_arr_key = array_keys($_GPC);
        $sub_arr = [];
        foreach($list as $v) {
            if(in_array("radio-".$v['id'],$sub_arr_key)) {
                $sub_arr[$v['id']] = $_GPC['radio-'.$v['id']];
            }
        }
        $prepare_detail = json_encode($sub_arr);
        $result = pdo_query("UPDATE ims_buhouwa_sign SET prepare_detail=:prepare_detail WHERE TO_DAYS(stime)=TO_DAYS(NOW()) AND openid=:openid",array(":prepare_detail"=>$prepare_detail,":openid"=>$_W['openid']));
        if($result!==false) {
            json(1);
        }else {
            json('操作异常');
        }
    }

    public function doMobileHomeworkBegin() {
        $this->checkWeixin();
        if(!$this->checkSign()) {
            message("请先签到",$this->createMobileUrl('index'),'info');
        }
        $sharedata = $this->getSharedata();
        include $this->template('homeworkBegin');
    }

    public function doMobileChangYongJuXing() {
        $this->checkWeixin();
        if(!$this->checkSign()) {
            message("请先签到",$this->createMobileUrl('index'),'info');
        }
//		$ifend = $this->checkIfEnd();
        $sharedata = $this->getSharedata();
        $list = pdo_fetchall("SELECT * FROM ims_buhouwa_remind_juxing ORDER BY sort ASC");
        $list_num = count($list);
        include $this->template('changYongJuXing');
    }

    public function doMobileQiuzhu() {
        $this->checkWeixin();
        if(!$this->checkSign()) {
            message("请先签到",$this->createMobileUrl('index'),'info');
        }
//		$ifend = $this->checkIfEnd();
        $sharedata = $this->getSharedata();
        $list = pdo_fetchall("SELECT * FROM ims_buhouwa_remind_scene");
        $new_list = [];
        foreach($list as $v) {
            $list_son = pdo_fetchall("SELECT * FROM ims_buhouwa_remind_words WHERE sid=:sid  AND is_recommend=1 ORDER BY sort ASC",array(":sid"=>$v['id']));
            $v['son'] = $list_son;
            $new_list[] = $v;
        }
        include $this->template('qiuzhu');
    }

    public function doMobilePraise() {
        global $_W,$_GPC;
        $edit_arr = $_GPC['arr'];
        $sign_id = pdo_fetchcolumn("SELECT id FROM ims_buhouwa_sign WHERE openid=:openid AND TO_DAYS(stime)=TO_DAYS(NOW())",array(':openid'=>$_W['openid']));
        if(!empty($edit_arr)) {
            $sql_arr = [];
            foreach($edit_arr as $v) {
                $sql_arr[] = "(".$sign_id.",'".$v."')";
            }
            $sql = implode(',',$sql_arr);
            $res = pdo_query("INSERT INTO ims_buhouwa_edit (sign_id,content) VALUES ".$sql);
            if($res) {
                pdo_query("UPDATE ims_buhouwa_sign SET praise_times=praise_times+:times WHERE id=:id",array(':id'=>$sign_id,':times'=>count($sql_arr)));
                json(1);
            }else {
                json(-1);
            }
        }else {
            json(1);
        }
    }

    public function doMobileBiaoyangjilu() {
        global $_W,$_GPC;
        $this->checkWeixin();
        if(!$this->checkSign()) {
            message("请先签到",$this->createMobileUrl('index'),'info');
        }
//		$ifend = $this->checkIfEnd();
        $sharedata = $this->getSharedata();
        $sign_id = pdo_fetchcolumn("SELECT id FROM ims_buhouwa_sign WHERE openid=:openid AND TO_DAYS(stime)=TO_DAYS(NOW())",array(':openid'=>$_W['openid']));
        $list = pdo_fetchall("SELECT * FROM ims_buhouwa_edit WHERE sign_id=:sign_id ORDER BY etime DESC",array(":sign_id"=>$sign_id));
        include $this->template('biaoyangjilu');
    }

    public function doMobilePinggu() {
        $this->checkWeixin();
        if(!$this->checkSign()) {
            message("请先签到",$this->createMobileUrl('index'),'info');
        }
//		$ifend = $this->checkIfEnd();
        $sharedata = $this->getSharedata();
        $taidu = pdo_fetchall("SELECT * FROM ims_buhouwa_pinggu WHERE type=1 AND is_recommend=1");
        $ganshou = pdo_fetchall("SELECT * FROM ims_buhouwa_pinggu WHERE type=2 AND is_recommend=1");
        $taidu_num = count($taidu);
        $ganshou_num = count($ganshou);
        include $this->template('pinggu');
    }

    public function doMobileResult() {
        global $_W,$_GPC;
        $taidu = pdo_fetchall("SELECT score FROM ims_buhouwa_pinggu WHERE type=1 AND is_recommend=1");
        $ganshou = pdo_fetchall("SELECT score FROM ims_buhouwa_pinggu WHERE type=2 AND is_recommend=1");
        $taidu_arr = [];
        $ganshou_arr = [];
        foreach($taidu as $v) {
            $taidu_arr[] = $v['score'];
        }
        foreach($ganshou as $v) {
            $ganshou_arr[] = $v['score'];
        }
        if(count($_GPC['taidu']) == 1) {
            $taidu_score = $taidu_arr[$_GPC['taidu'][0]];
        }else {
            $taidu_score = ($taidu_arr[$_GPC['taidu'][0]]+$taidu_arr[$_GPC['taidu'][1]])/2;
        }
        if(count($_GPC['ganshou']) == 1) {
            $ganshou_score = $ganshou_arr[$_GPC['ganshou'][0]];
        }else {
            $ganshou_score = ($ganshou_arr[$_GPC['ganshou'][0]]+$ganshou_arr[$_GPC['ganshou'][1]])/2;
        }
        $qingxushangshu = ($taidu_score+$ganshou_score)/2;
        $res = pdo_query("UPDATE ims_buhouwa_sign SET score=:score WHERE openid=:openid AND TO_DAYS(stime)=TO_DAYS(NOW())",array(':score'=>$qingxushangshu,':openid'=>$_W['openid']));
        if($res !== false) {
            json(1);
        }else {
            json(-1);
        }
    }

    public function doMobileIfresult() {
        json(1);
//		if(!$this->checkIfEnd()) {
//			json(1);
//		}else {
//			json(-1);
//		}
    }

    public function doMobileQingxushangshu() {
        global $_W,$_GPC;
        $this->checkWeixin();
        $openid = $_W['openid'];
        $myinfo = pdo_fetch("SELECT nickname,collect_count,avatar FROM ims_buhouwa_member WHERE openid=:openid",array(':openid'=>$openid));
        if(!$this->checkSign()) {
            message("请先签到",$this->createMobileUrl('index'),'info');
        }
        $exist = pdo_fetch("SELECT * FROM ims_buhouwa_sign WHERE openid=:openid AND TO_DAYS(stime)=TO_DAYS(NOW())",array(':openid'=>$openid));
        if($exist['score'] == '') {
            message('非法操作',$this->createMobileUrl('index'),'error');
        }
        $sharedata = $this->getSharedata();
        $content = pdo_fetchcolumn("SELECT content FROM ims_buhouwa_pinggu WHERE type=3 AND start_score<:score AND end_score>=:score ORDER BY RAND() LIMIT 0,1",array(':score'=>$exist['score']));
        $rank = pdo_fetchcolumn("SELECT r.rank FROM (SELECT t.*,(SELECT COUNT(s.score)+1 FROM ims_buhouwa_sign s WHERE s.score>t.score AND TO_DAYS(s.stime)=TO_DAYS(NOW())) rank FROM ims_buhouwa_sign t WHERE TO_DAYS(t.stime)=TO_DAYS(NOW()) ORDER BY rank ASC) r WHERE r.openid=:openid",array(':openid'=>$openid));
        $keep_days = pdo_fetchcolumn("SELECT COUNT(openid) FROM ims_buhouwa_sign WHERE openid=:openid",array(':openid'=>$openid));
        $praise_times = pdo_fetchcolumn("SELECT COUNT(id) FROM ims_buhouwa_edit WHERE sign_id=:sign_id",array(':sign_id'=>$exist['id']));
        $qrcode = pdo_fetchcolumn("SELECT qrcode FROM ims_buhouwa_activity");
        $sharedata['link'] = $_W['siteroot'] .'app/'. $this->createMobileUrl('qingxushangshu') . '&openid=' . $openid;

        $touch_share_img = '../addons/jiang_first/class/sharepic.php?openid=' . $openid . '&content=' . $content . '&nickname=' . $myinfo['nickname']
            . '&rank=' . $rank . '&keep_days=' . $keep_days . '&praise_times=' . $praise_times . '&collect_count=' . $myinfo['collect_count'] . '&score=' . $exist['score'] . '&avatar_path=' . $myinfo['avatar'];
        //echo '<img src="'.$touch_share_img.'">';die;
        include $this->template('qingxushangshu');
    }

    public function checkSign() {
        global $_W;
        $exist = pdo_fetch("SELECT * FROM ims_buhouwa_sign WHERE openid=:openid AND TO_DAYS(stime)=TO_DAYS(NOW())",array(':openid'=>$_W['openid']));
        if($exist) {
            return true;
        }else {
            return false;
        }
    }

    public function checkIfEnd() {
        global $_W;
        $exist = pdo_fetchcolumn("SELECT score FROM ims_buhouwa_sign WHERE openid=:openid AND TO_DAYS(stime)=TO_DAYS(NOW())",array(':openid'=>$_W['openid']));
        if($exist['score'] == '') {
            return false;
        }else {
            return true;
        }
    }

    private function getSharedata() {
        global $_W;
        $info = pdo_fetch("SELECT * FROM ims_buhouwa_activity WHERE uniacid=:uniacid",array(':uniacid'=>$_W['uniacid']));
        $share['title'] = htmlspecialchars_decode($info['title']);
        $share['desc'] = $info['desc'];
        $share['cover'] = $_W['siteroot'] .'attachment/'. $info['cover'];
        $share['link'] = $_W['siteroot'] .'app/'. $this->createMobileUrl('index');
        return $share;
    }

    public function doWebRule() {
        //这个操作被定义用来呈现 规则列表
    }
    public function doWebMain() {
        global $_W,$_GPC;
        include_class('page');
        $count = pdo_fetchcolumn("SELECT COUNT(id) FROM ims_buhouwa_sign");
        $Page = new \Page($count, 8, 5);
        $_GPC['do'] = 'menu';
        $list = pdo_fetchall("SELECT s.*,m.nickname,m.avatar,m.sign_count,m.collect_count FROM ims_buhouwa_sign s LEFT JOIN ims_buhouwa_member m ON s.openid=m.openid LIMIT " . ($Page->start()-1) . "," . $Page->cnums());
        $page = $Page->fpage(4, 5, 6);

        include $this->template('manage');
    }
    public function doWebReminder() {
        //这个操作被定义用来呈现 管理中心导航菜单
        global $_W,$_GPC;
        $act = $_GPC['act'];
        if($act == 'addscene') {
            include $this->template('addscene');
        }
        if($act == 'addwords') {
            $sid = $_GPC['sid'];
            include $this->template('addwords');
        }
        if($act == 'modscene') {
            $wid = $_GPC['sid'];
            $res = pdo_fetch("SELECT * FROM ims_buhouwa_remind_scene WHERE id=:id",array(":id"=>$wid));
            include $this->template('modscene');
        }
        if($act == 'modwords') {
            $wid = $_GPC['wid'];
            $res = pdo_fetch("SELECT * FROM ims_buhouwa_remind_words WHERE id=:id",array(":id"=>$wid));
            include $this->template('modwords');
        }
        if($act == 'words') {
            $sid = $_GPC['sid'];
            $list = pdo_fetchall("SELECT * FROM ims_buhouwa_remind_words WHERE sid=:sid ORDER BY sort ASC",array(':sid'=>$sid));
            include $this->template('words');
        }
        if($act == 'prepare') {
            $list = pdo_fetchall("SELECT * FROM ims_buhouwa_prepare");
            include $this->template('prepare');
        }
        if($act == 'addprepare') {
            include $this->template('addprepare');
        }
        if($act == 'modprepare') {
            $res = pdo_fetch("SELECT * FROM ims_buhouwa_prepare WHERE id=:id",array(':id'=>$_GPC['id']));
            include $this->template('modprepare');
        }
        if($act == 'pinggu') {
            $list = pdo_fetchall("SELECT * FROM ims_buhouwa_pinggu WHERE type!=3");
            include $this->template('pinggu');
        }
        if($act == 'addpinggu') {
            include $this->template('addpinggu');
        }
        if($act == 'modpinggu') {
            $res = pdo_fetch("SELECT * FROM ims_buhouwa_pinggu WHERE id=:id",array(':id'=>$_GPC['id']));
            include $this->template('modpinggu');
        }
        if($act == 'result') {
            $list = pdo_fetchall("SELECT * FROM ims_buhouwa_pinggu WHERE type=3");
            include $this->template('result');
        }
        if($act == 'addresult') {
            include $this->template('addresult');
        }
        if($act == 'modresult') {
            $res = pdo_fetch("SELECT * FROM ims_buhouwa_pinggu WHERE id=:id",array(':id'=>$_GPC['id']));
            include $this->template('modresult');
        }
        if($act == 'juxing') {
            $list = pdo_fetchall("SELECT * FROM ims_buhouwa_remind_juxing ORDER BY sort ASC");
            include $this->template('juxing');
        }
        if($act == 'addjuxing') {
            include $this->template('addjuxing');
        }
        if($act == 'modjuxing') {
            $wid = $_GPC['jid'];
            $res = pdo_fetch("SELECT * FROM ims_buhouwa_remind_juxing WHERE id=:id",array(":id"=>$wid));
            include $this->template('modjuxing');
        }
        $act = 'reminder';
        $list = pdo_fetchall("SELECT * FROM ims_buhouwa_remind_scene");
        include $this->template('reminder');
    }
    public function doWebAddscene() {
        //这个操作被定义用来呈现 管理中心导航菜单
        global $_W,$_GPC;
        $insert_data = array(
            'scene' => $_GPC['scene'],
        );
        $result = pdo_insert("buhouwa_remind_scene",$insert_data);
        if($result) {
            message('添加成功',$this->createWebUrl('reminder'),'success');
        }else {
            message('添加失败','','error');
        }

    }

    public function doWebAddwords() {
        //这个操作被定义用来呈现 管理中心导航菜单
        global $_W,$_GPC;
        $insert_data = array(
            'words' => $_GPC['words'],
            'sid' => $_GPC['sid']
        );
        $result = pdo_insert("buhouwa_remind_words",$insert_data);
        if($result) {
            message('添加成功',$this->createWebUrl('reminder',array('act'=>'words','sid'=>$_GPC['sid'])),'success');
        }else {
            message('添加失败','','error');
        }

    }

    public function doWebAddJuxing() {
        //这个操作被定义用来呈现 管理中心导航菜单
        global $_W,$_GPC;
        $insert_data = array(
            'words' => $_GPC['words'],
            'sort' => 0
        );
        $result = pdo_insert("buhouwa_remind_juxing",$insert_data);
        if($result) {
            message('添加成功',$this->createWebUrl('reminder',array('act'=>'juxing')),'success');
        }else {
            message('添加失败','referer','error');
        }

    }

    public function doWebAddprepare() {
        //这个操作被定义用来呈现 管理中心导航菜单
        global $_W,$_GPC;
        $insert_data = array(
            'title' => $_GPC['prepare'],
        );
        $result = pdo_insert("buhouwa_prepare",$insert_data);
        if($result) {
            message('添加成功',$this->createWebUrl('reminder',array('act'=>'prepare')),'success');
        }else {
            message('添加失败','referer','error');
        }

    }

    public function doWebAddpinggu() {
        //这个操作被定义用来呈现 管理中心导航菜单
        global $_W,$_GPC;
        $insert_data = array(
            'content' => $_GPC['content'],
            'score' => $_GPC['score'],
            'type' => $_GPC['type']
        );
        $result = pdo_insert("buhouwa_pinggu",$insert_data);
        if($result) {
            message('添加成功',$this->createWebUrl('reminder',array('act'=>'pinggu')),'success');
        }else {
            message('添加失败','referer','error');
        }

    }

    public function doWebAddresult() {
        //这个操作被定义用来呈现 管理中心导航菜单
        global $_W,$_GPC;
        $insert_data = array(
            'content' => $_GPC['content'],
            'start_score' => $_GPC['start_score'],
            'end_score' => $_GPC['end_score'],
            'type' => 3
        );
        $result = pdo_insert("buhouwa_pinggu",$insert_data);
        if($result) {
            message('添加成功',$this->createWebUrl('reminder',array('act'=>'result')),'success');
        }else {
            message('添加失败','referer','error');
        }

    }

    public function doWebRemindmodify() {
        //这个操作被定义用来呈现 管理中心导航菜单
        global $_W,$_GPC;
        $act = $_GPC['act'];
        if($act == 'modwords') {
            $update_data = array(
                'words' => $_GPC['words'],
            );
            $result = pdo_update("buhouwa_remind_words",$update_data,array('id'=>$_GPC['wid']));
            if($result !== false) {
                message('修改成功',$this->createWebUrl('reminder',array('act'=>'words','sid'=>$_GPC['sid'])),'success');
            }else {
                message('修改失败','referer','error');
            }
        }
        if($act == 'modjuxing') {
            $update_data = array(
                'words' => $_GPC['words'],
            );
            $result = pdo_update("buhouwa_remind_juxing",$update_data,array('id'=>$_GPC['jid']));
            if($result !== false) {
                message('修改成功',$this->createWebUrl('reminder',array('act'=>'juxing')),'success');
            }else {
                message('修改失败','referer','error');
            }
        }
        if($act == 'modscene') {
            $update_data = array(
                'scene' => $_GPC['scene'],
            );
            $result = pdo_update("buhouwa_remind_scene",$update_data,array('id'=>$_GPC['sid']));
            if($result !== false) {
                message('修改成功',$this->createWebUrl('reminder',array('act'=>'scene')),'success');
            }else {
                message('修改失败','referer','error');
            }
        }
        if($act == 'modprepare') {
            $update_data = array(
                'title' => $_GPC['prepare'],
            );
            $result = pdo_update("buhouwa_prepare",$update_data,array('id'=>$_GPC['pid']));
            if($result !== false) {
                message('修改成功',$this->createWebUrl('reminder',array('act'=>'prepare')),'success');
            }else {
                message('修改失败','referer','error');
            }
        }
        if($act == 'modpinggu') {
            $update_data = array(
                'content' => $_GPC['content'],
                'score' => $_GPC['score'],
                'type' => $_GPC['type']
            );
            $result = pdo_update("buhouwa_pinggu",$update_data,array('id'=>$_GPC['id']));
            if($result !== false) {
                message('修改成功',$this->createWebUrl('reminder',array('act'=>'pinggu')),'success');
            }else {
                message('修改失败','referer','error');
            }
        }
        if($act == 'modresult') {
            $update_data = array(
                'content' => $_GPC['content'],
                'start_score' => $_GPC['start_score'],
                'end_score' => $_GPC['end_score']
            );
            $result = pdo_update("buhouwa_pinggu",$update_data,array('id'=>$_GPC['id']));
            if($result !== false) {
                message('修改成功',$this->createWebUrl('reminder',array('act'=>'result')),'success');
            }else {
                message('修改失败','referer','error');
            }
        }

    }

    public function doWebPraiseinfo() {
        global $_W,$_GPC;
        $act = $_GPC['act'];
        if($act == 'info') {
            $sign_id = $_GPC['id'];
            $list = pdo_fetchall("SELECT * FROM ims_buhouwa_edit WHERE sign_id=:sign_id AND TO_DAYS(etime)=TO_DAYS(:etime)",array(':sign_id'=>$sign_id,':etime'=>$_GPC['etime']));
            $nickname = $_GPC['nickname'];
            include $this->template("info");
        }
        if($act == 'collect') {
            $eid = $_GPC['id'];
            $sign_id = $_GPC['sign_id'];
            $status = $_GPC['status'];
            $words = pdo_fetchcolumn("SELECT content FROM ims_buhouwa_edit WHERE id=:id",array(":id"=>$eid));
            $openid = pdo_fetchcolumn("SELECT openid FROM ims_buhouwa_sign WHERE id=:id",array(":id"=>$sign_id));
            $res = pdo_update("buhouwa_edit",array("if_collect"=>$status),array("id"=>$eid));
            if($res) {
                if($_GPC['status'] == 1) {
                    pdo_insert("buhouwa_remind_words",array("words"=>$words,'eid'=>$eid,'sid'=>2));
                    pdo_query("UPDATE ims_buhouwa_member SET collect_count=collect_count+1 WHERE openid=:openid",array(":openid"=>$openid));
                }else {
                    pdo_delete("buhouwa_remind_words",array('eid'=>$eid));
                    pdo_query("UPDATE ims_buhouwa_member SET collect_count=collect_count-1 WHERE openid=:openid",array(":openid"=>$openid));
                }
                json(1);
            }else {
                json(-1);
            }
        }
    }

    public function doWebDel() {
        //这个操作被定义用来呈现 管理中心导航菜单
        global $_W,$_GPC;
        $act = $_GPC['act'];
        if($act == 'delwords') {
            $result = pdo_delete("buhouwa_remind_words" , array('id'=>$_GPC['wid']));
            if($result) {
                message('删除成功','referer','success');
            }else {
                message('删除失败','referer','error');
            }
        }
        if($act == 'deljuxing') {
            $result = pdo_delete("buhouwa_remind_juxing" , array('id'=>$_GPC['jid']));
            if($result) {
                message('删除成功','referer','success');
            }else {
                message('删除失败','referer','error');
            }
        }
        if($act == 'delscene') {
            $result = pdo_delete("buhouwa_remind_scene" , array('id'=>$_GPC['id']));
            if($result) {
                pdo_delete("buhouwa_remind_words" , array('sid'=>$_GPC['id']));
                message('删除成功','referer','success');
            }else {
                message('删除失败','referer','error');
            }
        }
        if($act == 'delprepare') {
            $result = pdo_delete("buhouwa_prepare" , array('id'=>$_GPC['id']));
            if($result) {
                message('删除成功','referer','success');
            }else {
                message('删除场景失败','referer','error');
            }
        }
        if($act == 'delpinggu') {
            $result = pdo_delete("buhouwa_pinggu" , array('id'=>$_GPC['id']));
            if($result) {
                message('删除成功','referer','success');
            }else {
                message('删除场景失败','referer','error');
            }
        }
        if($act == 'delpraise') {
            $result = pdo_delete("buhouwa_edit" , array('id'=>$_GPC['id']));
            if($result) {
                message('删除成功','referer','success');
            }else {
                message('删除场景失败','referer','error');
            }
        }

    }

    public function doWebSort() {
        global $_W,$_GPC;
        $res = pdo_update("buhouwa_remind_juxing",array('sort'=>$_GPC['sort']),array('id'=>$_GPC['id']));
        if($res !== false) {
            json(1);
        }else {
            json(-1);
        }
    }
    public function doWebWordssort() {
        global $_W,$_GPC;
        $res = pdo_update("buhouwa_remind_words",array('sort'=>$_GPC['sort']),array('id'=>$_GPC['id']));
        if($res !== false) {
            json(1);
        }else {
            json(-1);
        }
    }

    public function doWebRecommend() {
        global $_W,$_GPC;
        $act = $_GPC['act'];
        if($act == 'pinggu') {
            $result = pdo_query("UPDATE ims_buhouwa_pinggu SET is_recommend=:status WHERE id=:id",array(':status'=>$_GPC['status'],':id'=>$_GPC['id']));
            if($result !== false) {
                json(1);
            }else {
                json(-1);
            }
        }
        if($act == 'prepare') {
            $result = pdo_query("UPDATE ims_buhouwa_prepare SET is_recommend=:status WHERE id=:id",array(':status'=>$_GPC['status'],':id'=>$_GPC['id']));
            if($result !== false) {
                json(1);
            }else {
                json(-1);
            }
        }
        if($act == 'words') {
            $result = pdo_query("UPDATE ims_buhouwa_remind_words SET is_recommend=:status WHERE id=:id",array(':status'=>$_GPC['status'],':id'=>$_GPC['id']));
            if($result !== false) {
                json(1);
            }else {
                json(-1);
            }
        }

    }

    public function doWebQrcode() {
        global $_W,$_GPC;
        $pic = pdo_fetchcolumn("SELECT qrcode FROM ims_buhouwa_activity");
        include $this->template('qrcode');
    }

    public function doWebAddqrcode() {
        global $_W,$_GPC;
        if($_FILES['file']['name'] != '') {
            $info = $this->uploadfile('file');
            if($info['result']) {
                $oldpath = pdo_fetchcolumn("SELECT qrcode FROM ims_buhouwa_activity");
                @unlink($oldpath);
                $res = pdo_query("UPDATE ims_buhouwa_activity SET qrcode=:savepath",array(':savepath'=>$info['savepath']));
                if($res) {
                    include_class('phpqrcode/qrlib');
                    $content = $_W['siteroot'] . 'app/' . $this->createMobileUrl('index');
                    $qrcode_path = '../addons/jiang_first/class/qrcode.png';
                    QRcode::png($content,$qrcode_path,QR_ECLEVEL_L,4,4);
                    json(1);
                }else {
                    json(-1);
                }
            }else {
                json($info['msg']);
            }
        }else {
            json('没有上传新图片');
        }


    }

    public function doMobileBanner() {
        //这个操作被定义用来呈现 微站首页导航图标
    }
    public function doMobileHome() {
        //这个操作被定义用来呈现 微站个人中心导航
    }
    public function doMobileShot() {
        //这个操作被定义用来呈现 微站快捷功能导航
    }
    public function doWebIndependent() {
        //这个操作被定义用来呈现 微站独立功能
    }

    private function addMember() {
        global $_W;
        $savepath = '../addons/jiang_first/class/avatar/';
        $exist = pdo_fetch("SELECT * FROM ims_buhouwa_member WHERE openid=:openid AND uniacid=:uniacid",array(':openid'=>$_W['openid'],':uniacid'=>$_W['uniacid']));
        if($exist) {
            return true;
        }else {
//            $avatar = substr($_W['fans']['avatar'],0,-3);
            $avatar = $_W['fans']['avatar'];
            $insert_data = array(
                'nickname' => $_W['fans']['nickname'],
                'openid' => $_W['openid'],
                'avatar' => $avatar,
                'uniacid'=> $_W['uniacid']
            );
            pdo_insert("buhouwa_member",$insert_data);

            $imghttp = get_headers($avatar,true);
            if($imghttp) {
                if($imghttp['Content-Type'] == "image/jpeg") {
                    $filename = $_W['openid'] . '.jpg';
                }else if($imghttp['Content-Type'] == "image/png") {
                    $filename = $_W['openid'] . '.png';
                }else {
                    $filename = $_W['openid'] . '.jpg';
                }
                $this->getImage($avatar,$savepath,$filename,1);
            }
        }
    }

    private function is_weixin() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    private function checkWeixin() {
        global $_W;
        if($_W['openid'] == '' || !$this->is_weixin()) {
            message('请登录微信','','error');
        }
        if (empty($_W['fans']['nickname'])) {
            mc_oauth_userinfo();
        }
    }

    private function uploadfile($name) {
        if($_FILES[$name]["size"] < 2000) {
            return array('result'=>false,'msg'=>'图片太大');
        }
        $filetype = $_FILES[$name]['type'];
        if ($filetype == "image/bmp" || $filetype == "image/gif" || $filetype == "image/jpeg" || $filetype == "image/png" || $filetype == "image/pjpeg") {
            if ($_FILES[$name]["error"] > 0) {
                return array('result'=>false,'msg'=>'上传失败');
            }else {
                $filename = $_FILES[$name]['name'];
                $arr = explode('.',$filename);
                $ext = array_pop($arr);
                $letter = range('a','z');
                $rand_name = $letter[rand(0,25)] . $letter[rand(0,25)] . $letter[rand(0,25)] . $letter[rand(0,25)];
                $dst = "../addons/jiang_first/template/upload/" . $rand_name . time() .'.'. $ext;
                if(move_uploaded_file($_FILES[$name]["tmp_name"], $dst)) {
                    return array('result'=>true,'msg'=>'上传成功','savepath'=>$dst);
                }else {
                    return array('result'=>false,'msg'=>'移动临时文件失败');
                }
            }
        }else {
            return array('result'=>false,'msg'=>'图片格式不符');
        }
    }

    private function getImage($url,$save_dir='',$filename='',$type=0){
        if(trim($url)==''){
            return array('file_name'=>'','save_path'=>'','error'=>1);
        }
        if(trim($save_dir)==''){
            $save_dir='./';
        }
        if(trim($filename)==''){//保存文件名
            $ext=strrchr($url,'.');
            if($ext!='.gif'&&$ext!='.jpg'){
                return array('file_name'=>'','save_path'=>'','error'=>3);
            }
            $filename=time().$ext;
        }
        if(0!==strrpos($save_dir,'/')){
            $save_dir.='/';
        }
        //创建保存目录
        if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
            return array('file_name'=>'','save_path'=>'','error'=>5);
        }
        //获取远程文件所采用的方法
        if($type){
            $ch=curl_init();
            $timeout=5;
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
            $img=curl_exec($ch);
            curl_close($ch);
        }else{
            ob_start();
            readfile($url);
            $img=ob_get_contents();
            ob_end_clean();
        }
        //$size=strlen($img);
        //文件大小
        $fp2=@fopen($save_dir.$filename,'a');
        fwrite($fp2,$img);
        fclose($fp2);
        unset($img,$url);
        return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
    }

	public function doWebRulelist() {
		//这个操作被定义用来呈现 规则列表
	}

	public function doMobileFast() {
		//这个操作被定义用来呈现 微站快捷功能导航
	}

}