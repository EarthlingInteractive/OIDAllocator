<?php $PU->emitHtmlBoilerplate($pageTitle, $params); ?>

<?php $PU->emitView('crumz', $params); ?>

<?php if(count($newItemIds) == 1): ?>
<p style="text-align: center; font-size: 48">
<?php foreach($newItemIds as $itemId) eht($itemId); ?>
</p>
<?php endif; ?>

<?php if(!empty($newItemIds)): ?>
<h3>Congratulations on your new item <?php eht(count($newItemIds) == 1 ? 'ID' : 'IDs'); ?>!</h3>
<table>
<thead>
<tr>
  <th>ID</th>
</tr>
</thead>
<?php foreach($newItemIds as $id): ?>
<tr>
  <td><?php echo $PU->linkHtml("$id/", $id); ?></td>
<!-- editing does not work yet
  <td><input type="text" name="items[<?php eht($id); ?>][name]" value="" placeholder="name this item!" size="15"/></td>
  <td><input type="text" name="items[<?php eht($id); ?>][notes]" value="" placeholder="other notes go here!" size="50"/></td>
-->
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>

<p><a href="./">Back to <?php eht($space['name']); ?></p>

<?php $PU->emitHtmlFooter(); ?>
