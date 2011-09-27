<?php

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

// to run from CLI: php -f Netfile.php | more
echo '<?xml version="1.0"?>';
$SoftwareID='061G';
$SoftwareRevisionDate='2011-08-06';

// Tax reuturns data - should be supplied from DB
// Taxpayer data:
$taxYear = 2010;
//...

$data = DataSource::getData( $userId = 2, $taxYear = $taxYear);

// ReturnLines
$returnLines = array();

foreach( $data as $row ) {
	$lineId = $row->line_id;
	if (	$row->line_type == 'TRC_L' 
			&& $row->field_code >= 100
			&& $row->field_code <= 8000)
		$returnLines[] = (object) array( 'code'=>$row->field_code, 'value'=>$row->line_value);
	elseif ( in_array ( $lineId, array( 'TaxpayerSocialInsuranceNumber', 'GivenName', 'FamilyName' )) )
		${lcfirst($lineId)} = $row->line_value;
}

?>
<net:Netfile 
	xmlns:comm="http://www.cra-arc.gc.ca/xmlns/comm/2012/1-0-0" 
	xmlns:net="http://www.cra-arc.gc.ca/xmlns/netfile/2012/1-0-0" 
	xmlns:sdt="http://www.cra-arc.gc.ca/xmlns/sdt/2-0-0" 
	xmlns:sfd="http://www.cra-arc.gc.ca/xmlns/sfd/2012/1-0-0" 
	xmlns:t1="http://www.cra-arc.gc.ca/xmlns/t1/2012/1-0-0" 
	xmlns:tp="http://www.cra-arc.gc.ca/xmlns/tp/2012/1-0-0" 
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
	xsi:schemaLocation="http://www.cra-arc.gc.ca/xmlns/netfile/2012/1-0-0 netfile.xsd">
	<net:T1Netfile>
		<t1:T1Return>
			<comm:SoftwareID><?=$SoftwareID?></comm:SoftwareID>
			<comm:SoftwareRevisionDate><?=$SoftwareRevisionDate?></comm:SoftwareRevisionDate>
			<t1:TaxpayerData>
				<tp:TaxpayerBasicData>
					<tp:TaxpayerSocialInsuranceNumber><?=$taxpayerSocialInsuranceNumber;?></tp:TaxpayerSocialInsuranceNumber>
					<tp:TaxYear><?=$taxYear?></tp:TaxYear>
					<tp:TaxpayerBirthDate>1966-07-12</tp:TaxpayerBirthDate>
					<tp:TaxpayerMaritalStatusCode>6</tp:TaxpayerMaritalStatusCode>
					<tp:SelfEmployedSpouseDeadlineIndicator>0</tp:SelfEmployedSpouseDeadlineIndicator>
				</tp:TaxpayerBasicData>
				<tp:TaxpayerResidenceData>
					<tp:TaxpayerYearEndResidenceProvinceCode>NS</tp:TaxpayerYearEndResidenceProvinceCode>
					<tp:TaxpayerCurrentResidenceProvinceCode>NS</tp:TaxpayerCurrentResidenceProvinceCode>
				</tp:TaxpayerResidenceData>
				<tp:TaxpayerContactData>
					<comm:TaxpayerName>
						<comm:GivenName><?=$givenName;?></comm:GivenName>
						<comm:FamilyName><?=$familyName;?></comm:FamilyName>
					</comm:TaxpayerName>
					<tp:TaxpayerMailingAddress>
						<comm:CanadianMailingAddress>
							<comm:AddressStreetText></comm:AddressStreetText>
							<comm:AddressMunicipalityName></comm:AddressMunicipalityName>
							<comm:AddressProvinceCode>NS</comm:AddressProvinceCode>
							<comm:AddressPostalCode>B0W3T0</comm:AddressPostalCode>
						</comm:CanadianMailingAddress>
					</tp:TaxpayerMailingAddress>
					<tp:TaxpayerHomeTelephoneNumber>
						<comm:TelephoneAreaCode>613</comm:TelephoneAreaCode>
						<comm:TelephoneLocalNumber>5551234</comm:TelephoneLocalNumber>
					</tp:TaxpayerHomeTelephoneNumber>
					<tp:AlternateAddressAuthorizationCode>00</tp:AlternateAddressAuthorizationCode>
					<tp:CorrespondenceLanguageCode>2</tp:CorrespondenceLanguageCode>
				</tp:TaxpayerContactData>
			</t1:TaxpayerData>
			<t1:RelatedProgramData>
				<t1:CanadianCitizenshipIndicator>1</t1:CanadianCitizenshipIndicator>
				<t1:ElectionsCanadaAuthorizationIndicator>02</t1:ElectionsCanadaAuthorizationIndicator>
				<t1:GSTHSTCreditApplicationIndicator>1</t1:GSTHSTCreditApplicationIndicator>
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