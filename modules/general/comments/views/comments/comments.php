<hr />
<div class="row">
	<div class="span6">
		<div id="vk_comments"></div>
		<script type="text/javascript" src="//vk.com/js/api/openapi.js?79"></script>
		<script type="text/javascript">
		  VK.init({apiId: <?=$cfg['vk_key'];?>, onlyWidgets: true});
		</script>
		<script type="text/javascript">
		VK.Widgets.Comments("vk_comments", {limit: <?=$cfg['vk_messages'];?>, width: "<?=$cfg['vk_width'];?>", attach: "*"});
		</script>
	</div>
	<div class="span6">
		<div class="fb-comments" data-href="<?=$_SERVER['HTTP_HOST'];?>/<?=$_SERVER['REQUEST_URI'];?>" data-width="<?=$cfg['fb_width'];?>" data-num-posts="<?=$cfg['fb_messages'];?>"></div>
	</div>
</div>