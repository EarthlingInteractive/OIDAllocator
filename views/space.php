<?php $PU->emitHtmlBoilerplate("Welcome!", $params); ?>

<?php if($pageTitle): ?><h2><?php eht($pageTitle); ?></h2><?php endif; ?>

<ul>
<?php foreach($subSpaces as $subSpace): ?>
<li><?php echo $PU->linkHtml("/spaces/{$subSpace['urlPath']}", $subSpace['name']); ?></li>
<?php endforeach; ?>
</ul>

<?php if(!empty($newItemIds)): ?>
<h3>Congratulations on your new item IDs!</h3>
<ul>
<?php foreach($newItemIds as $id): ?>
<li><?php echo $PU->linkHtml("$id/", $id); ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if(!empty($space['regions'])): ?>
<h3>Regions</h3>

<form method="POST">
<table>
<thead>
<tr><th>Name</th><th>Bottom</th><th>Top</th><th>Highest used ID</th></tr>
</thead>
<tbody>
<?php foreach($space['regions'] as $regionKey=>$region): ?>
<?php

$highestId = isset($space['counters'][$regionKey]) ?
	$space['counters'][$regionKey] : '';

?>
<tr>
<td><?php eht($regionKey); ?></td>
<td align="right"><?php eht($region['bottom']); ?></td>
<td align="right"><?php eht($region['top']); ?></td>
<td align="right"><?php eht($highestId); ?></td>
<td><input type="text" name="regions[<?php eht($regionKey); ?>][allocationRequest]" size="2" value="0"/></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<input type="submit" value="Allocate"/>
</form>
<?php endif; ?>

<?php $PU->emitHtmlFooter(); ?>
