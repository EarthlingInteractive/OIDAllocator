<?php $PU->emitHtmlBoilerplate("Welcome!", $params); ?>

<h2><?php eht($space['name']); ?></h2>

<ul>
<?php foreach($subSpaces as $spacePath=>$space): ?>
<li><?php echo $PU->linkHtml("/spaces/{$space['urlPath']}", $space['name']); ?></li>
<?php endforeach; ?>
</ul>

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
<td><input type="text" size="2" value="0"/></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<input type="submit" value="Allocate"/>
</form>
<?php endif; ?>

<?php $PU->emitHtmlFooter(); ?>
