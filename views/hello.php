<?php $PU->emitHtmlBoilerplate("Welcome!", $params); ?>

<h1>Welcome to OID Allocator!</h1>

<ul>
<?php foreach($spaces as $spacePath=>$space): ?>
<li><?php echo $PU->linkHtml("spaces/{$space['urlPath']}", $space['name']); ?></li>
<?php endforeach; ?>
</ul>

<?php $PU->emitHtmlFooter(); ?>
