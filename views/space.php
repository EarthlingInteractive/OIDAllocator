<?php $PU->emitHtmlBoilerplate($pageTitle, $params); ?>

<style>/*<![CDATA[*/
.fake-button, input[type=submit] {
	background: silver;
	border: 2px silver outset;
	outline: 1px solid gray;
	cursor: pointer;
	font-size: 10pt;
	font-family: Sans;
	padding: 0px 4px;
}
.fake-button:active, input:active[type=submit] {
	background: silver;
	border: 2px silver inset;
}
/*]]>*/</style>

<?php $PU->emitView('crumz', $params); ?>

<?php if($pageTitle): ?><h2><?php eht($pageTitle); ?></h2><?php endif; ?>

<?php if( isset($space['description']) ) echo "<p>", htmlspecialchars($space['description']), "</p>\n"; ?>

<ul>
<?php foreach($subSpaces as $subSpace): ?>
<li><?php echo $PU->linkHtml("/spaces/{$subSpace['urlPath']}", $subSpace['name']); ?></li>
<?php endforeach; ?>
</ul>

<?php if(!empty($newItemIds)): ?>
<h3>Congratulations on your new item <?php eht(count($newItemIds) == 1 ? 'ID' : 'IDs'); ?>!</h3>
<ul>
<?php foreach($newItemIds as $id): ?>
<li><?php echo $PU->linkHtml("$id/", $id); ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if(!empty($space['regions'])): ?>
<h3>Regions</h3>

<p>Click 'Next!' to get a single ID from a region.</p>

<p>If you need a bunch, fill in the number you need and click 'Allocate'.</p>

<script type="text/javascript">//<![CDATA[
	function submitRequestForNewId(regionKey) {
		document.getElementById("region-"+regionKey+"-allocation-request-box").value = 1;
		document.getElementById("allocation-form").submit();
	}
//]]></script>

<form method="POST" id="allocation-form">
<table>
<thead>
<tr><th>Name</th><th>Bottom</th><th>Top</th><th>Highest used ID</th><th colspan="2">Allocate</th></tr>
</thead>
<tbody>
<?php foreach($space['regions'] as $regionKey=>$region): ?>
<?php

$highestId = isset($space['counters'][$regionKey]) ?
	$space['counters'][$regionKey] : '';
$allocatable = !empty($region['allocatable']);

?>
<tr>
<td><?php eht($regionKey); ?></td>
<td align="right"><?php eht($region['bottom']); ?></td>
<td align="right"><?php eht($region['top']); ?></td>
<td align="right"><?php eht($highestId); ?></td>
<?php if($allocatable): ?>
<td align="right"><input type="text"
  id="region-<?php eht($regionKey); ?>-allocation-request-box"
  name="regions[<?php eht($regionKey); ?>][allocationRequest]" size="2" value="0"/></td>
<td><a class="fake-button"
       title="Allocate a single ID from this region"
       onclick="submitRequestForNewId(<?php eht(json_encode($regionKey)); ?>); return false;"
     >Next!</a></td>
<?php else: ?>
<?php endif; ?>
</tr>
<?php endforeach; ?>
</tbody>
<tfoot>
<tr>
  <td></td>
  <td></td>
  <td></td>
  <td></td>
  <td colspan="2"><input type="submit" value="Allocate" style="width:100%"/></td>
</tr>
</tfoot>
</table>

</form>
<?php endif; ?>

<?php $PU->emitHtmlFooter(); ?>
