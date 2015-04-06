<?php $PU->emitHtmlBoilerplate("Welcome!"); ?>

<h1>Welcome to PHP Template Project!</h1>

<p>This code was generated by PHP Project Initializer.
You probably want to make some modifications.</p>

<p>See also: <a href="<?php echo htmlspecialchars($helloUri); ?>"><?php echo htmlspecialchars($helloUri); ?></a></p>

<h4>Some REST Services</h4>
<ul>
<?php echo implode("\n",$classLinks) ?>
</ul>
<div style="position:fixed; bottom:0; right:0">Woo! <img src="images/head.png" width="96" height="128" align="middle"/></div>

<style>
dl.tabby { display: table; }
dl.tabby div { display: table-row; }
dl.tabby dt {
	display: table-cell;
	padding: 2px 8px 2px 4px;
}
dl.tabby dt:after {
	content: ":";
}
dl.tabby dd {
	display: table-cell;
	padding: 2px 4px 2px 8px;
	text-align: right;
}
</style>

<h4>Other stuff:</h4>
<dl class="tabby">
<?php foreach($otherStuff as $thing=>$stuff): ?>
<div><dt><?php eht($thing); ?></dt><dd><?php eht($stuff); ?></dd></div>
<?php endforeach; ?>
</dl>

<?php $PU->emitHtmlFooter(); ?>
