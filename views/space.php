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

<?php if($subSpaces): ?>
<h3>Sub-objects</h3>

<table class="bolly">
<?php foreach($subSpaces as $k=>$subSpace): ?>
<tr><td align="right"><?php eht($k); ?></td>
    <td><?php echo $PU->linkHtml("/spaces/{$subSpace['urlPath']}", $subSpace['name']); ?></td></tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

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

<?php if($anyRegionsAllocatable): ?>
<p>Click 'Next!' to get a single ID from a region.</p>

<p>If you need a bunch, fill in the number you need and click 'Allocate'.</p>
<?php endif; ?>

<script type="text/javascript">//<![CDATA[
	function submitRequestForNewId(regionKey) {
		document.getElementById("region-"+regionKey+"-allocation-request-box").value = 1;
		document.getElementById("allocation-form").submit();
	}
//]]></script>

<form method="POST" id="allocation-form">

<div style="display:inline-block">
<table class="bolly">
<thead>
<tr><th>Name</th><th>Bottom</th><th>Top</th><th>Highest used ID</th>
  <?php if($anyRegionsAllocatable) { ?><th colspan="2">Allocate</th><?php } ?></tr>
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
</table>

<?php if($anyRegionsAllocatable): ?>
<h4>Optional: add a short note about this allocation:</h4>
<textarea style="width:100%" name="notes" placeholder="Identify ALL THE THINGS! &nbsp; -- Steve"></textarea>
<div style="text-align:right">
  <input type="submit" value="Allocate"/>
</div>
<?php endif; ?>
</div>

</form>
<?php endif; ?>

<?php if($space['allocations']): ?>
<h3>Allocations</h3>

<table class="bolly">
  <thead>
    <tr>
      <th>Min</th>
      <th>Max</th>
      <th>Notes</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach($space['allocations'] as $k=>$allocation): ?>
   <?php if(!empty($allocation['notes'])): ?>
   <?php list($min,$max) = explode('-',$k); ?>
     <tr>
       <td align="right"><?php eht($min); ?></td>
       <td align="right"><?php eht($max); ?></td>
       <td><?php echo str_replace("\n",'<br />',htmlspecialchars($allocation['notes'])); ?></td>
     </tr>
   <?php endif; ?>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>


<?php $PU->emitHtmlFooter(); ?>
