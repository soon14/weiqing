<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>
<script src="../addons/jiang_shop/template/js/bootstrap.min.js"></script>
<nav class="navbar navbar-default " role="navigation">
	<div class="container" style="width: 100%; ">
		<h3 style="position: relative; text-align: center;">
			<span><?php  echo $title;?></span>
		</h3>
	</div>
</nav>

<ul class="nav nav-tabs" style="margin-bottom: 10px;">
	<li>&nbsp;&nbsp;&nbsp;&nbsp;</li>
	<li class="active"><a href="<?php  echo $this->createMobileUrl('store', array('op'=>'display'));?>">全部商品</a></li>
	<li><a href="<?php  echo $this->createMobileUrl('cart', array('op'=>'display'));?>">购物车</a></li>
	<?php  if(!empty($_W['member']['uid'])) { ?>
	<li><a href="<?php  echo $this->createMobileUrl('orders', array('op'=>'display'), true);?>">我的订单</a></li>
	<?php  } ?>
</ul>

<div class="main">
	<form action="./index.php" method="get" class="form-horizontal" role="form">
		<input type="hidden" name="c" value="<?php  echo $_GPC['c'];?>">
		<input type="hidden" name="a" value="<?php  echo $_GPC['a'];?>">
		<input type="hidden" name="do" value="<?php  echo $_GPC['do'];?>">
		<input type="hidden" name="m" value="<?php  echo $_GPC['m'];?>">
		<input type="hidden" name="op" value="<?php  echo $op;?>">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="btn-group">
					<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
						<span class="fa fa-list"></span>
						<?php  if(empty($_GPC['cid'])) { ?>
							全部商品
						<?php  } else { ?>
							<?php  echo $categories[$cid]['name'];?>
						<?php  } ?>
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu">
						<li><a href="<?php  echo $this->createMobileUrl('store', array('op'=>'display','cid'=>0));?>">全部</a></li>
						<?php  if(!empty($categories)) { ?>
						<li class="divider"></li>
						<?php  if(is_array($categories)) { foreach($categories as $key => $category) { ?>
						<li><a href="<?php  echo $this->createMobileUrl('store', array('op'=>'display','cid'=>$key));?>"><?php  echo $category['name'];?></a></li>
						<?php  } } ?>
						<?php  } ?>
					</ul>
				</div>
			</div>
			<div class="panel-body">
				<div class="row clearfix">
				<?php  if(is_array($goodses)) { foreach($goodses as $goodsid => $goods) { ?>
					<div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
						<div class="thumbnail clearfix" style="padding-bottom:10px;">
							<div class="logo" style="width:100%; margin:0 auto; height:200px; background:transparent url('<?php  echo tomedia($goods['image']);?>') no-repeat center center; background-size:cover; -webkit-background-size:cover; -moz-background-size:cover;"></div>
							<div class="row">
								<div class="col-xs-12 text-left">
									<h3 style="font-size:18px;"><span style="display:inline-block; max-width:100px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?php  echo $goods['name'];?></span> <label class="pull-right">￥<b><?php  echo $goods['price'];?></b></label></h3>
								</div>
							</div>
							<div class="row col-xs-12">
								<input type="hidden" name="goodsid" value="<?php  echo $goodsid;?>">
								<div class="pull-left input-group" style="width:80px;">
									<span class="input-group-btn" style="border-right: 0px;">
										<a href="javascript:" class="btn btn-default btn-sm cart_decrease"><i class="fa fa-minus"></i></a>
									</span>
									<input class="form-control input-sm cart_quantity" style="width:40px; text-align:center;" value="<?php  echo intval($carts[$goodsid]['quantity']);?>">
									<span class="input-group-btn">
										<a href="javascript:" class="btn btn-default btn-sm cart_increase"><i class="fa fa-plus"></i></a>
									</span>
								</div>
								<div class="pull-right">
									<a href="<?php  echo $this->createMobileUrl('cart', array('op'=>'display'));?>" class="btn btn-danger btn-sm" role="button">进入购物车</a>
								</div>
							</div>
						</div>
					</div>
				<?php  } } ?>
				</div>
				<?php  if(empty($goodses)) { ?>
					尚未添加商品
				<?php  } ?>
				<?php  echo $pager;?>
			</div>
		</div>
	</form>
</div>

<script>

var sharedata = {
	title: '微信JS-SDK Demo',
	desc: '微信JS-SDK,帮助第三方为用户提供更优质的移动web服务',
	link: 'http://demo.open.weixin.qq.com/jssdk/',
	imgUrl: 'http://bbs.we7.cc/static/image/common/logo.png',
	success: function(){
		alert('分享成功.');
	}
};

require(['jquery', 'util'],function($, util){
	$(function(){
		
		$('.cart_quantity').each(function(){
			if($(this).val() == '0'){
				$(this).parent().find('.cart_decrease').attr('disabled', true);
			}
		});
		
		$('.cart_decrease').click(function(){
			if($(this).attr('disabled') == true){
				return;
			}
			var father = $(this).parent().parent();
			var objDecrease = $(this);
			var objQuantity = father.find('.cart_quantity');
			var objGoodsid = father.prev();
			quantity = parseInt(objQuantity.val());
			if(!isNaN(quantity)){
				if(quantity >= 1){
					quantity--;
					$.post('<?php  echo $this->createMobileUrl('cart', array('op'=>'decrease'));?>',{goodsid: objGoodsid.val()},function(){
						objQuantity.val(quantity);
						if(quantity == 0){
							objDecrease.attr('disabled', true);
						}
					});
				}
			}
		});
		
		$('.cart_increase').click(function(){
			var father = $(this).parent().parent();
			var objIncrease = $(this);
			var objQuantity = father.find('.cart_quantity');
			var objGoodsid = father.prev();
			quantity = parseInt(objQuantity.val());
			if(!isNaN(quantity)){
				quantity++;
				$.post("<?php  echo $this->createMobileUrl('cart', array('op'=>'increase'));?>",{goodsid: objGoodsid.val()},function(){
					objQuantity.val(quantity);
					father.find('.cart_decrease').attr('disabled', false);
				});
			}
		});
	});
});
</script>

<?php (!empty($this) && $this instanceof WeModuleSite) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>