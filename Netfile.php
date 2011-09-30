<?php

function libxml_display_error($error) 
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

function libxml_display_errors() { 
    $errors = libxml_get_errors(); 
    foreach ($errors as $error) { 
        print libxml_display_error($error); 
    } 
    libxml_clear_errors(); 
} 

// Enable user error handling 
libxml_use_internal_errors(true);
libxml_disable_entity_loader( false);


/**
 * Data access component
 */
class DataSource {

	static private $_user = 'root';
	static private $_pass = NULL;
	static private $_connectionString = 'mysql:host=localhost;dbname=test';

	/**
	 *
	 * @return type 
	 */
	function __construct() {
		return;
	}
	
	/**
	 *
	 * @param int $userId User ID
	 * @param int $taxYear Tax Year
	 * @return array() 
	 */
	static function getData(
			$userId = 2,
			$taxYear = 2010) {

		try {
			$dbh = new PDO( 
					static::$_connectionString, 
					static::$_user, 
					static::$_pass );
			$stmt = $dbh->prepare(
					"SELECT
						r.*,
						SUBSTRING_INDEX(line_id,'_',2) AS line_type, -- Line ID prefix
						SUBSTRING_INDEX(line_id,'_',-1) AS field_code -- Field code
					FROM result_data AS r
					WHERE user_id = ? AND tax_year = ?");
			$stmt->execute( array( $userId, $taxYear ));
			$rows = $stmt->fetchAll(PDO::FETCH_CLASS);
			$dbh = null;
		} catch (PDOException $e) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}

		return $rows;
	}

}


// TODO: Encapsulate into a class

ob_start();
// to run from CLI: php -f Netfile.php | more
echo '<?xml version="1.0"?>
';
$SoftwareID='061G';
$SoftwareRevisionDate='2011-08-06';

// Tax reuturns data - should be supplied from DB
// Taxpayer data:
$taxYear = 2010;
//...

// Element list of elemts wiht values
$elementList = array(
	'AddressPostalCode',
	'AddressProvinceCode',
	'AlternateAddressAuthorizationCode',
	'BoatName',
	'BusinessAddressStreetText',
	'BusinessName',
	'BusinessOwnershipPercent',
	'CanadianCitizenshipIndicator',
	'CorrespondenceLanguageCode',
	'ElectionsCanadaAuthorizationIndicator',
	'FamilyName',
	'FinalBusinessYearIndicator',
	'FiscalPeriodEndDate',
	'FiscalPeriodStartDate',
	'GivenName',
	'GSTHSTCreditApplicationIndicator',
	'NAICSCode',
	'PartnerBusinessOwnershipPercent',
	'PartnerPIN',
	'PartnershipNumber',
	'SelectedFinancialDataRecordCount',
	'SelfEmployedSpouseDeadlineIndicator',
	'TaxpayerBirthDate',
	'TaxpayerCurrentResidenceProvinceCode',
	'TaxpayerMaritalStatusCode',
	'TaxpayerSocialInsuranceNumber',
	'TaxpayerYearEndResidenceProvinceCode',
	'TaxShelterNumber',
	'TaxYear',
	'TelephoneAreaCode',
	'TelephoneLocalNumber' );

// default element values:
foreach ($elementList as $elment) {
	${$elment} = '';
}
/* 
// Uncomment, if you need to set other default vlues
$addressPostalCode='';
$addressProvinceCode='';
$alternateAddressAuthorizationCode='';
$boatName='';
$businessAddressStreetText='';
$businessName='';
$businessOwnershipPercent='';
$canadianCitizenshipIndicator='';
$correspondenceLanguageCode='';
$electionsCanadaAuthorizationIndicator='';
$familyName='';
$finalBusinessYearIndicator='';
$fiscalPeriodEndDate='';
$fiscalPeriodStartDate='';
$givenName='';
$gSTHSTCreditApplicationIndicator='';
$nAICSCode='';
$partnerBusinessOwnershipPercent='';
$partnerPIN='';
$partnershipNumber='';
$selectedFinancialDataRecordCount='';
$selfEmployedSpouseDeadlineIndicator='';
$taxpayerBirthDate='';
$taxpayerCurrentResidenceProvinceCode='';
$taxpayerMaritalStatusCode='';
$taxpayerSocialInsuranceNumber='';
$taxpayerYearEndResidenceProvinceCode='';
$taxShelterNumber='';
$taxYear='';
$telephoneAreaCode='';
$telephoneLocalNumber='';
*/

$data = DataSource::getData( $userId = 2, $taxYear = $taxYear);

// ReturnLines
$returnLines = array();

foreach( $data as $row ) {
	$lineId = $row->line_id;
	if (	$row->line_type == 'TRC_L' 
			&& $row->field_code >= 100
			&& $row->field_code <= 8000)
		$returnLines[] = (object) array( 'code'=>$row->field_code, 'value'=>$row->line_value);
	elseif ( in_array ( $lineId, $elementList ) )
		${lcfirst($lineId)} = $row->line_value;
}

?>
<net:Netfile 
	xmlns:comm="http://www.cra-arc.gc.ca/xmlns/comm/2012/1-0-0" 
	xmlns:net="http://www.cra-arc.gc.ca/xmlns/netfile/2012/1-0-0" 
	xmlns:sdt="http://www.cra-arc.gc.ca/xmlns/sdt/2-0-0" 
	xmlns:sfd="http://www.cra-arc.gc.ca/xmlns/sfd/2012/1-0-0" 
	xmlns:t1="http://www.cra-arc.gc.ca/xmlns/t1/2012/1-0-0" 
	xmlns:tp="http://www.cra-arc.gc.ca/xmlns/tp/2012/1-0-0">
	<net:T1Netfile>
		<t1:T1Return>
			<comm:SoftwareID><?=$SoftwareID?></comm:SoftwareID>
			<comm:SoftwareRevisionDate><?=$SoftwareRevisionDate?></comm:SoftwareRevisionDate>
			<t1:TaxpayerData>
				<tp:TaxpayerBasicData>
					<tp:TaxpayerSocialInsuranceNumber><?=$taxpayerSocialInsuranceNumber;?></tp:TaxpayerSocialInsuranceNumber>
					<tp:TaxYear><?=$taxYear?></tp:TaxYear>
					<tp:TaxpayerBirthDate><?=$taxpayerBirthDate?></tp:TaxpayerBirthDate>
					<tp:TaxpayerMaritalStatusCode><?=$taxpayerMaritalStatusCode?></tp:TaxpayerMaritalStatusCode>
					<tp:SelfEmployedSpouseDeadlineIndicator><?=$SelfEmployedSpouseDeadlineIndicator?></tp:SelfEmployedSpouseDeadlineIndicator>
				</tp:TaxpayerBasicData>
				<tp:TaxpayerResidenceData>
					<tp:TaxpayerYearEndResidenceProvinceCode><?=$taxpayerYearEndResidenceProvinceCode?></tp:TaxpayerYearEndResidenceProvinceCode>
					<tp:TaxpayerCurrentResidenceProvinceCode><?=$taxpayerCurrentResidenceProvinceCode?></tp:TaxpayerCurrentResidenceProvinceCode>
				</tp:TaxpayerResidenceData>
				<tp:TaxpayerContactData>
					<comm:TaxpayerName>
						<comm:GivenName><?=$givenName?></comm:GivenName>
						<comm:FamilyName><?=$familyName?></comm:FamilyName>
					</comm:TaxpayerName>
					<tp:TaxpayerMailingAddress>
						<comm:CanadianMailingAddress>
							<comm:AddressStreetText><?=$addressStreetText?></comm:AddressStreetText>
							<comm:AddressMunicipalityName><?=$addressMunicipalityName?></comm:AddressMunicipalityName>
							<comm:AddressProvinceCode><?=$addressProvinceCode?></comm:AddressProvinceCode>
							<comm:AddressPostalCode><?=$addressPostalCode?></comm:AddressPostalCode>
						</comm:CanadianMailingAddress>
					</tp:TaxpayerMailingAddress>
					<tp:TaxpayerHomeTelephoneNumber>
						<comm:TelephoneAreaCode><?=$telephoneAreaCode?></comm:TelephoneAreaCode>
						<comm:TelephoneLocalNumber><?=$telephoneLocalNumber?></comm:TelephoneLocalNumber>
					</tp:TaxpayerHomeTelephoneNumber>
					<tp:AlternateAddressAuthorizationCode><?=$alternateAddressAuthorizationCode?></tp:AlternateAddressAuthorizationCode>
					<tp:CorrespondenceLanguageCode><?=$correspondenceLanguageCode?></tp:CorrespondenceLanguageCode>
				</tp:TaxpayerContactData>
			</t1:TaxpayerData>
			<t1:RelatedProgramData>
				<t1:CanadianCitizenshipIndicator><?=$canadianCitizenshipIndicator?></t1:CanadianCitizenshipIndicator>
				<t1:ElectionsCanadaAuthorizationIndicator><?=$electionsCanadaAuthorizationIndicator?></t1:ElectionsCanadaAuthorizationIndicator>
				<t1:GSTHSTCreditApplicationIndicator><?=$gSTHSTCreditApplicationIndicator?></t1:GSTHSTCreditApplicationIndicator>
			</t1:RelatedProgramData>
<?php foreach ($returnLines as $line): ?>
			<comm:ReturnLine>
				<comm:FieldCode><?=$line->code ?></comm:FieldCode>
				<comm:FieldValue><?=$line->value ?></comm:FieldValue>
			</comm:ReturnLine>
<?php endforeach;?>
		</t1:T1Return>
	</net:T1Netfile>
</net:Netfile>
<?php
$xmlStr=ob_get_contents();
ob_end_clean();

$xml = new DOMDocument(); 
$xml->loadXML($xmlStr);
$doc->xinclude();

$xsd =  dirname(__FILE__) . '/xsd/netfile.xsd';

if (!$xml->schemaValidate($xsd)) { 
    print 'DOMDocument::schemaValidate() Generated Errors!'; 
    libxml_display_errors(); 
}

echo $xml->saveXML();
