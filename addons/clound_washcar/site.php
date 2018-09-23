<?php
/**
 * 云洗车模块微站定义
 *
 * @author zhb1190
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Clound_washcarModuleSite extends WeModuleSite {
	public $_img_url = '../addons/clound_washcar/template/style/img/';

	public $_css_url = '../addons/clound_washcar/template/style/css/';

	public $_script_url = '../addons/clound_washcar/template/style/js/';
	
	public $_carwash_card_type = array(2 => '全年不计次数', 1 => '计次卡',);

	public function doMobileIndex() {
		//这个操作被定义用来呈现 功能封面,test push
		echo "test";
		include $this->template('register');

	}
	public function doWebQuery() {
		//这个操作被定义用来呈现 规则列表
	}
	public function doWebUser() {
		//这个操作被定义用来呈现 管理中心导航菜单
		echo "</br>enter doWebUser";
		echo "</br>xxx=".$_GET['xxx'];
		include $this->template('member');
		
	}
	public function doWebStatis() {
		global $_GPC, $_W;
		//load()->func('tpl');
		
		$washchar_chains=$this->getallchains();
		
		$params=array();
		$params[':bdate']=$_GPC['selectdate']['start'].' 00:00:00';
		$params[':edate']=$_GPC['selectdate']['end'].' 23:59:59';
		$params[':chainid']=$_GPC['chainid'];
		$params[':weid']=$_W['uniacid'];

		//现金
		$sql='SELECT id,cashtype,SUM(money) as total FROM '.tablename('carwash_cash').' WHERE delflag=0 AND chainid=:chainid AND weid=:weid AND  cashdate>=:bdate AND cashdate<=:edate GROUP BY cashtype';
		$list=pdo_fetchall($sql, $params);
		$cashinfo=array('input'=>0.0,'output'=>0.0);
		foreach($list as $key=>$value) {
			if($value['cashtype']==1) {
				$cashinfo['output']=$value['total'];
			} else if($value['cashtype']==2){
				$cashinfo['input']=$value['total'];
			}
		}
		//工资信息
		//$salaryinfo=array();
		//$sql='SELECT id,name,salary from '.tablename('carwash_staff');
		
		//借款
		$loaninfo=array();
		$sql='SELECT a.id AS id, b.id as staffid, b.name as name, SUM(a.money) AS total FROM '.tablename('carwash_cash').' AS a LEFT JOIN '.tablename('carwash_staff')." AS b ON CONVERT(a.`name`,SIGNED)=b.id WHERE a.cashtype=3 AND a.cashdate>='".$params[':bdate']."' AND a.cashdate<='".$params[':edate']."' and a.weid={$params[':weid']} and a.delflag=0 GROUP BY b.id";
		$list=pdo_fetchall($sql, $params);
		foreach($list as $key=>$value) {
			$loaninfo[$value['staffid']]['money']=$value['total'];
			$loaninfo[$value['staffid']]['name']=$value['name'];
		}
		
		//会员卡
		$cardinfo=array('count'=>0.0,'year'=>0.0);
		$sql='SELECT id,`type`,SUM(price) as total FROM '.tablename('carwash_cards').' WHERE maincard=1 AND chainid=:chainid AND weid=:weid AND  saletime>=:bdate AND saletime<=:edate GROUP BY `type`';
		$list=pdo_fetchall($sql, $params);
		foreach($list as $key=>$value) {
			if($value['type']==1) {
				$cardinfo['count']=$value['total'];
			} else if($value['type']==2){
				$cardinfo['year']=$value['total'];
			}
		}
		
		//服务信息
		$serviceinfo=array();
		$serviceinfo_money=0;
		foreach($washchar_chains as $key=>$value) {
			if($key!=$params[':chainid']) {
				$serviceinfo[$key]=array('count'=>0,'year'=>0);
			}
		}
		$sql='SELECT id,SUM(money) as total FROM '.tablename('carwash_washrecord').' WHERE usemember=0 AND chainid=:chainid AND weid=:weid AND  washdate>=:bdate AND washdate<=:edate';
		$item=pdo_fetch($sql, $params);
		$serviceinfo_money=$item['total'];
		
		$sql='SELECT b.chainid as chainid,b.type as `type`, SUM(a.cardcount) as total FROM '.tablename('carwash_washrecord').'as a LEFT JOIN '.tablename('carwash_cards').' as b ON a.memberid=b.id  WHERE a.usemember=1 AND a.chainid=:chainid AND a.weid=:weid AND  a.washdate>=:bdate AND a.washdate<=:edate AND b.chainid<>:chainid GROUP BY b.chainid, b.type';
		$list=pdo_fetchall($sql, $params);
		foreach($list as $key=>$value) {
			if($value['type']==1) {
				$serviceinfo[$value['chainid']]['count']=$value['total'];
			} else if($value['type']==2) {
				$serviceinfo[$value['chainid']]['year']=$value['total'];
			}
		}
		
		include $this->template('report');
	}
	
	public function doWebLoan() {
		global $_GPC, $_W;
		$tblname='carwash_cash';
		$op = $_GPC['op'];
		$weid = $_W['uniacid'];
		load()->func('tpl');
		if ($op == 'edit') {
			$id = intval($_GPC['id']);
			if (checksubmit('submit')) {
				$insert = array(
					'weid' => $weid,
					'name' => $_GPC['staffid'],
					'money'=>$_GPC['money'],
					'cashtype' => 3,
					'chainid'=>$_GPC['chainid'],
					'description' => $_GPC['description'],
					'cashdate' => $_GPC['cashtime'],
					'delflag'=>0,
				);

				if (empty($id)) {
					$insert['recorddate']=date("Y-m-d H:m:s",time());
					pdo_insert($tblname, $insert);
				} else {
					pdo_update($tblname, $insert, array('id' => $id));
				}
				message("记录保存成功!", $this->createWebUrl('loan'), "success");
				exit();
			}
			
			$washchar_chains=$this->getallchains();

			if (!empty($id)) {
				$sql = 'SELECT a.id AS id, b.`name` AS `name`, b.`id` AS staffid, a.`money` AS money, a.`cashdate` AS cashdate,a.`chainid` AS chainid, a.`description` AS description FROM '. tablename('carwash_cash') .' AS a LEFT JOIN '. tablename('carwash_staff') .' AS b ON a.`name`=b.`id` WHERE a.`cashtype`=3 AND a.id=:id';
				$item = pdo_fetch($sql, array(':id' => $id));
				if (empty($item)) {
					message('抱歉，记录不存在或是已经删除！', '', 'error');
				}
			}
			include $this->template('loan_form');
		} else if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$insert = array(
				'delflag'=>1,
				'deldate'=>date("Y-m-d H:m:s",time())
			);
			pdo_update($tblname, $insert, array('id' => $id));

			message("服务信息删除成功!", $this->createWebUrl('loan'), "success");
		} else if ($op == 'deleteall') {
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				$insert = array(
					'delflag'=>1,
					'deldate'=>date("Y-m-d H:m:s",time())
				);
				pdo_update($tblname, $insert, array('id' => $id));
			}
			$this->web_message('服务信息删除成功！', '', 0);
			exit();
		} else if ($op == 'undel') {
			$id = intval($_GPC['id']);
			$insert = array(
				'delflag'=>0,
				'deldate'=>date("Y-m-d H:m:s",time())
			);
			pdo_update($tblname, $insert, array('id' => $id));

			message("服务信息恢复成功!", $this->createWebUrl('loan'), "success");
		}else if ($op == 'querystaff') {
			$kwd = trim($_GPC['staffname']);
			$sql = 'SELECT id,name FROM ' . tablename('carwash_staff') . ' WHERE `weid`=:weid AND name =:name';
			$params = array();
			$params[':weid'] = $_W['uniacid'];
			$params[':name'] = $kwd;
			
			$item = pdo_fetch($sql, $params);
			$result=array();
			$result['success']=0;
			$result['name']="";
			$result['id']=-1;
			if(!empty($item)){
				$result['name']=$item['name'];
				$result['id']=$item['id'];
				$result['success']=1;
			}
			echo json_encode($result);
		} else {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$where = ' WHERE a.delflag=0 AND a.`weid` = :weid AND a.cashtype=3 ';
			$params = array(':weid' => $_W['uniacid']);
			$queryed=0;
						
			$kwd = trim($_GPC['staffname']);
			if (!empty($kwd)) {
				$queryed=1;
				$where .= ' AND b.name like :staffname';
				$params[':staffname'] = "%{$kwd}%";
			}
			
			if($_GPC['usequerytime']==1) {
				$queryed=1;
				$params[':ltime']=$_GPC['selectdate']['start'].' 00:00:00';
				$params[':btime']=$_GPC['selectdate']['end'].' 23:59:59';
				$where .= " AND a.cashdate>=:ltime AND a.cashdate<=:btime";
			}
			
			$washchar_chains=$this->getallchains();

			$sql = 'SELECT a.id AS id, b.`name` AS `name`, b.`id` AS staffid, a.`money` AS money, a.`cashdate` AS cashdate,a.`chainid` AS chainid, a.`description` AS description FROM '. tablename('carwash_cash') .' AS a LEFT JOIN '. tablename('carwash_staff') .' AS b ON a.`name`=b.`id` '. $where . ' ORDER BY `cashdate` DESC LIMIT ' . ($pindex - 1) * $psize . ',' . $psize;
			
			$list = pdo_fetchall($sql, $params);

			$where1 = str_replace("a.","",$where);
			$where1 = str_replace("b.","",$where1);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($tblname) . $where1, $params);
			
			$pager = pagination($total, $pindex, $psize);


			if (!empty($_GPC['export'])) {
				//输入了查询条件才导出所有的记录，否则只导出第一页
				if($queryed==1) {
					$sql = 'SELECT a.id AS id, b.`name` AS `name`, b.`id` AS staffid, a.`money` AS money, a.`cashdate` AS cashdate,a.`chainid` AS chainid, a.`description` AS description FROM '. tablename('carwash_cash') .' AS a LEFT JOIN '. tablename('carwash_staff') .' AS b ON a.`name`=b.`id` '. $where . ' ORDER BY `cashdate` DESC';
					
					$list = pdo_fetchall($sql, $params);
				}
			
				/* 输入到CSV文件 */
				$html = "\xEF\xBB\xBF";

				/* 输出表头 */
				$filter = array(
					'id' => '序号',
					'name' => '借款员工',
					'money' => '借款金额',
					'cashdate' => '借款日期',
					'chainid' => '店铺',
					'description' => '详情',
				);
								
				$loanmoney = array(); //年卡的钱数
				foreach($washchar_chains as $key => $value) {
					$loanmoney[$key]=array();
					$loanmoney[$key]['count']=0;//数量
					$loanmoney[$key]['money']=0.0;//借款金额
				}

				foreach ($filter as $key => $value) {
					$html .= $value . "\t,";
				}
				$html .= "\n";

				if($_GPC['usequerytime']==1) {
					$filename=$params[':ltime']."_".$params[':btime'];
				}else {
					$filename="all";
				}
				
				if (!empty($list)) {
					$status = array('1'=>'支出', '2'=>'收入');
					foreach ($list as $key => $value) {
						
						$itemcount = $itemcount + 1;
						$moneytype = 0;
						
						foreach ($filter as $index => $title) {
							if ($index == 'chainid'){
								$html .= $washchar_chains[$value[$index]]['name'] . "\t,";
							} else if ($index == 'description') {
								$detialtext = str_replace("\n"," ",$value[$index]);
								$detialtext = str_replace("\r"," ",$detialtext);
								$detialtext = str_replace("\t","    ",$detialtext);
								$detialtext = str_replace(",","；",$detialtext);
								$html .= $detialtext . "\t,";
								
							} else {
								$html .= $value[$index] . "\t,";
							}
						}
						$html .= "\n";
						
						$loanmoney[$value['chainid']]['count'] += 1;
						$loanmoney[$value['chainid']]['money'] += $value['money'];
					}					
				}

				$gapstr="\t,\t,\t,\t,\t,";
				$html .= "\t,--\t,--\t,--\t,--\t,--\t,--\t,\n";
				$html .= "1\t,起始时间: {$params[':ltime']}\t,{$gapstr}\n";
				$html .= "2\t,结束时间: {$params[':btime']}\t,{$gapstr}\n";
				$index=3;
				foreach($loanmoney as $key => $value) {
					$html .= "{$index}\t,{$washchar_chains[$key]['name']}店:总记录数{$loanmoney[$key]['count']}条;总计{$loanmoney[$key]['money']}元;\t,{$gapstr}\n";
					$index += 1;
				}
				
				/* 输出CSV文件 */
				if($queryed == 1) {
					$filename="{$_GPC['selectdate']['start']}_{$_GPC['selectdate']['end']}";
				} else {
					$filename="";
				}
				
				/* 输出CSV文件 */
				header("Content-type:text/csv");
				header("Content-Disposition:attachment; filename=借款信息{$filename}.csv");
				echo $html;
				exit();

			}

			include $this->template('loan');
		}
	}
	
	public function doWebCard() {
		global $_GPC, $_W;
		$tblname='carwash_cards';
		$op=$_GPC['op'];
		$weid=$_W['uniacid'];
		$carwash_card_type=$this->_carwash_card_type;
		
		if($op=='edit'){
			
			$id = intval($_GPC['id']);
			$no=$_GPC['cardno'];

			if (checksubmit('submit')) {

				$insert = array(
					'weid' => $weid,
					'name' => $_GPC['name'],
					'phone' => $_GPC['phone'],
					'type' => $_GPC['type'],
					'saletime' => $_GPC['saletime'],
					'begintime' => $_GPC['begintime'],
					'endtime' => $_GPC['endtime'],
					'times' => $_GPC['times'],
					'price' => $_GPC['price'],
					'chainid' => $_GPC['chainid'],
					'state' => $_GPC['cardstate'],
				);
				//删除所有的副卡
				pdo_delete($tblname,array('maincard'=>0,'no'=>$_GPC['carno']));
				
				if ($_GPC['device']) {
					$devices = array();
					foreach($_GPC['device'] as $key=>$value){
						$devices[]=$value;
					}
				}
				if(count($devices)>0){
					$insert['number']=$devices[0];
				}
				
				$insert['maincard']=1;
				if (empty($id)) {
					$insert['levavetime'] = $insert['times'];
					$insert['no'] = $_GPC['carno'].'-'.date("Y-m-d");
					pdo_insert($tblname, $insert);
				} else {
					$insert['no'] = $_GPC['carno'];
					$insert['levavetime']=$_GPC['leavetimes'];
					pdo_update($tblname, $insert, array('id' => $id));
				}
				if(count($devices)>0) {
					$skiped=0;
					foreach ($devices as $key => $deviceinsert) {
						if($skiped==1){
							$insert['maincard']=0;
							$insert['number']=$deviceinsert;
							pdo_insert($tblname, $insert);
						}
						$skiped=1;
					}
				}
				
				message("会员卡信息保存成功!", $this->createWebUrl('card'), "success");
				exit();
			}
			$sql = 'SELECT * FROM '. tablename($tblname) .' WHERE `no`= (SELECT `no` FROM '. tablename($tblname) .' WHERE `id`=:id )';
			$item = pdo_fetchall($sql, array(':id' => $id));
			$mainid=0;
			if(count($item)>0){
				$mainid=array_keys($item)[0];
			}
			//车牌号
			$numbers=array();
			if(count($item)>0){
				foreach ($item as $key=>$itemvalue) {
					$numbers[]=$itemvalue['number'];
					if($itemvalue['maincard']==1) {
						$mainid=$key;
					}
				}
			}
			//分店
			$sql = 'SELECT id,name FROM '.tablename('carwash_chains');
			$chainslist = pdo_fetchall($sql);
			if(empty($chainslist)) {
				message('抱歉，请先添加店铺信息！', '', 'error');
				exit();
			}
			$washchar_chains=array();
			foreach ($chainslist as $key=>$value) {
				$washchar_chains[]=array('id'=>$value['id'],'name'=>$value['name']);
			}
			
			include $this->template('card_form');
		}else if ($op == 'delete') {

			$id = intval($_GPC['id']);

			if (!empty($id)) {
				$item = pdo_fetch("SELECT no FROM " . tablename($tblname) . " WHERE id = :id LIMIT 1", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，获取会员卡号失败！', '', 'error');
					exit();
				}
				$carno=$item['no'];
				pdo_delete($tblname, array("no" => $carno));
			} else {
				message('抱歉，参数错误！', '', 'error');
				exit();
			}
			message("会员卡信息删除成功!", $this->createWebUrl('card'), "success");
		}else if ($op == 'deleteall') {
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);

				if (!empty($id)) {
					$item = pdo_fetch("SELECT no FROM " . tablename($tblname) . " WHERE id = :id LIMIT 1", array(':id' => $id));
					if (empty($item)) {
						message('抱歉，获取会员卡号失败！', '', 'error');
						exit();
					}
					$carno=$item['no'];
					pdo_delete($tblname, array("no" => $carno));
				} else {
					message('抱歉，参数错误！', '', 'error');
					exit();
				}
			}
			$this->web_message('会员卡信息删除成功！', '', 0);
			exit();
		} 
		else {
			$washchar_chains = $this->getallchains();
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$where = ' WHERE `weid` = :weid';
			$params = array(':weid' => $_W['uniacid']);
			
			$query=0;
			$kwd = trim($_GPC['cardno']);
			if(!empty($kwd)) {
				$where .= " AND `no` like :cardno";
				$params[':cardno'] = "%{$kwd}%";
				$query=1;
			}
			
			$kwd = trim($_GPC['phoneno']);
			if(!empty($kwd)) {
				$where .= " AND `phone` like :phoneno";
				$params[':phoneno'] = "%{$kwd}%";
				$query=1;
			}
			
			$kwd = trim($_GPC['carno']);
			if(!empty($kwd)) {
				$where .= " AND `number` like :carno";
				$params[':carno'] = "%{$kwd}%";
				$query=1;
			}
			
			$kwd = trim($_GPC['chainid']);
			if(intval($kwd)>0) {
				$where .= " AND `chainid` = :chainid";
				$params[':chainid'] = $kwd;
				$query=1;
			}
			
			if($_GPC['usequerytime']==1) {
				$params[':ltime']=$_GPC['selectdate']['start'].' 00:00:00';
				$params[':btime']=$_GPC['selectdate']['end'].' 23:59:59';
				$where .= " AND saletime>=:ltime AND saletime<=:btime";
				$query=1;
			}
			
			$sql = 'SELECT * FROM ' . tablename($tblname) . $where . ' ORDER BY `saletime` DESC LIMIT ' .
				($pindex - 1) * $psize . ',' . $psize;
				
			$list = pdo_fetchall($sql, $params);
			
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($tblname) . "$where", $params);
			$pager = pagination($total, $pindex, $psize);
			
			
			if (!empty($_GPC['export'])) {
				if($query==1) {
					$sql = 'SELECT * FROM ' . tablename($tblname) . $where . ' ORDER BY `saletime` DESC';
					$list = pdo_fetchall($sql, $params);
				}

				$list = pdo_fetchall($sql, $params);
			
				/* 输入到CSV文件 */
				$html = "\xEF\xBB\xBF";

				/* 输出表头 */
				$filter = array(
					'id' => '序号',
					'no' => '卡号',
					'maincard' => '主卡',
					'number' => '车牌号',
					'name' => '姓名',
					'phone' => '电话',
					'type' => '类型',
					'levavetimme' => '剩余次数',
					'times' => '次数',
					'state' => '状态',
					'price' => '价格',
					'begintime' => '开始时间',
					'endtime' => '结束时间',
					'saletime' => '销售时间',
					'chainid' => '店铺',
				);
				
				

				$itemcount = 0; //总记录数
				$cardmoney = array(); //年卡的钱数
				foreach($washchar_chains as $key => $value) {
					$cardmoney[$key]=array();
					$cardmoney[$key]['yearcount']=0;//年卡数量
					$cardmoney[$key]['timecount']=0;//次卡数量
					$cardmoney[$key]['yearmoney']=0.0;//年卡收入
					$cardmoney[$key]['timemoney']=0.0;//次卡收入
				}
								
				foreach ($filter as $key => $value) {
					$html .= $value . "\t,";
				}
				$html .= "\n";
				$cardstate=array('1'=>'启用','0'=>'未启用','2'=>'已过期');
				$typestr=array('1'=>'次卡','2'=>'年卡');
				if (!empty($list)) {
					foreach ($list as $key => $value) {
						foreach ($filter as $index => $title) {
							if($index=='state') {
								$html .= $cardstate[$value[$index]] . "\t, ";
							} else if($index=='type'){
								$html .= $typestr[$value[$index]] . "\t, ";
							} else if($index=='maincard') {
								if($value[$index]==1) {
									$html .= '是' . "\t, ";
								} else {
									$html .= '否' . "\t, ";
								}
							} else if($index=='chainid') {
								$html .= $washchar_chains[$value[$index]]['name'] . "\t";
							} else {
								$html .= $value[$index] . "\t, ";
							}
						}
						$html .= "\n";
						
						$itemcount += 1;
						if($value['maincard']==1) {
							if($value['type']==2) {
								$cardmoney[$value['chainid']]['yearcount'] += 1;
								$cardmoney[$value['chainid']]['yearmoney'] += $value['price'];
							} else {
								$cardmoney[$value['chainid']]['timecount'] += 1;
								$cardmoney[$value['chainid']]['timemoney'] += $value['price'];
							}
						}
					}
				}

				$gapstr="\t,\t,\t,\t,\t,\t,\t,\t,\t,\t,\t,\t,";
				$html .= "\t,--\t,--\t,--\t,--\t,--\t,--\t,--\t,--\t,--\t,--\t,--\t,--\t,--\t,\n";
				$html .= "1\t,起始时间: {$params[':ltime']}\t,{$gapstr}\n";
				$html .= "2\t,结束时间: {$params[':btime']}\t,{$gapstr}\n";
				$index=3;
				foreach($cardmoney as $key => $value) {
					$totalcard=$value['yearcount']+$value['timecount'];
					$totalmoney=$value['yearmoney']+$value['timemoney'];
					$html .= "{$index}\t,{$washchar_chains[$key]['name']}店:总{$totalcard}张;年卡{$value['yearcount']}张;次卡{$value['timecount']}张;";
					$html .= "总金额{$totalmoney}元;年卡{$value['yearmoney']}元;次卡{$value['timemoney']}元\t,{$gapstr}\n";
					$index += 1;
				}
				
				/* 输出CSV文件 */
				if($query == 1) {
					$filename="{$_GPC['selectdate']['start']}_{$_GPC['selectdate']['end']}";
				} else {
					$filename="会员卡信息";
				}
				header("Content-type:text/csv");
				header("Content-Disposition:attachment; filename=店铺数据{$filename}.csv");
				echo $html;
				exit();

			}
			
			include $this->template('card');
		}
	}
	
	
	public function doWebSign() {
		global $_GPC, $_W;
		$tblname='carwash_staffsign';
		$op=$_GPC['op'];
		$weid=$_W['uniacid'];
		if($op=='edit') {
			$currentdate= date('Y-m-d' ,time());
			$staffid=$_GPC['staffid'];
			if (checksubmit('submit')) {
				$days=$_GPC['totalday'];
				for($i=0; $i<$days; $i++) {
					
					$date=date_create($_GPC['date']['start']);
					date_add($date,date_interval_create_from_date_string($i." days"));
					$datekey=date_format($date,"Y-m-d");
					
					$signdata = array(
						'weid' => $weid,
						'staffid' => $_GPC['staffid'],
						'am' => 0,
						'pm'=> 0,
						'memo' => "",
						'chainid' => 0,
						'adate' => date("Y-m-d",strtotime($datekey)),
					);
				
					if(count($_GPC['checkam'])>0) {
						if(isset($_GPC['checkam'][$datekey])) {
							$signdata['am']=1;
						}
					}
					if(count($_GPC['checkpm'])>0) {
						if(isset($_GPC['checkpm'][$datekey])) {
							$signdata['pm']=1;
						}
					}
					if(count($_GPC['mem'])>0) {
						$signdata['memo']=$_GPC['mem'][$datekey];
					}
					if(count($_GPC['chain'])>0) {
						$signdata['chainid']=intval($_GPC['chain'][$datekey]);
					}
					
					$id=0;
					if(count($_GPC['id'])>0) {
						$id=intval($_GPC['id'][$datekey]);
					}
					echo "id=".empty($id);
					if (empty($id)) {
						pdo_insert($tblname, $signdata);
					}else{
						pdo_update($tblname, $signdata, array('id' => $id));
					}
				}
				message("员工签到信息保存成功!", $this->createWebUrl('sign'), "success");
				exit();
			}
			$washchar_chains=$this->getallchains();
			$sql="SELECT id,name,telephone FROM ".tablename('carwash_staff')." WHERE id=:id LIMIT 1";
			$staffitem = pdo_fetch($sql, array(':id' => $staffid));
			if(empty($staffitem)) {
				message('抱歉，员工信息错误！', '', 'error');
				exit();
			}
			
			if(!empty($_GPC['signdate'])) {
				$currentdate = $_GPC['signdate'];
			}
			$sql="SELECT * FROM ".tablename($tblname)." WHERE staffid=:id AND adate=:currentdate LIMIT 1";
			$item = pdo_fetch($sql, array(':id' => $staffid, ':currentdate' => $currentdate));
			
			include $this->template('staffsign_form');
		}else if($op=='ajaxquery'){
			$sql="SELECT * FROM ".tablename($tblname)." WHERE staffid=:id AND adate>=:startdate AND adate<=:enddate";
			$itemlist = pdo_fetchall($sql, array(':id' => $_GPC['staffid'], ':startdate'=>$_GPC['start'],':enddate'=>$_GPC['end']));
			$result=array();
			foreach($itemlist as $value) {
				$result[]=array('date'=>$value['adate'], 'am'=>$value['am'],'pm'=>$value['pm'],'chainid'=>$value['chainid'],'id'=>$value['id'],'mem'=>$value['memo']);
			}
			echo json_encode($result);
			exit();
		}else{
			//默认查询当前时间前一个月的
			$params = array();
			
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			
			$begindate=date("Y-m-d");
			$params[':startdate']=$begindate;
			$enddate=date('Y-m-d',strtotime('+1 month'));
			$params[':enddate']=$enddate;
			$display=' ORDER BY a.`adate` DESC LIMIT ' . ($pindex-1)*$psize . ',' . $psize;
			$tables="(" . tablename('carwash_staffsign')." AS a LEFT JOIN ".tablename('carwash_staff')." AS b ON a.`staffid`=b.id) LEFT JOIN ".tablename('carwash_chains')." AS c ON a.`chainid`=c.id ";
						
			$where="WHERE a.adate>=:startdate AND a.adate<=:enddate";
			$kwd=trim($_GPC['chainname']);
			if(!empty($kwd)) {
				$where .= " AND c.name like :chainname";
				$params[':chainname']="%{$kwd}%";
			}

			$kwd=trim($_GPC['staffname']);
			if(!empty($kwd)) {
				$where .= " AND b.name like :staffname";
				$params[':staffname']="%{$kwd}%";
			}
			
			$kwd=trim($_GPC['telephone']);
			if(!empty($kwd)) {
				$where .= " AND b.telephone like :telephone";
				$params[':telephone']="%{$kwd}%";
			}
			

			$kwd=trim($_GPC['searchdate']['start']);
			if(!empty($kwd)) {
				$begindate=$kwd;
				$params[':startdate']=$begindate;
			}
			$kwd=trim($_GPC['searchdate']['end']);
			if(!empty($kwd)) {
				$enddate=$kwd;
				$params[':enddate']=$enddate;
			}
			
			$sqlcount = 'SELECT COUNT(*) FROM ' . $tables . $where;
			$total = pdo_fetchcolumn($sqlcount, $params);

			if ($total > 0) {
				$sql='SELECT a.id AS id, a.`staffid` AS staffid, a.`chainid` AS chainid, a.`am` AS am, a.pm AS pm, a.`adate` AS adate, b.`name` AS staffname,b.`telephone` as telephone,c.`name` AS chainname FROM ' . $tables . $where . $display;
				
				$list = pdo_fetchall($sql, $params);

				$pager = pagination($total, $pindex, $psize);
			}
			

			
			
			if (!empty($_GPC['export'])) {
				$sql='SELECT a.id AS id, a.`staffid` AS staffid, a.`chainid` AS chainid, a.`am` AS am, a.pm AS pm, a.`adate` AS adate, b.`name` AS staffname,b.`telephone` as telephone,c.`name` AS chainname FROM ' . $tables . $where . $display;
				
				$list = pdo_fetchall($sql, $params);
				/* 输入到CSV文件 */
				$html = "\xEF\xBB\xBF";

				/* 输出表头 */
				$filter = array(
					'id' => '序号',
					'staffname' => '姓名',
					'staffid' => '工号',
					'telephone' => '电话',
					'adate' => '日期',
					'am' => '上午',
					'pm' => '下午',
					'chainname' => '店铺',
				);

				foreach ($filter as $key => $value) {
					$html .= $value . "\t,";
				}
				$html .= "\n";
				$sigstatus=array('1'=>'签到','0'=>'未签到');
				if (!empty($list)) {
					foreach ($list as $key => $value) {
						foreach ($filter as $index => $title) {
							if($index=='am'  or $index=='pm') {
								$html .= $sigstatus[$value[$index]] . "\t, ";
							} else {
								$html .= $value[$index] . "\t, ";
							}
						}
						$html .= "\n";
					}
				}

				/* 输出CSV文件 */
				$filename=$begindate."_".$enddate;
				header("Content-type:text/csv");
				header("Content-Disposition:attachment; filename=店铺数据{$filename}.csv");
				echo $html;
				exit();

			}
			
			include $this->template('staffsign');
		}
	
	}
	
	
	public function doWebChain() {
		global $_GPC, $_W;
		$tblname='carwash_chains';
		$op = $_GPC['op'];
		$weid = $_W['uniacid'];
		$hotel_level_config = $this->_hotel_level_config;
		load()->func('tpl');
		if ($op == 'edit') {
					
			$id = intval($_GPC['id']);
			if (checksubmit('submit')) {
				$insert = array(
					'weid' => $weid,
					'displaylevel' => $_GPC['displaylevel'],
					'name' => $_GPC['name'],
					'telephone' => $_GPC['telephone'],
					'picture'=>$_GPC['picture'],
					'address' => $_GPC['address'],
					'province' => $_GPC['district']['province'],
					'city' => $_GPC['district']['city'],
					'area' => $_GPC['district']['district'],
					'longitude' => $_GPC['baidumap']['lng'],
					'dimension' => $_GPC['baidumap']['lat'],
					'description' => $_GPC['description'],
				);

				if (empty($id)) {
					pdo_insert($tblname, $insert);
				} else {
					pdo_update($tblname, $insert, array('id' => $id));
				}
				message("店铺信息保存成功!", $this->createWebUrl('chain'), "success");
				exit();
			}
			
			$id = intval($_GPC['id']);

			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename($tblname) . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，店铺不存在或是已经删除！', '', 'error');
				}
			}

			include $this->template('chains_form');
		} else if ($op == 'delete') {

			$id = intval($_GPC['id']);
			pdo_delete("$tblname", array("id" => $id));

			message("店铺信息删除成功!", $this->createWebUrl('chain'), "success");
		} else if ($op == 'deleteall') {
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				pdo_delete("$tblname", array("id" => $id));
			}
			$this->web_message('酒店信息删除成功！', '', 0);
			exit();
		} else if ($op == 'showall') {
			if ($_GPC['show_name'] == 'showall') {
				$show_status = 1;
			} else {
				$show_status = 0;
			}

			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);

				if (!empty($id)) {
					pdo_update('hotel2', array('status' => $show_status), array('id' => $id));
				}
			}
			$this->web_message('操作成功！', '', 0);
			exit();
		} else if ($op == 'status') {
			$id = intval($_GPC['id']);
			if (empty($id)) {
				message('抱歉，传递的参数错误！', '', 'error');
			}
			$temp = pdo_update('hotel2', array('status' => $_GPC['status']), array('id' => $id));
			if ($temp == false) {
				message('抱歉，刚才操作数据失败！', '', 'error');
			} else {
				message('状态设置成功！', referer(), 'success');
			}
		} else if ($op == 'query') {
			$kwd = trim($_GPC['keyword']);
			$sql = 'SELECT id,title,description,thumb FROM ' . tablename('hotel2') . ' WHERE `weid`=:weid';
			$params = array();
			$params[':weid'] = $_W['uniacid'];
			if (!empty($kwd)) {
				$sql.=" AND `title` LIKE :title";
				$params[':title'] = "%{$kwd}%";
			}
			$ds = pdo_fetchall($sql, $params);
			foreach ($ds as &$value) {
				$value['thumb'] = tomedia($value['thumb']);
			}
			include $this->template('query');
		} else {
			$where = ' WHERE `weid` = :weid';
			$params = array(':weid' => $_W['uniacid']);
			
			$kwd = trim($_GPC['keywords']);
			if (!empty($kwd)) {
				$where.=" AND `name` LIKE :title";
				$params[':title'] = "%{$kwd}%";
			}
			$addkwd = trim($_GPC['addkeywords']);
			if (!empty($addkwd)) {
				$where.=" AND `address` LIKE :address";
				$params[':address'] = "%{$addkwd}%";
			}
			
			$sql = 'SELECT COUNT(*) FROM ' . tablename($tblname) . $where;
			$total = pdo_fetchcolumn($sql, $params);

			if ($total > 0) {
				$pindex = max(1, intval($_GPC['page']));
				$psize = 20;

				$sql = 'SELECT * FROM ' . tablename($tblname) . $where . ' ORDER BY `displaylevel` DESC LIMIT ' .
					($pindex - 1) * $psize . ',' . $psize;
				$list = pdo_fetchall($sql, $params);
				foreach ($list as &$row) {
					$row['level'] = $this->_hotel_level_config[$row['level']];
				}

				$pager = pagination($total, $pindex, $psize);
			}

			if (!empty($_GPC['export'])) {
				$sql = 'SELECT * FROM ' . tablename($tblname) . $where . ' ORDER BY `displaylevel`';
				$list = pdo_fetchall($sql, $params);
				/* 输入到CSV文件 */
				$html = "\xEF\xBB\xBF";

				/* 输出表头 */
				$filter = array(
					'id' => '序号',
					'name' => '店铺名称',
					'telephone' => '电话',
					'address' => '地址',
					'description' => '简介'
				);

				foreach ($filter as $key => $value) {
					$html .= $value . "\t,";
				}
				$html .= "\n";

				if (!empty($list)) {
					foreach ($list as $key => $value) {
						foreach ($filter as $index => $title) {
							$html .= $value[$index] . "\t, ";
						}
						$html .= "\n";
					}
				}

				/* 输出CSV文件 */
				header("Content-type:text/csv");
				header("Content-Disposition:attachment; filename=店铺数据.csv");
				echo $html;
				exit();

			}

			include $this->template('chains');
		}
	}
	
	public function doWebService() {
		global $_GPC, $_W;
		$tblname='carwash_services';
		$op = $_GPC['op'];
		$weid = $_W['uniacid'];
		load()->func('tpl');
		if ($op == 'edit') {
			$id = intval($_GPC['id']);
			if (checksubmit('submit')) {
				$insert = array(
					'weid' => $weid,
					'name' => $_GPC['name'],
					'price'=>$_GPC['price'],
					'count' => $_GPC['count'],
					'description' => $_GPC['description'],
					'delflag' => 0,
				);

				if (empty($id)) {
					pdo_insert($tblname, $insert);
				} else {
					pdo_update($tblname, $insert, array('id' => $id));
				}
				message("服务信息保存成功!", $this->createWebUrl('service'), "success");
				exit();
			}
			
			$id = intval($_GPC['id']);

			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename($tblname) . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，服务不存在或是已经删除！', '', 'error');
				}
			}
			include $this->template('service_form');
		} else if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$insert = array(
				'delflag'=>1,
				'deldate'=>date("Y-m-d H:m:s",time())
			);
			pdo_update($tblname, $insert, array('id' => $id));

			message("服务信息删除成功!", $this->createWebUrl('service'), "success");
		} else if ($op == 'deleteall') {
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				$insert = array(
					'delflag'=>1,
					'deldate'=>date("Y-m-d H:m:s",time())
				);
				pdo_update($tblname, $insert, array('id' => $id));
			}
			$this->web_message('服务信息删除成功！', '', 0);
			exit();
		} else if ($op == 'undel') {
			$id = intval($_GPC['id']);
			$insert = array(
				'delflag'=>0,
				'deldate'=>date("Y-m-d H:m:s",time())
			);
			pdo_update($tblname, $insert, array('id' => $id));

			message("服务信息恢复成功!", $this->createWebUrl('service'), "success");
		}else if ($op == 'showall') {
			if ($_GPC['show_name'] == 'showall') {
				$show_status = 1;
			} else {
				$show_status = 0;
			}

			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);

				if (!empty($id)) {
					pdo_update('hotel2', array('status' => $show_status), array('id' => $id));
				}
			}
			$this->web_message('操作成功！', '', 0);
			exit();
		} else if ($op == 'status') {
			$id = intval($_GPC['id']);
			if (empty($id)) {
				message('抱歉，传递的参数错误！', '', 'error');
			}
			$temp = pdo_update('hotel2', array('status' => $_GPC['status']), array('id' => $id));
			if ($temp == false) {
				message('抱歉，刚才操作数据失败！', '', 'error');
			} else {
				message('状态设置成功！', referer(), 'success');
			}
		} else if ($op == 'query') {
			$kwd = trim($_GPC['keyword']);
			$sql = 'SELECT id,title,description,thumb FROM ' . tablename('hotel2') . ' WHERE `weid`=:weid';
			$params = array();
			$params[':weid'] = $_W['uniacid'];
			if (!empty($kwd)) {
				$sql.=" AND `title` LIKE :title";
				$params[':title'] = "%{$kwd}%";
			}
			$ds = pdo_fetchall($sql, $params);
			foreach ($ds as &$value) {
				$value['thumb'] = tomedia($value['thumb']);
			}
			include $this->template('query');
		} else {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$where = ' WHERE `weid` = :weid';
			$params = array(':weid' => $_W['uniacid']);
			
			$kwd = trim($_GPC['servicename']);
			if (!empty($kwd)) {
				$where .= ' AND `name` like :servicename';
				$params[':servicename'] = "%{$kwd}%";
			}

			$sql = 'SELECT * FROM ' . tablename($tblname) . $where . ' ORDER BY id DESC LIMIT ' .
				($pindex - 1) * $psize . ',' . $psize;
			$list = pdo_fetchall($sql, $params);

			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($tblname) . $where, $params);
			$pager = pagination($total, $pindex, $psize);


			if (!empty($_GPC['export'])) {
				$sql = 'SELECT * FROM ' . tablename($tblname) . $where . ' ORDER BY id DESC';
				$list = pdo_fetchall($sql, $params);
			
				/* 输入到CSV文件 */
				$html = "\xEF\xBB\xBF";

				/* 输出表头 */
				$filter = array(
					'id' => '序号',
					'name' => '服务名称',
					'price' => '价格',
					'count' => '次数',
					'description' => '详情',
				);

				foreach ($filter as $key => $value) {
					$html .= $value . "\t,";
				}
				$html .= "\n";

				if (!empty($list)) {
					foreach ($list as $key => $value) {
						foreach ($filter as $index => $title) {
								$html .= $value[$index] . "\t, ";
						}
						$html .= "\n";
					}
				}

				/* 输出CSV文件 */
				header("Content-type:text/csv");
				header("Content-Disposition:attachment; filename=全部服务数据.csv");
				echo $html;
				exit();

			}

			include $this->template('service');
		}
	}
	
	
	public function doWebCash() {
		global $_GPC, $_W;
		$tblname='carwash_cash';
		$op = $_GPC['op'];
		$weid = $_W['uniacid'];
		load()->func('tpl');
		if ($op == 'edit') {
			$id = intval($_GPC['id']);
			if (checksubmit('submit')) {
				$insert = array(
					'weid' => $weid,
					'name' => $_GPC['name'],
					'money'=>$_GPC['money'],
					'cashtype' => $_GPC['cashtype'],
					'chainid'=>$_GPC['chainid'],
					'description' => $_GPC['description'],
					'cashdate' => $_GPC['cashtime'],
					'delflag'=>0,
				);

				if (empty($id)) {
					$insert['recorddate']=date("Y-m-d H:m:s",time());
					pdo_insert($tblname, $insert);
				} else {
					pdo_update($tblname, $insert, array('id' => $id));
				}
				message("记录保存成功!", $this->createWebUrl('cash'), "success");
				exit();
			}
			
			$washchar_chains=$this->getallchains();

			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename($tblname) . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，记录不存在或是已经删除！', '', 'error');
				}
			}
			include $this->template('cash_form');
		} else if ($op == 'delete') {
			$id = intval($_GPC['id']);
			$insert = array(
				'delflag'=>1,
				'deldate'=>date("Y-m-d H:m:s",time())
			);
			pdo_update($tblname, $insert, array('id' => $id));

			message("服务信息删除成功!", $this->createWebUrl('cash'), "success");
		} else if ($op == 'deleteall') {
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				$insert = array(
					'delflag'=>1,
					'deldate'=>date("Y-m-d H:m:s",time())
				);
				pdo_update($tblname, $insert, array('id' => $id));
			}
			$this->web_message('服务信息删除成功！', '', 0);
			exit();
		} else if ($op == 'undel') {
			$id = intval($_GPC['id']);
			$insert = array(
				'delflag'=>0,
				'deldate'=>date("Y-m-d H:m:s",time())
			);
			pdo_update($tblname, $insert, array('id' => $id));

			message("服务信息恢复成功!", $this->createWebUrl('cash'), "success");
		}else if ($op == 'showall') {
			if ($_GPC['show_name'] == 'showall') {
				$show_status = 1;
			} else {
				$show_status = 0;
			}

			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);

				if (!empty($id)) {
					pdo_update('hotel2', array('status' => $show_status), array('id' => $id));
				}
			}
			$this->web_message('操作成功！', '', 0);
			exit();
		} else if ($op == 'status') {
			$id = intval($_GPC['id']);
			if (empty($id)) {
				message('抱歉，传递的参数错误！', '', 'error');
			}
			$temp = pdo_update('hotel2', array('status' => $_GPC['status']), array('id' => $id));
			if ($temp == false) {
				message('抱歉，刚才操作数据失败！', '', 'error');
			} else {
				message('状态设置成功！', referer(), 'success');
			}
		} else {
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$where = ' WHERE delflag=0 AND `weid` = :weid AND (cashtype=1 OR cashtype=2)';
			$params = array(':weid' => $_W['uniacid']);
			$queryed=0;
			
			$kwd = trim($_GPC['querystatus']);
			if ($kwd=='inputtype') {
				$queryed=1;
				$where .= ' AND cashtype=2';
			}
			if ($kwd=='outputtype') {
				$queryed=1;
				$where .= ' AND cashtype=1';
			}
			
			$kwd = trim($_GPC['cashname']);
			if (!empty($kwd)) {
				$queryed=1;
				$where .= ' AND name like :cashname';
				$params[':cashname'] = "%{$kwd}%";
			}
			
			if($_GPC['usequerytime']==1) {
				$queryed=1;
				$params[':ltime']=$_GPC['selectdate']['start'].' 00:00:00';
				$params[':btime']=$_GPC['selectdate']['end'].' 23:59:59';
				$where .= " AND cashdate>=:ltime AND cashdate<=:btime";
			}
			
			$washchar_chains=$this->getallchains();

			$sql = 'SELECT * FROM ' . tablename($tblname) . $where . ' ORDER BY id DESC LIMIT ' .
				($pindex - 1) * $psize . ',' . $psize;
			$list = pdo_fetchall($sql, $params);

			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename($tblname) . $where, $params);
			
			$pager = pagination($total, $pindex, $psize);


			if (!empty($_GPC['export'])) {
				//输入了查询条件才导出所有的记录，否则只导出第一页
				if($queryed==1) {
					$sql = 'SELECT * FROM ' . tablename($tblname) . $where . ' ORDER BY id DESC';
					$list = pdo_fetchall($sql, $params);
				}
			
				/* 输入到CSV文件 */
				$html = "\xEF\xBB\xBF";

				/* 输出表头 */
				$filter = array(
					'id' => '序号',
					'name' => '费用名称',
					'cashtype' => '费用类型',
					'money' => '费用',
					'cashdate' => '费用日期',
					'chainid' => '店铺',
					'description' => '详情',
				);
				
				/*总记录数*/
				$itemcount = 0;
				/*支出条数*/
				$outcount = 0;
				/*收入总金额*/
				$inputmoney = 0.0;
				/*支出总金额*/
				$outputmoney = 0.0;
				/*收入类型1支出*/
				$moneytype = 0;
				
				foreach ($filter as $key => $value) {
					$html .= $value . "\t,";
				}
				$html .= "\n";

				if($_GPC['usequerytime']==1) {
					$filename=$params[':ltime']."_".$params[':btime'];
				}else {
					$filename="all";
				}
				
				if (!empty($list)) {
					$status = array('1'=>'支出', '2'=>'收入');
					foreach ($list as $key => $value) {
						
						$itemcount = $itemcount + 1;
						$moneytype = 0;
						
						foreach ($filter as $index => $title) {
							if ($index == 'cashtype') {
								if($value[$index] == '1') {
									$outcount = $outcount + 1;
									$moneytype = 1;
								}
								$html .= $status[$value[$index]] . "\t,";
							} else if ($index == 'chainid'){
								$html .= $washchar_chains[$value[$index]]['name'] . "\t,";
							} else if ($index == 'description') {
								$detialtext = str_replace("\n"," ",$value[$index]);
								$detialtext = str_replace("\r"," ",$detialtext);
								$detialtext = str_replace("\t","    ",$detialtext);
								$detialtext = str_replace(",","；",$detialtext);
								$html .= $detialtext . "\t,";
								
							} else {
								if($index == 'money') {
									if($moneytype == 1) {
										$outputmoney = $outputmoney+$value[$index];
									} else {
										$inputmoney = $inputmoney+$value[$index];
									}
								}
								$html .= $value[$index] . "\t,";
							}
						}
						$html .= "\n";
					}					
				}
				$gapstr="\t,\t,\t,\t,\t,";
				$html .= "\t,--\t,--\t,--\t,--\t,--\t,--\t,\n";
				$html .= "1\t,起始时间: {$params[':ltime']}\t,{$gapstr}\n";
				$html .= "2\t,结束时间: {$params[':btime']}\t,{$gapstr}\n";
				$html .= "3\t,总记录数: {$itemcount}条\t,{$gapstr}\n";
				$html .= "4\t,支出记录数: {$outcount}条\t,{$gapstr}\n";
				$inputcount=$itemcount-$outcount;
				$html .= "5\t,收入记录数: {$inputcount}条\t,{$gapstr}\n";
				$html .= "6\t,支出总金额: {$outputmoney}元\t,{$gapstr}\n";
				$html .= "7\t,收入总金额: {$inputmoney}元\t,{$gapstr}\n";
				$wetmoney = $inputmoney - $outputmoney;
				$html .= "8\t,净收入: {$wetmoney}元\t,{$gapstr}\n";
				
				/* 输出CSV文件 */
				header("Content-type:text/csv");
				header("Content-Disposition:attachment; filename=现金流数据{$filename}.csv");
				echo $html;
				exit();

			}

			include $this->template('cash');
		}
	}
	
	
	public function doWebStaff() {
		//这个操作被定义用来呈现 管理中心导航菜单
		//echo "</br>test</br>"
		global $_GPC, $_W;
		$stafftbl='carwash_staff';
		$op=$_GPC['op'];
		if ($op == 'edit') {
			
			$id = intval($_GPC['id']);

			if (!empty($id)) {
				$item = pdo_fetch("SELECT * FROM " . tablename($stafftbl) . " WHERE id = :id", array(':id' => $id));
				if (empty($item)) {
					message('抱歉，用户不存在或是已经删除！', '', 'error');
				}
			}

			if (checksubmit('submit')) {
				echo "submit";
				$data = array(
					'name' => $_GPC['realname'],
					'sex' => $_GPC['sex'],
					'age' => $_GPC['age'],
					'telephone' => $_GPC['mobile'],
					'identity' => $_GPC['identity'],
					'address' => $_GPC['address'],
					'director' => $_GPC['director'],
					'entrydate' => $_GPC['entrydate'],
					'leavedate' => $_GPC['leavedate'],
					'class' => $_GPC['class_radio'],
					'type'=>$_GPC['typ_radio'],
					'status'=>$_GPC['status'],
					'salary'=>$_GPC['salary'],
					'weid'=>$_W['uniacid'],
				);
				if ($data['status']==1) {
					$data['leavedate']=date("Y-m-d H:i:s");
				}
				if (empty($id)) {
					$data['inputdate']=date("Y-m-d H:i:s");
					pdo_insert('carwash_staff', $data);
				} else {
					pdo_update('carwash_staff', $data, array('id' => $id));
				}	
				message('员工信息更新成功！', $this->createWebUrl('staff'), 'success');	
			}
			include $this->template('staff_form');
		} else if ($op == 'delete') {
			$id = intval($_GPC['id']);
			pdo_delete($stafftbl, array('id' => $id));
			//message('删除员工成功！', referer(), 'success');
			message('删除员工成功！', $this->createWebUrl('staff'), 'success');
		}
		 else if ($op == 'deleteall') {
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				pdo_delete($stafftbl, array('id' => $id));
			}
			$this->web_message('删除所选员工成功！', '', 0);
			exit();
		}
		else
		{
			$sql = "";
			$params = array();
			if (!empty($_GPC['name'])) {
				$sql .= ' AND `name` LIKE :realname';
				$params[':realname'] = "%{$_GPC['name']}%";
			}
			if (!empty($_GPC['telephone'])) {
				$sql .= ' AND `telephone` LIKE :mobile';
				$params[':mobile'] = "%{$_GPC['telephone']}%";
			}
			if (!empty($_GPC['querystatus'])) {
				if($_GPC['querystatus']=='status0') {
					$sql .= ' AND `status`=0';
				} else {
					$sql .= ' AND `status`=1';
				}
			}
			
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$list = pdo_fetchall("SELECT * FROM " . tablename('carwash_staff') . " WHERE weid = '{$_W['uniacid']}'  $sql ORDER BY id DESC LIMIT " . ($pindex - 1) * $psize . ',' . $psize, $params);
			$total = pdo_fetchcolumn('SELECT COUNT(*) FROM ' . tablename('carwash_staff') . " WHERE weid = '{$_W['uniacid']}'  $sql", $params);
			$pager = pagination($total, $pindex, $psize);
			include $this->template('staff');
		}
		
	}
	
	public function getcaridbyno($carno,$weid) {
		echo "number=".$carno;
		$sql="SELECT id FROM ".tablename('carwash_cars')." WHERE number=:carno AND weid=:weid";
		$item = pdo_fetch($sql, array(':carno' => $carno, ':weid'=>$weid));
		if(empty($item['id'])) {
			return 0;
		} else {
			return $item['id'];
		}
	}
	
	public function doWebWashcar() {
		global $_GPC, $_W;
		$tblname='carwash_washrecord';
		$op=$_GPC['op'];
		$weid=$_W['uniacid'];
		
		if($op=='mod') {
			$washchar_chains=$this->getallchains();
			$service_items=$this->getallservices();
			$wash_date= date('y-m-d H:i:s',time());
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$sql = 'SELECT a.id as id, a.number as number,a.memberid as memberid,b.no as cardno,a.chainid as chainid,a.serviceid as serviceid,a.usemember as usemember,a.money as money,a.cardcount as cardcount,a.washdate as washdate,a.carid as carid FROM ' . tablename($tblname) . ' as a left join '.tablename('carwash_cards'). ' as b ON a.memberid=b.id where a.id = :id ';
				$item = pdo_fetch($sql, array(':id' => $id));
				$wash_date=$item['washdate'];
				if (empty($item)) {
					message('抱歉，记录不存在或是已经删除！', '', 'error');
				}
			}
			if (checksubmit('submit')) {

				$insertdata = array(
						'washdate'=> $_GPC['servicedate'],
						'chainid' => $_GPC['servicechain'],
						'recorddate' => date('y-m-d H:i:s',time()),
						);
				
				//更新数据库
				pdo_update($tblname, $insertdata, array('id' => $id));
				message("消费信息修改成功!", $this->createWebUrl('washcar'), "success");
				exit();
			}
			include $this->template('washrecord_form');
		} else if($op=='edit') {
			$washchar_chains=$this->getallchains();
			$service_items=$this->getallservices();
			$wash_date= date('y-m-d H:i:s',time());
			$id = intval($_GPC['id']);
			if (!empty($id)) {
				$sql = 'SELECT a.id as id, a.number as number,a.memberid as memberid,b.no as cardno,a.chainid as chainid,a.serviceid as serviceid,a.usemember as usemember,a.money as money,a.cardcount as cardcount,a.washdate as washdate,a.carid as carid FROM ' . tablename($tblname) . ' as a left join '.tablename('carwash_cards'). ' as b ON a.memberid=b.id where a.id = :id ';
				$item = pdo_fetch($sql, array(':id' => $id));
				$wash_date=$item['washdate'];
				if (empty($item)) {
					message('抱歉，记录不存在或是已经删除！', '', 'error');
				}
			}
			if (checksubmit('submit')) {

				$insertdata = array(
						'weid' => $weid,
						'number' => $_GPC['carnumber'],
						'money' => $_GPC['serviceprice'],
						'washdate'=> $_GPC['servicedate'],
						'opeatorid' => "",
						'chainid' => $_GPC['servicechain'],
						'recorddate' => date('y-m-d H:i:s',time()),
						'memberid'=>$_GPC['memberid'],
						'serviceid'=>$_GPC['serviceitem'],
						'cardcount'=>$_GPC['servicecount'],
						'usemember'=>$_GPC['usemember'],
						'carid'=>$_GPC['carid'],
				);
				if($_GPC['usemember']==1) {
					$insertdata['money']=0;
				} else {
					$insertdata['cardcount']=0;
				}
				if(empty($_GPC['id'])) {
					//更新会员卡信息
					$cardres=array('type'=>2,'count'=>0);
					if($_GPC['usemember']==1) {
						$cardres=$this->updatecard($_GPC['memberno'],$_GPC['servicecount'],$_GPC['servicedate']);
					}
					//需要先查询汽车记录是否存在，如果不存在才添加，因为非会员卡洗车时汽车记录有可能已经存在了
					//添加汽车记录
					if(empty($_GPC['carid'])) {
						$dbcarid=$this->getcaridbyno($_GPC['carnumber'],$weid);
						if(empty($dbcarid)) {
							$carinfo=array(
								'number'=>$_GPC['carnumber'],
								'weid'=>$weid,
								'rdate'=>date('y-m-d H:i:s',time()),
								);
							pdo_insert('carwash_cars', $carinfo);
							$insertdata['carid']=$this->getcaridbyno($_GPC['carnumber'],$weid);
						} else {
							$insertdata['carid'] = $dbcarid;
						}
					}
				}
				
				//更新数据库
				if(empty($_GPC['id'])) {
					pdo_insert($tblname, $insertdata);
				} else {
					pdo_update($tblname, $insertdata, array('id' => $id));
					message("消费信息修改成功!", $this->createWebUrl('washcar'), "success");
					exit();
				}
				if($cardres['type']==1) {
					message("消费信息保存成功，会员卡剩余[{$cardres['count']}]次!", $this->createWebUrl('washcar'), "success");
				} else {
					message("消费信息保存成功!", $this->createWebUrl('washcar'), "success");
				}
				exit();
			}
			include $this->template('washrecord_form');
		}else if($op=='querycard'){
			$sql="SELECT a.no AS no,a.`levavetime` AS levavetime, a.id AS id,b.`id` AS carid FROM ".tablename(carwash_cards)." AS a,".tablename(carwash_cars)." AS b WHERE a.state=1 AND a.number = b.number AND ((a.type=1 AND a.`levavetime`>0) OR a.type=2)  AND a.weid=:weid AND b.number=:carnumber";
			$item = pdo_fetch($sql, array(':carnumber' => $_GPC['carnumber'], ':weid'=>$weid));
			if(empty($item)) {
				//有可能还没有关联汽车记录
				$sql="SELECT * FROM ".tablename('carwash_cards')." WHERE number=:carnumber AND state=1 AND ((type=1 AND levavetime>0) OR type=2)  AND weid=:weid";
				$item = pdo_fetch($sql, array(':carnumber' => $_GPC['carnumber'], ':weid'=>$weid));
			}
			//如果是副卡，就需要重新查询一下主卡的ID，因为副卡在被编辑的时候会删除
			if($item['maincard']==0) {
				$mainitem=pdo_fetch("SELECT id from ".tablename('carwash_cards')." WHERE no=:no", array(':no'=>$item['no']));
				$item['id']=$mainitem['id'];
			}
			$result=array();
			$result['success']=0;
			if(!empty($item)){
				$result['leavetime']=$item['levavetime'];
				$result['id']=$item['id'];
				$result['memberid']=$item['no'];
				$result['carid']=$item['carid'];
				$result['success']=1;
			}
			if($result['id']==0) {
				$result['success']=0;
			}
			echo json_encode($result);
			exit();
		}else if ($op == 'delete') {
			$id = intval($_GPC['id']);
			pdo_delete($tblname, array('id' => $id));
			message('删除消费记录成功！', $this->createWebUrl('washcar'), 'success');
		}
		 else if ($op == 'deleteall') {
			foreach ($_GPC['idArr'] as $k => $id) {
				$id = intval($id);
				pdo_delete($tblname, array('id' => $id));
			}
			$this->web_message('规则操作成功！', '', 0);
			exit();
		}else{
			$washchar_chains=$this->getallchains();
			
			$pindex = max(1, intval($_GPC['page']));
			$psize = 20;
			$where = ' WHERE a.`weid` = :weid';
			$params = array(':weid' => $_W['uniacid']);
			$queryed=0;
			
			$kwd=trim($_GPC['carnumber']);
			if(!empty($kwd)) {
				$params[':carnumber']="%{$kwd}%";
				$where .= ' AND a.number like :carnumber';
				$queryed=1;
			}
			
			if($_GPC['usequerytime']==1) {
				$params[':ltime']=$_GPC['selectdate']['start'].' 00:00:00';
				$params[':btime']=$_GPC['selectdate']['end'].' 23:59:59';
				$where .= " AND a.washdate>=:ltime AND a.washdate<=:btime";
				$queryed=1;
			}
			
			$tables='('.tablename($tblname).' AS a LEFT JOIN  '.tablename('carwash_cards').' AS b ON a.`memberid`=b.`id`) LEFT JOIN '.tablename('carwash_services').'AS c ON a.serviceid=c.id';
			
			$sql = 'SELECT COUNT(*) FROM ' . $tables . $where;
			$total = pdo_fetchcolumn($sql, $params);
			
			$sql = 'SELECT a.id AS id, b.no AS cardno, a.number as number, a.serviceid as serviceid, a.washdate as washdate, a.usemember as usemember, a.money as money, a.cardcount as cardcount, a.chainid as chainid,c.name as servicename, b.chainid as cardchain, b.type as cardtype  FROM '.$tables. $where.' ORDER BY a.`washdate` DESC LIMIT ' .
			($pindex - 1) * $psize . ',' . $psize;
			
			$washrecord_list = pdo_fetchall($sql, $params);
			
			$pager = pagination($total, $pindex, $psize);
			
			if (!empty($_GPC['export'])) {
				//输入了查询条件才导出当前查询的所有页面，否则只导出第一页
				if($queryed==1) {
					$sql = 'SELECT a.id AS id, b.no AS cardno, a.number as number, a.serviceid as serviceid, a.washdate as washdate, a.usemember as usemember, a.money as money, a.cardcount as cardcount, a.chainid as chainid,c.name as servicename, b.chainid as cardchain, b.type as cardtype FROM '.$tables. $where.' ORDER BY a.`washdate` DESC';
			
					$washrecord_list = pdo_fetchall($sql, $params);
				}
				
				/*各个分店的年卡消费次数*/
				$yearcar = array();
				foreach($washchar_chains as $id => $value) {
					$yearcar[$id] = array();
					foreach($washchar_chains as $id1 => $value1){
						$yearcar[$id][$id1]=0;
					}
				}
				/*各个分店的次卡的消费情况*/
				$countcard = array();
				foreach($washchar_chains as $id => $value) {
					$countcard[$id] = array();
					foreach($washchar_chains as $id1 => $value1){
						$countcard[$id][$id1]=0;
					}
				}
				/*各个分店的现金收入情况*/
				$inputmoney = array();
				foreach($washchar_chains as $id => $value) {
					$inputmoney[$id] = 0.0;
				}
				
				/* 输入到CSV文件 */
				$html = "\xEF\xBB\xBF";

				/* 输出表头 */
				$filter = array(
					'id' => '序号',
					'number' => '车牌号',
					'cardno' => '会员卡号',
					'servicename' => '服务类型',
					'washdate' => '消费时间',
					'chainid' => '消费店铺',
					'usemember' => '支付方式',
					'money' => '价格',
					'cardchain' => '会员店铺',
					'cardtype' => '卡类型',
					'cardcount' => '次数',
				);

				foreach ($filter as $key => $value) {
					$html .= $value . "\t,";
				}
				$html .= "\n";
				$paytype=array('1'=>'会员','0'=>'现金');
				$cardname=array(1=>'次卡',2=>'年卡');
				if (!empty($washrecord_list)) {
					foreach ($washrecord_list as $key => $value) {
						foreach ($filter as $index => $title) {
							if($index=='usemember') {
								$html .= $paytype[$value[$index]] . "\t, ";
							} else if($index=='chainid' or $index=='cardchain'){
								$html .= $washchar_chains[$value[$index]]['name'] . "\t, ";
							} else if($index=='cardtype' ){
								$html .= $cardname[$value[$index]] . "\t, ";
							} else {								
								$html .= $value[$index] . "\t, ";
							}
						}
						$html .= "\n";
						
						/*统计会员卡消费次数*/
						if($value['usemember']=='1') {
							if($value['cardtype']==2) {
								$yearcar[$value['chainid']][$value['cardchain']] += 1;
							} else {
								$countcard[$value['chainid']][$value['cardchain']] += $value['cardcount'];
								//if($value['chainid']!=1 && $value['chainid']!=2 && $value['chainid']!=3) {
									//echo $value['id']."---".$value['cardchain']."\r\n";
								//}
							}
						} else {
							$inputmoney[$id] += $value['money'];
						}
					}
					//print_r($countcard);
						//exit();
				}
				/* 输出CSV文件 */
				if($_GPC['usequerytime']==1) {
					$filename=$params[':ltime']."_".$params[':btime'];
				}else {
					$filename="all";
				}
				header("Content-type:text/csv");
				header("Content-Disposition:attachment; filename=洗车记录{$filename}.csv");
				
				/*输出统计信息*/
				$reportindex = 0;
				$gapstr = "\t,\t,\t,\t,\t,\t,\t,\t,";
				$html .= "\t, --\t, --\t, --\t, --\t, --\t, --\t, --\t, --\t, --\t, \n";
				$html .= "{$reportindex}\t, 起始时间:{$params[':ltime']}\t,{$gapstr}\n";
				$reportindex++;
				$html .= "{$reportindex}\t, 结束时间:{$params[':btime']}\t,{$gapstr}\n";
				
				foreach ($yearcar as $key=>$value) {
					$reportindex ++;
					$html .= "{$reportindex}\t, ";
					$html .= "{$washchar_chains[$key]['name']}的刷年卡情况:";
					foreach($value as $id=>$count) {
						$html .= "{$washchar_chains[$id]['name']}店{$count}次;";
					}
					$html .= "{$gapstr}\n";
				}
				
				foreach ($countcard as $key=>$value) {
					$reportindex ++;
					$html .= "{$reportindex}\t, ";
					$html .= "{$washchar_chains[$key]['name']}的刷次卡情况:";
					foreach($value as $id=>$count) {
						$html .= "{$washchar_chains[$id]['name']}店{$count}次;";
					}
					$html .= "{$gapstr}\n";
				}
				
				foreach ($inputmoney as $key=>$value) {
					$reportindex ++;
					$html .= "{$reportindex}\t, ";
					$html .= "{$washchar_chains[$key]['name']}的现金输入情况:{$value}元;";
					$html .= "{$gapstr}\n";
				}
				
				
				echo $html;
				exit();

			}
			
			include $this->template('washrecord');
		}
	}
	
	public function updatecard($cardid, $count,$servicedate) {
		$tblname='carwash_cards';
		$sql = 'SELECT id,no,times,levavetime,type,endtime FROM '.tablename($tblname) .' WHERE `no`=:no AND state=1' ;
		$params = array(':no'=>$cardid);
		$item = pdo_fetch($sql,$params);
		if(empty($item)) {
			message('抱歉，没有有效的会员卡！', '', 'error');
			exit();
		}
		
		$curtime=date("y-m-d h:i:s");
		$outdate=0;
		
		if(strtotime($servicedate)>strtotime($item['endtime'])) {
			if(strtotime($curtime)>strtotime($item['endtime'])) {
				$outdate=1;
				$insert['state']=2;
				pdo_update($tblname, $insert, array('no' => $cardid));
				message('抱歉，会员卡在['.$item['endtime'].']已经过期！', '', 'error');
				exit();
			} else {
				message('抱歉，服务时间超过了会员卡的过期时间['.$item['endtime'].']！', '', 'error');
				exit();
			}
		}
		
		if ($item['type']==1 && $item['levavetime']<$count) {
			$insert['levavetime']=0;
			pdo_update($tblname, $insert, array('no' => $cardid));
			message('抱歉，会员卡中次数不足，还剩['.$item['levavetime'].']次！', '', 'error');
			exit();
		} 
		
		if ($item['type']==1) {
			$insert['levavetime']=$item['levavetime']-$count;
			pdo_update($tblname, $insert, array('no' => $cardid));
			return array('type'=>1,'count'=>$insert['levavetime']);
		}
		return array('type'=>2,'count'=>0);
	}
	
	public function web_message($error, $url = '', $errno = -1) {
		$data = array();
		$data['errno'] = $errno;
		if (!empty($url)) {
			$data['url'] = $url;
		}
		$data['error'] = $error;
		echo json_encode($data);
		exit;
	}
	
	public function tpl_form_field_daterange($name, $value = array(), $time = false) {
	$s = '';

	if (empty($time) && !defined('TPL_INIT_DATERANGE_DATE')) {
		$s = '
<script type="text/javascript">
	require(["daterangepicker"], function(){
		$(function(){
			$(".daterange.daterange-date").each(function(){
				var elm = this;
				$(this).daterangepicker({
					startDate: $(elm).prev().prev().val(),
					endDate: $(elm).prev().val(),
					format: "YYYY-MM-DD"
				}, function(start, end){
					$(elm).find(".date-title").html(start.toDateStr() + " 至 " + end.toDateStr());
					$(elm).prev().prev().val(start.toDateStr());
					$(elm).prev().val(end.toDateStr());
					$(elm).prev().change();
				});
			});
		});
	});
</script>
';
		define('TPL_INIT_DATERANGE_DATE', true);
	}

	if (!empty($time) && !defined('TPL_INIT_DATERANGE_TIME')) {
		$s = '
<script type="text/javascript">
	require(["daterangepicker"], function(){
		$(function(){
			$(".daterange.daterange-time").each(function(){
				var elm = this;
				$(this).daterangepicker({
					startDate: $(elm).prev().prev().val(),
					endDate: $(elm).prev().val(),
					format: "YYYY-MM-DD HH:mm",
					timePicker: true,
					timePicker12Hour : false,
					timePickerIncrement: 1,
					minuteStep: 1
				}, function(start, end){
					$(elm).find(".date-title").html(start.toDateTimeStr() + " 至 " + end.toDateTimeStr());
					$(elm).prev().prev().val(start.toDateTimeStr());
					$(elm).prev().val(end.toDateTimeStr());
				});
			});
		});
	});
</script>
';
		define('TPL_INIT_DATERANGE_TIME', true);
	}
	if ($value['starttime'] !== false && $value['start'] !== false) {
		if($value['start']) {
			$value['starttime'] = empty($time) ? date('Y-m-d',strtotime($value['start'])) : date('Y-m-d H:i',strtotime($value['start']));
		}
		$value['starttime'] = empty($value['starttime']) ? (empty($time) ? date('Y-m-d') : date('Y-m-d H:i') ): $value['starttime'];
	} else {
		$value['starttime'] = '请选择';
	}
	
	if ($value['endtime'] !== false && $value['end'] !== false) {
		if($value['end']) {
			$value['endtime'] = empty($time) ? date('Y-m-d',strtotime($value['end'])) : date('Y-m-d H:i',strtotime($value['end']));
		}
		$value['endtime'] = empty($value['endtime']) ? $value['starttime'] : $value['endtime'];
	} else {
		$value['endtime'] = '请选择';
	}
	$s .= '
	<input name="'.$name . '[start]'.'" type="hidden" value="'. $value['starttime'].'" />
	<input name="'.$name . '[end]'.'" type="hidden" value="'. $value['endtime'].'" />
	<button class="btn btn-default daterange '.(!empty($time) ? 'daterange-time' : 'daterange-date').'" type="button"><span class="date-title">'.$value['starttime'].' 至 '.$value['endtime'].'</span> <i class="fa fa-calendar"></i></button>
	';
	return $s;
	}

	public function getallchains() {
		$sql = 'SELECT id,name FROM '.tablename('carwash_chains');
		$chainslist = pdo_fetchall($sql);
		if(empty($chainslist)) {
			message('抱歉，请先添加店铺信息！', '', 'error');
			exit();
		}
		$_washchar_chains=array();
		foreach ($chainslist as $key=>$value) {
			$_washchar_chains[$value['id']]=array('id'=>$value['id'],'name'=>$value['name']);
		}
		return $_washchar_chains;
	}
	
	public function getallservices() {
		$sql = 'SELECT id,name,price,count FROM ' . tablename('carwash_services') . ' WHERE delflag=0 ';
		$list = pdo_fetchall($sql);
		if(empty($list)) {
			message('抱歉，请先添加服务信息！', '', 'error');
			exit();
		}
		$_washchar_services=array();
		foreach ($list as $key=>$value) {
			$_washchar_services[$value['id']]=array('id'=>$value['id'],'name'=>$value['name'],'price'=>$value['price'],'count'=>$value['count']);
		}
		return $_washchar_services;
	}
	
}