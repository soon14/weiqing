<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<nav class="navbar navbar-default " role="navigation">
	<div class="container" style="width: 100%; ">
		<h3 style="position: relative; text-align: center;">
			<span><?php  echo $title;?></span>
		</h3>
	</div>
</nav>

<ul class="nav nav-tabs" style="margin-bottom: 10px;">
	<li>&nbsp;&nbsp;&nbsp;&nbsp;</li>
	<li><a href="<?php  echo $this->createMobileUrl('store', array('op'=>'display'));?>">全部商品</a></li>
	<li><a href="<?php  echo $this->createMobileUrl('cart', array('op'=>'display'));?>">购物车</a></li>
	<li class="active"><a href="<?php  echo $this->createMobileUrl('orders', array('op'=>'display'), true );?>">我的订单</a></li>
</ul>

<div class="main">
	<div class="row">
		<div class="col-xs-12 " style="padding: 10px 25px;">
		<div class="btn-group btn-group-justified">
			<div class="btn-group">
				<a href="<?php  echo $this->createMobileUrl('orders', array('op'=>'display','status'=>1), true );?>" class="btn btn-default <?php  if($status == 1) { ?>active<?php  } ?>">未付款 <?php  if($status == 1) { ?><span class="badge"><?php  echo $total;?></span><?php  } ?></a>
			</div>
			<div class="btn-group">
				<a href="<?php  echo $this->createMobileUrl('orders', array('op'=>'display','status'=>2), true );?>" class="btn btn-default <?php  if($status == 2) { ?>active<?php  } ?>">已付款 <?php  if($status == 2) { ?><span class="badge"><?php  echo $total;?></span><?php  } ?></a>
			</div>
			<div class="btn-group">
				<a href="<?php  echo $this->createMobileUrl('orders', array('op'=>'display','status'=>0), true );?>" class="btn btn-default <?php  if(empty($status)) { ?>active<?php  } ?>">全部 <?php  if(empty($status)) { ?><span class="badge"><?php  echo $total;?></span><?php  } ?></a>
			</div>
		</div>
		</div>
	</div>

	<?php  if(is_array($orders)) { foreach($orders as $order) { ?>
	<div class="panel panel-default" style="margin: 10px;">
		<div class="panel-heading"> 
			订单编号: <?php  echo $order['sn'];?>
			<?php  if($order['status'] == 1) { ?>
			<label class="label label-danger">未付款</label>
			<?php  } else { ?>
			<label class="label label-success">已付款</label>
			<?php  } ?>
		</div>
		<div class="panel-body clearfix" >
			<?php  if(is_array($order['items'])) { foreach($order['items'] as $item) { ?>
			<style>
				@media screen and(min-width:767px){.media{margin-top:0px;}}
			</style>
			<div class="media col-xs-12 col-sm-6 col-md-4">
				<a class="media-left media-middle pull-left" href="">
					<img src="<?php  echo tomedia($item['image']);?>" style="max-width: 90px; max-height: 90px; border: 1px dotted gray">
				</a>
				<div class="media-body">
					<h4 class="media-heading"><?php  echo $item['name'];?></h4>
					<p>
						<b>价格:</b> <?php  echo $item['price'];?> 元<br/>
						<b>数量:</b> <?php  echo $item['quantity'];?> 件<br/>
						<b>小计:</b> <?php  echo $item['price'] * $item['quantity']?> 元
					</p>
				</div>
			</div>
			<?php  } } ?>
		</div>
		<div class="panel-footer text-center">
		<?php  if($order['status'] == 1) { ?>
			<a href="<?php  echo $this->createMobileUrl('pay', array('op'=>'order', 'id'=> $order['id']));?>" class="btn btn-primary btn-block">付款 (总计: <?php  echo $order['amount'];?>元.)</a>
		<?php  } else { ?>
			已付款 <?php  echo $order['amount'];?> 元.
		<?php  } ?>
		</div>
	</div>
	<?php  } } ?>
	<div style="padding: 0 10px;">
		<style>
			.pagination {margin-top: 0px;}
		</style>
		<?php  echo $pager;?>
	</div>
</div>

<script>
require(['jquery', 'util'], function($, util){
	$(function(){
	});
});
</script>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>