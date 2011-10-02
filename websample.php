<?php
/*
 * Sample to demostrate how to use the class
 */
require_once 'Netfile.php';

/**
 * XML highlighting
 *
 * @param sting serialized XML
 * @return string formated and colorified XML
 */
function xml_highlight($s)
{
	$s = htmlspecialchars($s);
	$s = preg_replace("#&lt;([/]*?)(.*)([\s]*?)&gt;#sU",
		"<font color=\"#0000FF\">&lt;\\1\\2\\3&gt;</font>",$s);
	$s = preg_replace("#&lt;([\?])(.*)([\?])&gt;#sU",
		"<font color=\"#800000\">&lt;\\1\\2\\3&gt;</font>",$s);
	$s = preg_replace("#&lt;([^\s\?/=])(.*)([\[\s/]|&gt;)#iU",
		"&lt;<font color=\"#808000\">\\1\\2</font>\\3",$s);
	$s = preg_replace("#&lt;([/])([^\s]*?)([\s\]]*?)&gt;#iU",
		"&lt;\\1<font color=\"#808000\">\\2</font>\\3&gt;",$s);
	$s = preg_replace("#([^\s]*?)\=(&quot;|')(.*)(&quot;|')#isU",
		"<font color=\"#800080\">\\1</font>=<font color=\"#FF00FF\">\\2\\3\\4</font>",$s);
	$s = preg_replace("#&lt;(.*)(\[)(.*)(\])&gt;#isU",
		"&lt;\\1<font color=\"#800080\">\\2\\3\\4</font>&gt;",$s);
	return nl2br($s);
}

/**
 * Validation error message formating
 * @param object $error validatatin error message
 * @return string formated error message wiht HTML tags
 */
function display_error($error)
{
    $return = "<br/>\n";
    switch ($error->level) {
        case LIBXML_ERR_WARNING:
            $return .= "<b>Warning $error->code</b>: ";
            break;
        case LIBXML_ERR_ERROR:
            $return .= "<b>Error $error->code</b>: ";
            break;
        case LIBXML_ERR_FATAL:
            $return .= "<b>Fatal Error $error->code</b>: ";
            break;
    }
    $return .= trim($error->message);
    if ($error->file) {
        $return .=    " in <b>$error->file</b>";
    }
    $return .= " on line <b>$error->line</b>\n";

    return $return;
}

/**
 * Helper for printing "nice" error messages
 * @param array() $errors validation error message list
 */
function display_errors( $errors) {
	foreach ($errors as $error) {
        print display_error($error);
    }
}


// 1. Instantiate XML document creater object (can be used for multiple/repeated document creation)
$nefile = new Netfile();

// 2. Create, validtate and return DOM Documenet representation of the T1Netfile document

$userId = isset($_GET["userId"]) ? $_GET["userId"] : 2;
$taxYear = isset($_GET["taxYear"]) ? $_GET["taxYear"] : 2010;

$doc = $nefile->createT1NetfileDoc( $userId, $taxYear);

// 3. Check validation status and print out error messages
if (!$nefile->isValid) { ?>
<html>
	<head>
		<title>Error Messages</title>
	</head>
	<body>
		<h1>Document validation Error Messages</h1>
	<?php
	display_errors ($nefile->errors);
	echo '<div style="border-width:.2em;border-style: dotted;border-color: #900;"><pre>';
	echo xml_highlight($nefile->XML);
	echo "</pre></div></body></html>";
}
else {

	// 4. Optionally retrieve serialized XML document representation
	$xml = $nefile->XML;
	header('Content-Type: text/xml');
	echo $xml;
}
?>