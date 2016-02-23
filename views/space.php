<?php $PU->emitHtmlBoilerplate("Welcome!", $params); ?>

<?php
	$numParts = array();
	$nameParts = array();
	foreach($crumz as $crum) {
		$text = $crum['num'];
		
		if( empty($crum['isEmpty']) ) {
			$numParts[] = $PU->linkHtml("/spaces/".$crum['urlPath'], $text);//"<a href=\"".htmlspecialchars($PU->pathTo(
		} else {
			$numParts[] = htmlspecialchars($text);
		}
		
		if( !empty($crum['name']) ) {
			$text = $crum['name'];
			$nameParts[] = $PU->linkHtml("/spaces/".$crum['urlPath'], $text);
		}
	}
	
	if($numParts or $nameParts) {
		echo "<p>";
		if( $numParts ) echo '<span class="oid-crumbs">', implode('.', $numParts), '</span>';
		if( $nameParts) echo '<span class="oid-crumbs">(', implode(' &gt; ', $nameParts), ')</span>';
		echo "</p>\n";
	}
?>

<?php if($pageTitle): ?><h2><?php eht($pageTitle); ?></h2><?php endif; ?>

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

<script type="text/javascript">//<![CDATA[
	function submitRequestForNewId(regionKey) {
		document.getElementById("region-"+regionKey+"-allocation-request-box").value = 1;
		document.getElementById("allocation-form").submit();
	}
//]]></script>

<form method="POST" id="allocation-form">
<table>
<thead>
<tr><th>Name</th><th>Bottom</th><th>Top</th><th>Highest used ID</th></tr>
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
<td><input type="text"
  id="region-<?php eht($regionKey); ?>-allocation-request-box"
  name="regions[<?php eht($regionKey); ?>][allocationRequest]" size="2" value="0"/></td>
<td><button onclick="submitRequestForNewId(<?php eht(json_encode($regionKey)); ?>); return false;">Next!</button></td>
<?php else: ?>
<?php endif; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<input type="submit" value="Allocate"/>
</form>
<?php endif; ?>

<?php $PU->emitHtmlFooter(); ?>
