<?php

// Enable user error handling:
libxml_use_internal_errors(true);
// Enable XML/XSD includes:
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

class Netfile{

	/**
	 * Software ID (should match the ID specified by the schema)
	 * @var string
	 */
	const SoftwareID='061G';
	/**
	 * Software Revision Date (should match the ID specified by the schema)
	 * @var string
	 */
	const SoftwareRevisionDate='2011-08-06';

	/**
	 * @var arrary(string) Element list of elemts wiht values
	 */
	static private $_elementList = array(
		'AddressMunicipalityName',
		'AddressPostalCode',
		'AddressProvinceCode',
		'AddressStreetText',
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
		'TelephoneAreaCode',
		'TelephoneLocalNumber' );

	/**
	 * @var boolean XML vaidation indicato
	 */
	private $_isValid = TRUE;

	/**
	 * @var array() Validation error messages
	 */
	private $_errors = NULL;

	/** @var DOMDocument XML DOM representation */
	private $_xml = NULL;

	/**
	 * @return DOMDocument Validated XML document
	 */
	public function getDoc() {
		return $this->_xml;
	}

	/**
	 * @return string serialized XML document
	 */
	public function getXML() {
		return $this->_xml->saveXML();
	}

	/**
	 * @param int $userId User ID
	 * @param int $taxYear Tax Year
	 * @return DOMDocument Validated XML document
	 */
	public function createT1NetfileDoc(
		$userId = 2,
		$taxYear = 2010) {

		// default element values: ''
		foreach ( static::$_elementList as $elment) {
			${lcfirst($elment)} = '';
		}

		// Uncomment, if you need to set other default vlues
		//$addressPostalCode='';
		//$addressProvinceCode='';
		//$alternateAddressAuthorizationCode='';
		//$boatName='';
		//$businessAddressStreetText='';
		//$businessName='';
		//$businessOwnershipPercent='';
		//$canadianCitizenshipIndicator='';
		//$correspondenceLanguageCode='';
		//$electionsCanadaAuthorizationIndicator='';
		//$familyName='';
		//$finalBusinessYearIndicator='';
		//$fiscalPeriodEndDate='';
		//$fiscalPeriodStartDate='';
		//$givenName='';
		//$gSTHSTCreditApplicationIndicator='';
		//$nAICSCode='';
		//$partnerBusinessOwnershipPercent='';
		//$partnerPIN='';
		//$partnershipNumber='';
		//$selectedFinancialDataRecordCount='';
		//$selfEmployedSpouseDeadlineIndicator='';
		//$taxpayerBirthDate='';
		//$taxpayerCurrentResidenceProvinceCode='';
		//$taxpayerMaritalStatusCode='';
		//$taxpayerSocialInsuranceNumber='';
		//$taxpayerYearEndResidenceProvinceCode='';
		//$taxShelterNumber='';
		//$taxYear='';
		//$telephoneAreaCode='';
		//$telephoneLocalNumber='';

		$data = DataSource::getData( $userId = $userId, $taxYear = $taxYear);

		// Return Lines
		$returnLines = array();

		foreach( $data as $row ) {
			$lineId = $row->line_id;
			if (	$row->line_type == 'TRC_L'
					&& $row->field_code >= 100
					&& $row->field_code <= 8000)
				$returnLines[] = (object) array( 'code'=>$row->field_code, 'value'=>$row->line_value);
			elseif ( in_array ( $lineId, static::$_elementList ) )
				${lcfirst($lineId)} = $row->line_value;
		}

		// generating teh document:
		ob_start();
		echo "<?xml version=\"1.0\"?>\n";

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
			<comm:SoftwareID><?=self::SoftwareID?></comm:SoftwareID>
			<comm:SoftwareRevisionDate><?=self::SoftwareRevisionDate?></comm:SoftwareRevisionDate>
			<t1:TaxpayerData>
				<tp:TaxpayerBasicData>
					<tp:TaxpayerSocialInsuranceNumber><?=$taxpayerSocialInsuranceNumber;?></tp:TaxpayerSocialInsuranceNumber>
					<tp:TaxYear><?=$taxYear?></tp:TaxYear>
					<tp:TaxpayerBirthDate><?=$taxpayerBirthDate?></tp:TaxpayerBirthDate>
					<tp:TaxpayerMaritalStatusCode><?=$taxpayerMaritalStatusCode?></tp:TaxpayerMaritalStatusCode>
					<tp:SelfEmployedSpouseDeadlineIndicator><?=$selfEmployedSpouseDeadlineIndicator?></tp:SelfEmployedSpouseDeadlineIndicator>
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

		$this->_xml = new DOMDocument();
		$this->_xml->loadXML($xmlStr);
		$this->_xml->xinclude();

		$this->_isValid = $this->_xml->schemaValidate( dirname(__FILE__) . '/xsd/netfile.xsd' );
		$this->_errors = $this->_isValid ? NULL : libxml_get_errors();
		libxml_clear_errors();
		return $this->_xml;

	}

	/**
	 * Property getter
	 * @param string $variable
	 * @return mixed
	 */
	public function __get($variable)
	{
		if ($variable == 'isValid') return $this->_isValid;
		else if ($variable == 'errors') return $this->_errors;
		else if ($variable == 'XML') return $this->getXML();
		else if ($variable == 'Doc') return $this->getDoc();
		return $this->{$variable};
	}
}