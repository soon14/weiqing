<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<ul class="nav nav-tabs">
	<li class="active"><a href="<?php  echo $this->createWebUrl('orders', array('op'=>'display'));?>">订单列表</a></li>
</ul>

<div class="main">
	<div class="panel panel-info">
	<div class="panel-heading">筛选</div>
	<div class="panel-body">
		<form action="./index.php" method="get" class="form-horizontal" role="form">
			<input type="hidden" name="c" value="<?php  echo $_GPC['c'];?>">
			<input type="hidden" name="a" value="<?php  echo $_GPC['a'];?>">
			<input type="hidden" name="do" value="<?php  echo $_GPC['do'];?>">
			<input type="hidden" name="m" value="<?php  echo $_GPC['m'];?>">
			<input type="hidden" name="op" value="<?php  echo $op;?>">
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">关键字</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<input class="form-control" name="sn" id="" type="text" value="<?php  echo $_GPC['sn'];?>" placeholder="订单号">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">订单状态</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<select name="status" class="form-control">
							<option value="0" <?php  if(intval($_GPC['status']) == 0) { ?>selected="selected"<?php  } ?>>全部</option>
							<option value="1" <?php  if(intval($_GPC['status']) == 1) { ?>selected="selected"<?php  } ?>>未付款</option>
							<option value="2" <?php  if(intval($_GPC['status']) == 2) { ?>selected="selected"<?php  } ?>>已付款</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">日期</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<?php  echo tpl_form_field_daterange('createtime', $createtime);?>
					</div>
					<div class="col-xs-12 col-sm-2 col-md-1 col-lg-1">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	
	<div class="alert alert-success" role="alert">点击订单查看清单。</div>
	
	<div class="panel panel-default">
		<div class="panel-heading">订单列表</div>
		<div class="table-responsive panel-body">
			<table class="table table-hover">
				<thead class="navbar-inner">
					<tr>
						<th class="col-sm-2">订单单号</th>
						<th class="col-sm-2">客户信息</th>
						<th class="col-sm-1">下单日期</th>
						<th class="col-sm-1">付款日期</th>
						<th class="col-sm-1">金额</th>
						<th class="col-sm-1">状态</th>
						<th class="col-sm-4"></th>
					</tr>
				</thead>
				<?php  if(!empty($orders)) { ?>
				<?php  if(is_array($orders)) { foreach($orders as $key => $order) { ?>
				<tbody class="tr_order">
					<tr>
						<td><?php  echo $order['sn'];?></td>
						<td><?php  echo $order['uid'];?></td>
						<td><?php  echo date('Y-m-d', $order['createtime']);?></td>
						<td>
							<?php  if(!empty($order['paytime'])) { ?>
							<?php  echo date('Y-m-d', $order['paytime']);?>
							<?php  } ?>
						</td>
						<td><?php  echo $order['amount'];?></td>
						<td><?php  echo $this->getOrdersStatus($order['status']);?></td>
						<td></td>
					</tr>
				</tbody>
				<tbody class="tr_item" style="display: none;">
					<tr>
						<th></th>
						<th>品名</th>
						<th>价格</th>
						<th>数量</th>
						<th>小计</th>
					</tr>
					<?php  if(is_array($order['items'])) { foreach($order['items'] as $key => $item) { ?>
					<tr>
						<td class="text-right"><image src="<?php  echo tomedia($item['image']);?>" style="max-width: 48px; max-height: 48px; border: 1px dotted gray"></td>
						<td>
							<?php  echo $item['name'];?><br/>
							<?php  echo $item['sn'];?>
						</td>
						<td><?php  echo $item['price'];?></td>
						<td><?php  echo $item['quantity'];?></td>
						<td>
							<?php  echo $item['price']*$item['quantity'];?>
						</td>
					</tr>
					<?php  } } ?>
				</tbody>
				<?php  } } ?>
				<?php  } ?>
				<?php  if(empty($orders)) { ?>
				<tr>
					<td colspan="7">
						没有订单
					</td>
				</tr>
				<?php  } ?>
			</table>
			<?php  echo $pager;?>
			
		</div>
	</div>
</div>

<script>
require(['jquery', 'util'], function($, util){
	$(function(){
		$('.tr_order').click(function(){
			var items = $(this).next();
			if(items.css('display') == 'none'){
				items.show();
			} else {
				items.hide();
			}
		});
	});
});
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>