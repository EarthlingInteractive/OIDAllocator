<html>
<head>
<title><?php echo htmlspecialchars("{$title} - OID Allocator"); ?></title>
<!-- TODO: [Relative!] link to CSS instead of including inline -->
<style>
span.oid-crumbs {
	margin: 4px 8px;
	padding: 4px 8px;
}

h1, h2, h3, h4, p, textarea {
	margin-top: 8px;
	margin-bottom: 8px;
}

table.bolly {
	border-collapse: collapse;
	background-color: rgba(192,128,64,0.1);
}
table.bolly tr * {
	padding-right: 8px;
	padding-left: 8px;
}

table.bolly tr *:nth-child(2n+2) {
	background: rgba(192,128,64,0.1);
}
table.bolly tbody tr:nth-child(2n+1) {
	background: rgba(192,128,64,0.1);
}
table.bolly td, table.bolly th {
	border: 1px solid rgba(192,128,64,0.5);
}

td.null {
	background-color: rgba(128,128,0,0.2);
}

.tabby {
	display: table;
}
.tabby > div {
	display: table-row;
}
.tabby > div > * {
	display: table-cell;
}

.tabby > div > label:nth-child(1) {
	padding-right: 10px;
}

.nav-bar {
	border-bottom: 1px solid silver;
	overflow: auto;
	padding: 0;
}
ul.nav1 { float: left; }
ul.nav2 { float: right; }
ul.nav1, ul.nav2 {
	margin: 0;
	display: table;
}
ul.nav1 > li, ul.nav2 li {
	display: table-cell;
	padding: 4px 8px;
}
.footer {
	text-align: center;
	border-top: 1px solid silver;
	color: rgba(128,192,128,0.5);
}

.error-messages {
	color: red;
}
</style>
<script>
var footerClickCount = 0;
function footerClicked() {
	++footerClickCount;
	if( footerClickCount > 5 ) alert("Teehee!  That tickles!");
}
</script>
</head>
<body>

<div class="nav-bar">
<ul class="nav1">
<li><?php echo $PU->linkHtml('/','Home'); ?></li>
<li class="devtool" style="display:none">Hi there!</li>
</ul>

<ul class="nav2">
<?php if( $loggedInUser ): ?>
<li>Logged in as <?php eht($loggedInUser['username']); ?> <?php echo $PU->linkHtml('./logout','Log Out'); ?></li>
<?php else: ?>
<li><?php echo $PU->linkHtml('/login','Log In'); ?></li>
<li><?php echo $PU->linkHtml('/register','Register'); ?></li>
<?php endif; ?>
</ul>
</div>
