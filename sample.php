<?php
/*
 * Sample to demostrate how to use the class
 */

require_once 'Netfile.php';

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
$doc = $nefile->createT1NetfileDoc( 2, 2010);

// 3. Check validation status and print out error messages
if (!$nefile->isValid) display_errors ($nefile->errors);

// 4. Optionally retrieve serialized XML document representation
$xml = $nefile->XML;
echo $xml;