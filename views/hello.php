<?php $PU->emitHtmlBoilerplate("Welcome! - PHP Template Project"); ?>

<h1>Welcome to PHP Template Project!</h1>

<p>This code was generated by PHP Project Initializer.
You probably want to make some modifications.</p>

<p>See also: <a href="<?php echo htmlspecialchars($helloUri); ?>"><?php echo htmlspecialchars($helloUri); ?></a></p>

<h4>Some REST Services</h4>
<ul>
<?php echo implode("\n",$classLinks) ?>
</ul>
<div style="position:fixed; bottom:0; right:0">Woo! <img src="images/head.png" width="96" height="128" align="middle"/></div>
</body></html>
