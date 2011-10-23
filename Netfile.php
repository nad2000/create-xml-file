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
        'AboriginalLandResidenceIndicator',
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
		'TelephoneLocalNumber',
		'YukonFirstNationsSettlementNumber' );

	/**
	 * @var arrary(string) SDF element map of elemts wiht field type codes
	 */
	static private $_SDFElementMap = array(
		'ASAIPFD'	=>	'AgriStabilityAgriInvestProgramFarmingData',
		'ASAIPHD'	=>	'AgriStabilityAgriInvestProgramHarmonizedData',
		'BAD'	=>	'BusinessActivityData',
		'EED'	=>	'EmploymentExpenseData',
		'FFAD'	=>	'FarmingActivityData',
		'FAD'	=>	'FishingActivityData',
		'MLED'	=>	'MealLodgingExpenseData',
		'PAD'	=>	'ProfessionalActivityData',
		'RERD'	=>	'RealEstateRentalData'
	);
	
	/**
	 * @var arrary(string) SDF Valid Field Code
	 */
	static private $_validSDFcodes = array(
		'RealEstateRentalData'	=>	array(
			8141, 8230, 8299, 8521,8690, 8710, 8810, 8860, 8871, 8960, 9060, 9180, 9200, 9220, 9281, 9270, 9949, 
			9369, 9945, 9947, 9948, 9936, 9974, 9943, 9946,9925, 9926, 9927, 9928, 9923, 9924),
		'BusinessActivityData'	=>	array(
			8000, 8290, 8230, 8299, 8300, 8320, 8340, 8360, 8450, 8500, 8518, 8519, 8521, 8523, 8590, 8710, 8760, 
			8810, 8811, 8860, 8871, 8910, 8960, 9060, 9180, 9200, 9220, 9224, 9275, 9281, 9935, 9936, 9270, 9368, 
			9369, 9974,9943, 9945, 9946, 9931, 9932, 9933, 9925, 9927, 9923, 9924, 9926, 9928),
		'ProfessionalActivityData'	=>	array(
			8000, 8290, 8230, 8299, 8521, 8523, 8590, 8690, 8710, 8760, 8810, 8811, 8860, 8871, 8910, 8960, 9060, 
			9180, 9200, 9220, 9224, 9275, 9281, 9935, 9936, 9270, 9368, 9369, 9974, 9943, 9945, 9946, 9931, 9932, 
			9933, 9923, 9924, 9925, 9926, 9927, 9928),
		'FishingActivityData'	=>	array(
			9600, 8299, 9138, 9062, 9224, 9136, 8690, 8710, 8523, 8760, 9281, 8810, 9137, 8860, 9060, 8963, 9270, 
			9935, 9936, 9369, 9974, 9943, 9945, 9946, 9931, 9932, 9933, 9925, 9926, 9927, 9923, 9924, 9928),
		'FarmingActivityData'	=>	array(
			9370, 9371, 9372, 9373, 9374, 9375, 9376, 9377, 9378, 9420, 9421, 9422, 9423, 9424, 9425, 9426, 9470, 
			9471, 9472, 9473, 9474, 9476, 9477, 9520, 9541, 9542, 9540, 9570, 9601, 9604, 9605, 9600, 9659, 9661, 
			9662, 9663, 9664, 9711, 9712, 9713, 9760, 9764, 9795, 9796, 9797, 9798, 9799, 9802, 9803, 9804, 9805, 
			9808, 9809, 9810, 9811, 9814, 9819, 9820, 9937, 9938, 9790, 9935, 9936, 9898, 9899, 9941, 9942, 9974, 
			9943, 9945, 9946, 9923, 9924, 9925, 9926, 9927, 9928, 9929, 9930, 9931, 9932, 9933),
		'EmploymentExpenseData'	=>	array(
			8862, 8520, 9281, 8523, 9200, 8910, 8810, 9270, 1770, 9131, 1776, 1777, 9973, 9945, 9368),
		'MealLodgingExpenseData'	=>	array(8523, 8528, 9200)
	);

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
	 * Prints out ReturnLine element sequece
	 * @param array() $returnLines Return lines
	 * @param array() $validCodes  Valid codes
	 */
	private function createReturnLines( $returnLines, $validCodes = NULL)
	{
		foreach ( $returnLines as $line)
			if ( empty($validCodes) || in_array($line->code, $validCodes)) echo <<<EOS
<comm:ReturnLine>
	<comm:FieldCode>{$line->code}</comm:FieldCode>
	<comm:FieldValue>{$line->value}</comm:FieldValue>
</comm:ReturnLine>
EOS;
	}

	//function __construct() {
    //}

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

		$SDFRecordCount = 0;
		foreach( $data as $row ) {
			$lineId = $row->line_id;
			if (	$row->line_type == 'TRC_L'
					&& is_numeric($row->field_code)
					&& $row->field_code >= 100
					&& $row->field_code <= 8000)
			{
				$returnLines[] = (object) array( 
					'type'=>$row->line_type,
					'code'=> intval($row->field_code), 
					'value'=>$row->line_value );
				/*if ( array_key_exists($row->line_type, static::$_SDFElementMap))
						${lcfirst( static::$_SDFElementMap[$row->line_type])} = TRUE;
						$agriStabilityAgriInvestProgramFarmingData;
						$agriStabilityAgriInvestProgramHarmonizedData;
						$businessActivityData;
						$employmentExpenseData;
						$farmingActivityData;
						$fishingActivityData;
						$mealLodgingExpenseData;
						$professionalActivityData;
						$realEstateRentalData; */
			}
			elseif ( in_array ( $lineId, static::$_elementList ) )
				${lcfirst($lineId)} = $row->line_value;
			elseif ( in_array ( $lineId, static::$_SDFElementMap ) && $row->line_value == '1' ) {
				${lcfirst($lineId)} = TRUE;
				$SDFRecordCount++;
			}
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
			<comm:SoftwareID><?php echo self::SoftwareID;?></comm:SoftwareID>
			<comm:SoftwareRevisionDate><?php echo self::SoftwareRevisionDate;?></comm:SoftwareRevisionDate>
			<t1:TaxpayerData>
				<tp:TaxpayerBasicData>
					<tp:TaxpayerSocialInsuranceNumber><?php echo $taxpayerSocialInsuranceNumber;?></tp:TaxpayerSocialInsuranceNumber>
					<tp:TaxYear><?php echo $taxYear;?></tp:TaxYear>
					<tp:TaxpayerBirthDate><?php echo $taxpayerBirthDate;?></tp:TaxpayerBirthDate>
					<tp:TaxpayerMaritalStatusCode><?php echo $taxpayerMaritalStatusCode;?></tp:TaxpayerMaritalStatusCode>
					<tp:SelfEmployedSpouseDeadlineIndicator><?php echo $selfEmployedSpouseDeadlineIndicator;?></tp:SelfEmployedSpouseDeadlineIndicator>
				</tp:TaxpayerBasicData>
				<tp:TaxpayerResidenceData>
					<tp:TaxpayerYearEndResidenceProvinceCode><?php echo $taxpayerYearEndResidenceProvinceCode;?></tp:TaxpayerYearEndResidenceProvinceCode>
					<?php if ( !empty($taxpayerCurrentResidenceProvinceCode)): ?>
					<tp:TaxpayerCurrentResidenceProvinceCode><?php echo $taxpayerCurrentResidenceProvinceCode;?></tp:TaxpayerCurrentResidenceProvinceCode>
					<?php endif; ?>
					<?php if ( !empty($yukonFirstNationsSettlementNumber)): ?>
					<tp:YukonFirstNationsSettlementNumber><?php echo $yukonFirstNationsSettlementNumber;?></tp:YukonFirstNationsSettlementNumber>
					<?php endif; ?>
					<?php if ( !empty($aboriginalLandResidenceIndicator)): ?>
					<tp:AboriginalLandResidenceIndicator><?php echo $aboriginalLandResidenceIndicator;?></tp:AboriginalLandResidenceIndicator>
					<?php endif; ?>
				</tp:TaxpayerResidenceData>
				<tp:TaxpayerContactData>
					<comm:TaxpayerName>
						<comm:GivenName><?php echo $givenName;?></comm:GivenName>
						<comm:FamilyName><?php echo $familyName;?></comm:FamilyName>
					</comm:TaxpayerName>
					<tp:TaxpayerMailingAddress>
						<comm:CanadianMailingAddress>
							<comm:AddressStreetText><?php echo $addressStreetText;?></comm:AddressStreetText>
							<comm:AddressMunicipalityName><?php echo $addressMunicipalityName;?></comm:AddressMunicipalityName>
							<comm:AddressProvinceCode><?php echo $addressProvinceCode;?></comm:AddressProvinceCode>
							<comm:AddressPostalCode><?php echo $addressPostalCode;?></comm:AddressPostalCode>
						</comm:CanadianMailingAddress>
					</tp:TaxpayerMailingAddress>
					<tp:TaxpayerHomeTelephoneNumber>
						<comm:TelephoneAreaCode><?php echo $telephoneAreaCode;?></comm:TelephoneAreaCode>
						<comm:TelephoneLocalNumber><?php echo $telephoneLocalNumber;?></comm:TelephoneLocalNumber>
					</tp:TaxpayerHomeTelephoneNumber>
					<tp:AlternateAddressAuthorizationCode><?php echo $alternateAddressAuthorizationCode;?></tp:AlternateAddressAuthorizationCode>
					<tp:CorrespondenceLanguageCode><?php echo $correspondenceLanguageCode;?></tp:CorrespondenceLanguageCode>
				</tp:TaxpayerContactData>
			</t1:TaxpayerData>
			<t1:RelatedProgramData>
				<t1:CanadianCitizenshipIndicator><?php echo $canadianCitizenshipIndicator;?></t1:CanadianCitizenshipIndicator>
				<t1:ElectionsCanadaAuthorizationIndicator><?php echo $electionsCanadaAuthorizationIndicator;?></t1:ElectionsCanadaAuthorizationIndicator>
				<t1:GSTHSTCreditApplicationIndicator><?php echo $gSTHSTCreditApplicationIndicator;?></t1:GSTHSTCreditApplicationIndicator>
			</t1:RelatedProgramData>
			<?php $this->createReturnLines($returnLines); ?>
			<?php if ($SDFRecordCount > 0): ?>
			<t1:SelectedFinancialDataRecordCount><?php printf( "%02d", $SDFRecordCount);  ?></t1:SelectedFinancialDataRecordCount>
			<?php if (!empty($realEstateRentalData)): ?>
			<sfd:SelectedFinancialData>
				<sfd:RealEstateRentalData>
					<sfd:BusinessNumber>123456789RT1234</sfd:BusinessNumber>
					<sfd:NAICSCode>531111</sfd:NAICSCode>
					<sfd:FiscalPeriodStartDate>2011-01-01</sfd:FiscalPeriodStartDate>
					<sfd:FiscalPeriodEndDate>2011-12-31</sfd:FiscalPeriodEndDate>
					<sfd:PartnershipNumber>123456789</sfd:PartnershipNumber>
					<sfd:BusinessOwnershipPercent>8450</sfd:BusinessOwnershipPercent>
					<sfd:FinalBusinessYearIndicator>1</sfd:FinalBusinessYearIndicator>
					<sfd:PartnerCoOwnerData>
						<sfd:PartnerName>
							<comm:GivenName>John</comm:GivenName>
							<comm:FamilyName>Doe</comm:FamilyName>
						</sfd:PartnerName>
						<sfd:PartnerPIN>0</sfd:PartnerPIN>
						<sfd:PartnerBusinessOwnershipPercent>155000</sfd:PartnerBusinessOwnershipPercent>
					</sfd:PartnerCoOwnerData>
					<?php $this->createReturnLines($returnLines, static::$_validSDFcodes['RealEstateRentalData']); ?>
				</sfd:RealEstateRentalData>
			</sfd:SelectedFinancialData>
			<?php endif; ?>
			<?php if (!empty($businessActivityData)): ?>
			<sfd:SelectedFinancialData>
				<sfd:BusinessActivityData>
					<sfd:BusinessNumber>334455667AA8990</sfd:BusinessNumber>
					<sfd:NAICSCode>123456</sfd:NAICSCode>
					<sfd:BusinessName>HAPPY TAXES LTD.</sfd:BusinessName>
					<sfd:PartnershipNumber>123456789</sfd:PartnershipNumber>
					<sfd:TaxShelterNumber>AA123456</sfd:TaxShelterNumber>
					<sfd:BusinessAddress>
						<comm:BusinessAddressStreetText>12 GREEN ST.</comm:BusinessAddressStreetText>
						<comm:AddressPostalCode>K1B2F9</comm:AddressPostalCode>
					</sfd:BusinessAddress>
					<sfd:FiscalPeriodStartDate>2011-01-01</sfd:FiscalPeriodStartDate>
					<sfd:FiscalPeriodEndDate>2011-12-31</sfd:FiscalPeriodEndDate>
					<sfd:BusinessOwnershipPercent>8500</sfd:BusinessOwnershipPercent>
					<sfd:FinalBusinessYearIndicator>1</sfd:FinalBusinessYearIndicator>
					<sfd:PartnerCoOwnerData>
						<sfd:PartnerName>
							<comm:GivenName>MAC</comm:GivenName>
							<comm:FamilyName>BLACK</comm:FamilyName>
						</sfd:PartnerName>
						<sfd:PartnerPIN>00000000</sfd:PartnerPIN>
						<sfd:PartnerBusinessOwnershipPercent>150000</sfd:PartnerBusinessOwnershipPercent>
					</sfd:PartnerCoOwnerData>
					<?php $this->createReturnLines($returnLines, static::$_validSDFcodes['BusinessActivityData']); ?>
				</sfd:BusinessActivityData>
			</sfd:SelectedFinancialData>
			<?php endif; ?>
			<?php if (!empty($professionalActivityData)): ?>
			<sfd:SelectedFinancialData>
				<sfd:ProfessionalActivityData>
					<sfd:BusinessNumber>123456789AA2345</sfd:BusinessNumber>
					<sfd:NAICSCode>541110</sfd:NAICSCode>
					<sfd:BusinessName>James Publishing</sfd:BusinessName>
					<sfd:PartnershipNumber>123456789</sfd:PartnershipNumber>
					<sfd:TaxShelterNumber>AB123456</sfd:TaxShelterNumber>
					<sfd:BusinessAddress>
						<comm:BusinessAddressStreetText>20 Spicer Street</comm:BusinessAddressStreetText>
						<comm:AddressPostalCode>R2Y1C4</comm:AddressPostalCode>
					</sfd:BusinessAddress>
					<sfd:FiscalPeriodStartDate>2011-01-01</sfd:FiscalPeriodStartDate>
					<sfd:FiscalPeriodEndDate>2011-12-31</sfd:FiscalPeriodEndDate>
					<sfd:BusinessOwnershipPercent>0000</sfd:BusinessOwnershipPercent>
					<sfd:FinalBusinessYearIndicator>2</sfd:FinalBusinessYearIndicator>
					<sfd:PartnerCoOwnerData>
						<sfd:PartnerName>
							<comm:GivenName></comm:GivenName>
							<comm:FamilyName></comm:FamilyName>
						</sfd:PartnerName>
						<sfd:PartnerPIN>00000000</sfd:PartnerPIN>
						<sfd:PartnerBusinessOwnershipPercent>0</sfd:PartnerBusinessOwnershipPercent>
					</sfd:PartnerCoOwnerData>
					<?php $this->createReturnLines($returnLines, static::$_validSDFcodes['ProfessionalActivityData']); ?>
				</sfd:ProfessionalActivityData>	
			</sfd:SelectedFinancialData>
			<?php endif; ?>
			<?php if (!empty($fishingActivityData)): ?>
			<sfd:SelectedFinancialData>
				<sfd:FishingActivityData>
					<sfd:BusinessNumber>123456789AA2345</sfd:BusinessNumber>
					<sfd:NAICSCode>114133</sfd:NAICSCode>
					<sfd:BoatName>Colin's Fishery</sfd:BoatName>
					<sfd:PartnershipNumber>123456789</sfd:PartnershipNumber>
					<sfd:TaxShelterNumber>AA123456</sfd:TaxShelterNumber>
					<sfd:FiscalPeriodStartDate>2011-01-01</sfd:FiscalPeriodStartDate>
					<sfd:FiscalPeriodEndDate>2011-12-31</sfd:FiscalPeriodEndDate>
					<sfd:BusinessOwnershipPercent>0000</sfd:BusinessOwnershipPercent>
					<sfd:FinalBusinessYearIndicator>1</sfd:FinalBusinessYearIndicator>
					<sfd:PartnerCoOwnerData>
						<sfd:PartnerName>
							<comm:GivenName></comm:GivenName>
							<comm:FamilyName></comm:FamilyName>
						</sfd:PartnerName>
						<sfd:PartnerPIN>00000000</sfd:PartnerPIN>
						<sfd:PartnerBusinessOwnershipPercent>0</sfd:PartnerBusinessOwnershipPercent>
					</sfd:PartnerCoOwnerData>
					<?php $this->createReturnLines($returnLines, static::$_validSDFcodes['FishingActivityData']); ?>
				</sfd:FishingActivityData>
			</sfd:SelectedFinancialData>
			<?php endif; ?>
			<?php if (!empty($employmentExpenseData)): ?>
			<sfd:SelectedFinancialData>
				<sfd:EmploymentExpenseData>
					<?php $this->createReturnLines($returnLines, static::$_validSDFcodes['EmploymentExpenseData']); ?>
				</sfd:EmploymentExpenseData>
			</sfd:SelectedFinancialData>
			<?php endif; ?>
			<?php if (!empty($mealLodgingExpenseData)): ?>
			<sfd:SelectedFinancialData>
				<sfd:MealLodgingExpenseData>
					<?php $this->createReturnLines($returnLines, static::$_validSDFcodes['MealLodgingExpenseData']); ?>
				</sfd:MealLodgingExpenseData>
			</sfd:SelectedFinancialData>
			<?php endif; ?>
			<?php endif; // $SDFRecordCount > 0  ?>
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
