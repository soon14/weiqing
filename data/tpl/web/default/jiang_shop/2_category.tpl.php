<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<ul class="nav nav-tabs">
	<li <?php  if($op == 'display') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('category', array('op'=>'display'));?>">商品分类列表</a></li>
	<li <?php  if($op == 'create') { ?>class="active"<?php  } ?>><a href="<?php  echo $this->createWebUrl('category', array('op'=>'create'));?>">添加分类</a></li>
</ul>

<?php  if($op == 'display') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" id="form">
		<div class="panel panel-default">
			<div class="panel-heading">便利店 - 商品分类</div>
			<div class="panel-body">
				<div class="table-responsive panel-body">
					<table class="table table-hover" style="min-width: 300px;">
						<thead class="navbar-inner">
							<tr>
								<th style="width:120px;">分类名称</th>
								<th style="width:80px;">排序</th>
								<th style="width:80px;">操作</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php  if(is_array($categories)) { foreach($categories as $key => $item) { ?>
							<tr>
								<td class="text-left">
									<input type="text" name="categories[<?php  echo $key;?>][name]" value="<?php  echo $item['name'];?>" class="form-control categories-name">
								</td>
								<td>
									<input type="text" name="categories[<?php  echo $key;?>][orderno]" value="<?php  echo $item['orderno'];?>" class="form-control">
								</td>
								<td>
									<a onclick="if(!confirm('删除后将不可恢复,确定删除吗?')) return false;" href="<?php  echo $this->createWebUrl('category', array('op'=>'delete', 'id'=>$key));?>" class="btn btn-default btn-danger" data-toggle="tooltip" data-placement="top" title="" data-original-title="删除"><i class="fa fa-times"></i> </a>
								</td>
								<td></td>
							</tr>
							<?php  } } ?>
							<?php  if(!empty($categories)) { ?>
							<tr>
								<td colspan="4">
									<input name="submit" type="submit" value="保存" class="btn btn-primary" />
									<input type="hidden" name="token" value="<?php  echo $_W['token'];?>" />
								</td>
							</tr>
							<?php  } else { ?>
							<tr>
								<td colspan="4">
									尚未添加商品分类
								</td>
							</tr>
							<?php  } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</form>
</div>

<script>
require(['jquery', 'util'], function($, util){
	$(function(){
		$('#form').submit(function(){
			var result = true;
			$('input.categories-name').each(function(){
				if(this.value.length == 0){
					return result = false;
				}
			});
			if(!result){
				util.message('有分类名称未填写.');
			}
			return result;
		});
	});
});
</script>
<?php  } ?> <!-- end of display -->

<?php  if($op == 'create') { ?>
<div class="main">
	<form action="" method="post" class="form-horizontal form" id="form">
		<div class="panel panel-default">
			<div class="panel-heading">便利店 - 商品分类</div>
			<div class="panel-body">
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">名称</label>
					<div class="col-xs-12 col-sm-4">
						<input type="text" name="category[name]" class="form-control" value="" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-3 col-md-2 col-lg-2 control-label">排序</label>
					<div class="col-xs-12 col-sm-4">
						<input type="text" name="category[orderno]" class="form-control" value="" />
					</div>
				</div>
				<div class="form-group">
					<div class="col-xs-12 col-sm-9 col-md-10 col-lg-10 col-sm-offset-3 col-md-offset-2 col-lg-offset-2">
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
			var result = true;
			if($('input[name="category[name]"]').val() == ''){
				result = false;
				util.message('未填写分类名称.');
			}
			return result;
		});
	});
});
</script>
<?php  } ?> <!-- end of create -->

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>