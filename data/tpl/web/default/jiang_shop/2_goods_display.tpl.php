<?php defined('IN_IA') or exit('Access Denied');?><?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/header', TEMPLATE_INCLUDEPATH)) : (include template('common/header', TEMPLATE_INCLUDEPATH));?>

<ul class="nav nav-tabs">
	<li class="active"><a href="<?php  echo $this->createWebUrl('goods', array('op'=>'display'));?>">商品列表</a></li>
	<li><a href="<?php  echo $this->createWebUrl('goods', array('op'=>'edit'));?>">添加商品</a></li>
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
						<input class="form-control" name="keyword" id="" type="text" value="<?php  echo $_GPC['keyword'];?>" placeholder="品名或编码">
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">状态</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<select name="status" class="form-control">
							<option value="0" <?php  if(intval($_GPC['status']) == 0) { ?>selected="selected"<?php  } ?>>全部</option>
							<option value="1" <?php  if(intval($_GPC['status']) == 1) { ?>selected="selected"<?php  } ?>>下架</option>
							<option value="2" <?php  if(intval($_GPC['status']) == 2) { ?>selected="selected"<?php  } ?>>上架</option>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-xs-12 col-sm-2 col-md-2 col-lg-1 control-label">分类</label>
					<div class="col-xs-12 col-sm-8 col-md-8 col-lg-6">
						<select class="form-control" name="categoryid">
							<option value="0" <?php  if(intval($_GPC['categoryid']) == 0) { ?>selected="selected"<?php  } ?>>全部分类</option>
							<?php  if(is_array($categories)) { foreach($categories as $cid => $category) { ?>
							<option value="<?php  echo $cid;?>" <?php  if(intval($_GPC['categoryid']) == $cid) { ?>selected="selected"<?php  } ?>><?php  echo $category['name'];?></option>
							<?php  } } ?>
						</select>
					</div>
					<div class="col-xs-12 col-sm-2 col-md-1 col-lg-1">
						<button class="btn btn-default"><i class="fa fa-search"></i> 搜索</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">商品列表</div>
		<div class="panel-body">
			<div class="table-responsive panel-body">
				<table class="table table-hover" style="min-width: 300px;">
					<thead class="navbar-inner">
						<tr>
							<th class="col-sm-2">品名</th>
							<th class="col-sm-2">编码</th>
							<th class="col-sm-2">外观</th>
							<th class="col-sm-1">进价</th>
							<th class="col-sm-1">售价</th>
							<th class="col-sm-1">库存</th>
							<th class="col-sm-1">状态</th>
							<th class="col-sm-2 text-right">操作</th>
						</tr>
					</thead>
					<tbody>
						<?php  if(is_array($goodses)) { foreach($goodses as $goodsid => $goods) { ?>
						<tr>
							<td><?php  echo $goods['name'];?></td>
							<td><?php  echo $goods['sn'];?></td>
							<td><image src="<?php  echo tomedia($goods['image']);?>" style="max-width: 48px; max-height: 48px; border: 1px dotted gray"></td>
							<td><?php  echo $goods['cost'];?></td>
							<td><?php  echo $goods['price'];?></td>
							<td><?php  echo $goods['quantity'];?></td>
							<td><?php  echo $this->getGoodsStatus($goods['status']);?></td>
							<td class="text-right">
								<a href="<?php  echo $this->createWebUrl('goods', array('op'=>'edit', 'id'=>$goodsid));?>" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="" data-original-title="编辑"><i class="fa fa-pencil"></i></a>
								<a onclick="if(!confirm('删除后将不可恢复,确定删除吗?')) return false;" href="<?php  echo $this->createWebUrl('goods', array('op'=>'delete', 'id'=>$goodsid));?>" class="btn btn-default btn-danger" data-toggle="tooltip" data-placement="top" title="" data-original-title="删除"><i class="fa fa-times"></i> </a>
							</td>
						</tr>
						<?php  } } ?>
						<?php  if(empty($goodses)) { ?>
						<tr>
							<td colspan="8">
								尚未添加商品
							</td>
						</tr>
						<?php  } ?>
					</tbody>
				</table>
				<?php  echo $pager;?>
			</div>
		</div>
	</div>
</div>

<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer', TEMPLATE_INCLUDEPATH)) : (include template('common/footer', TEMPLATE_INCLUDEPATH));?>