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
	<li class="active"><a href="<?php  echo $this->createMobileUrl('cart', array('op'=>'display'));?>">购物车</a></li>
	<?php  if(!empty($_W['member']['uid'])) { ?>
	<li><a href="<?php  echo $this->createMobileUrl('orders', array('op'=>'display'), true);?>">我的订单</a></li>
	<?php  } ?>
</ul>
<style>
	.main .table{table-layout:fixed; min-width:300px;}
	.main .table tr>th,.main .table tr>td{vertical-align:middle; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;}
</style>
<div class="main">
	<form action="<?php  echo $this->createMobileUrl('cart', array('op'=>'settle'));?>" method="post" class="form-horizontal" role="form">
		<div class="panel panel-default">
			<div class="panel-heading">购物车列表</div>
			<div class="table-responsive panel-body " style="width: 100%;">
				<table class="table table-hover">
					<thead class="navbar-inner">
						<tr>
							<th style="width:50px;"><a href="javascript:">全选</a></th>
							<th style="width:80px;">商品</th>
							<th style="width:60px;">外观</th>
							<th style="width:60px;">售价</th>
							<th style="width:60px;">数量</th>
							<th style="width:60px;">金额</th>
							<th style="width:80px;" class="text-right">操作</th>
						</tr>
					</thead>
					<tbody>
						<?php  if(is_array($carts)) { foreach($carts as $goodsid => $cart) { ?>
						<?php  $goods = $goodses[$goodsid];?>
						<tr>
							<td>
								<?php  if($goods['status'] == 2) { ?>
								<input type="checkbox" name="cartids[]" value="<?php  echo $cart['id'];?>">
								<?php  } else { ?>
								下架
								<?php  } ?>
							</td>
							<td><?php  echo $goods['name'];?> <br> 编码:<?php  echo $goods['sn'];?></td>
							<td><img src="<?php  echo tomedia($goods['image']);?>" style="max-width: 48px; max-height: 48px; border: 1px dotted gray; text-align:center;"></td>
							<td class="goods_price"><?php  echo $goods['price'];?></td>
							<td class="cart_quantity"><?php  echo $cart['quantity'];?></td>
							<td class="cart_amount"><?php  echo $goods['price'] * $cart['quantity']?></td>
							<td class="text-right" >
								<a href="<?php  echo $this->createMobileUrl('store', array('op'=>'display', 'goodsid'=>$goodsid));?>" class="btn btn-default btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑"><i class="fa fa-pencil"></i></a>
								<a onclick="if(!confirm('删除后将不可恢复,确定删除吗?')) return false;" href="<?php  echo $this->createMobileUrl('cart', array('op'=>'delete', 'id'=>$cart['id']));?>" class="btn btn-default btn-sm btn-danger" data-toggle="tooltip" data-placement="top" title="" data-original-title="删除"><i class="fa fa-times"></i> </a>
							</td>
						</tr>
						<?php  } } ?>
						<?php  if(empty($carts)) { ?>
						<tr>
							<td colspan="7">
								购物车中还没有商品.
							</td>
						</tr>
						<?php  } else { ?>
						<tr>
							<td>
								<input type="checkbox" name="checkall">
							</td>
							<td></td>
							<td></td>
							<td></td>
							<td>合计:</td>
							<td><label class="sum form-control-static">0.00</label></td>
							<td class="text-right">
								<input type="submit" value="结算" name="submit" class="btn btn-primary" disabled="disabled">
								<input type="hidden" value="<?php  echo $_W['token'];?>" name="token">
							</td>
						</tr>
						<?php  } ?>
					</tbody>
				</table>
				<?php  echo $pager;?>
			</div>
		</div>
	</form>
</div>

<script>
require(['jquery', 'util'], function($, util){
	$(function(){
		$('input[name="checkall"]').click(function(){
			var checked = this.checked;
			$('input[name="cartids[]"]').each(function(){
				this.checked = checked;
			});
			
			calc();
			
			$('input[name="submit"]').get(0).disabled = $('input[name="cartids[]"]:checked').length == 0
		});
		
		// 计算结算金额
		function calc(){
			var sum = 0;
			$('input[name="cartids[]"]').each(function(){
				if(this.checked){
					var txt =$(this).parent().next().next().next().next().next().text();
					var s = parseFloat(txt);
					if(!isNaN(s)){
						sum += s;
					}
				}
			});
			$('label.sum').text(sum);
		}
		
		$('input[name="submit"]').click(function(){
			<?php  if(empty($_W['member']['uid'])) { ?>
			util.message('请先登陆系统.','<?php  echo url('auth/login', array('forward' => base64_encode($_SERVER['QUERY_STRING'])), true)?>','info');
			return false;
			<?php  } ?>
			var sum = $('label.sum').text();
			sum = parseFloat(sum);
			if(isNaN(sum)||sum == 0){
				util.message('请选择要结算的商品');
				return false;
			}
		});
		
		$('input[name="cartids[]"]').change(function(){
			calc();
			
			$('input[name="submit"]').get(0).disabled = $('input[name="cartids[]"]:checked').length == 0
		});
	});
});
</script>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>