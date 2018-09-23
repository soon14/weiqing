<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<ul class="nav nav-tabs">
	<li><a href="<?php  echo $this->createWebUrl('goods', array('op'=>'display'));?>">商品列表</a></li>
	<li <?php  if(empty($id)) { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('goods', array('op'=>'edit'));?>">添加商品</a></li>
	<?php  if(!empty($id)) { ?>
	<li class="active"><a href="<?php  echo $this->createWebUrl('goods', array('op'=>'edit', 'id'=>$id));?>">编辑商品</a></li>
	<?php  } ?>
</ul>

<div class="main">
	<form action="" method="post" class="form-horizontal form" id="form">
		<div class="panel panel-default">
			<div class="panel-heading">便利店 - 商品</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">品名</label>
					<div class="col-xs-12 col-sm-8">
						<input type="text" name="goods[name]" class="form-control" value="<?php  echo $goods['name'];?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">分类</label>
					<div class="col-xs-12 col-sm-8">
						<select class="form-control" name="goods[categoryid]">
							<option value="">选择分类</option>
							<?php  if(is_array($categories)) { foreach($categories as $cid => $category) { ?>
							<option value="<?php  echo $cid;?>" <?php  if($goods['categoryid'] == $cid) { ?>selected="selected"<?php  } ?>><?php  echo $category['name'];?></option>
							<?php  } } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">外观</label>
					<div class="col-xs-12 col-sm-8">
						<?php  echo tpl_form_field_image('goods[image]', $goods['image']);?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">商品编码</label>
					<div class="col-xs-12 col-sm-8">
						<input type="text" name="goods[sn]" class="form-control" value="<?php  echo $goods['sn'];?>" />
						<span class="help-block">知道商品编码可以很方便的查找商品</span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">进价</label>
					<div class="col-xs-12 col-sm-8">
						<div class="input-group">
							<input type="text" name="goods[cost]" class="form-control" value="<?php  echo $goods['cost'];?>" >
							<span class="input-group-addon">元</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">售价</label>
					<div class="col-xs-12 col-sm-8">
						<div class="input-group">
							<input type="text" name="goods[price]" class="form-control" value="<?php  echo $goods['price'];?>" >
							<span class="input-group-addon">元</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">库存</label>
					<div class="col-xs-12 col-sm-8">
						<div class="input-group">
							<input type="text" name="goods[quantity]" class="form-control" value="<?php  echo $goods['quantity'];?>" >
							<span class="input-group-addon">件</span>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">状态</label>
					<div class="col-xs-12 col-sm-8">
						<label class="radio radio-inline">
							<input type="radio" name="goods[status]" value="1" <?php  if(intval($goods['status']) != 2) { ?>checked="checked"<?php  } ?>> 下架
						</label>
						<label class="radio radio-inline">
							<input type="radio" name="goods[status]" value="2" <?php  if(intval($goods['status']) == 2) { ?>checked="checked"<?php  } ?>> 上架
						</label>
					</div>
				</div>
				<?php  if(!empty($id)) { ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">添加日期</label>
					<div class="col-xs-12 col-sm-8">
						<p class="form-control-static"><?php  echo date('Y-m-d', $goods['createtime']);?></p>
					</div>
				</div>
				<?php  } ?>
				<div class="form-group">
					<div class="col-xs-12 col-sm-9 col-md-10 col-lg-10 col-sm-offset-3 col-md-offset-2 col-lg-offset-2">
						<input type="hidden" name="id" value="<?php  echo $goods['id'];?>" />
						<input name="submit" type="submit" value="提交" class="btn btn-primary" />
						<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
require(['jquery', 'util'], function($, util){
	$(function(){
		$('#form').submit(function(){
			if($('input[name="goods[name]"]').val() == ''){
				util.message('请填写商品名称.');
				return false;
			}
			if($('input[name="goods[sn]"]').val() == ''){
				util.message('请填写商品编码.');
				return false;
			}
			if($('input[name="goods[image]"]').val() == ''){
				util.message('请上传商品外观照片.');
				return false;
			}
			var cost = parseFloat($('input[name="goods[cost]"]').val());
			if(isNaN(cost)){
				util.message('请填写商品进价.');
				return false;
			}
			var price = parseFloat($('input[name="goods[price]"]').val());
			if(isNaN(price)){
				util.message('请填写商品售价.');
				return false;
			}
			var quantity = parseFloat($('input[name="goods[quantity]"]').val());
			if(isNaN(quantity)){
				util.message('请填写商品库存.');
				return false;
			}
			return true;
		});
	});
});
</script>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>