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
