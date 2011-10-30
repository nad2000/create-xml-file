<?php

// Enable user error handling:
libxml_use_internal_errors(true);
// Enable XML/XSD includes:
libxml_disable_entity_loader( false);

require_once 'DataSource.php';

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
	 * @var arrary(string) SFD element map of elemts wiht field type codes
	 */
	static private $_SFDElementMap = array(
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
	 * @var arrary(string) SFD Valid Field Code
	 */
	static private $_validSFDcodes = array(
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

	/** @var string Raw document representation rendered using template */
	private $_raw = NULL;

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
		foreach ( $returnLines as $line) {
			if ( ( empty($validCodes) && $line->code >= 100 && $line->code <= 8000 )
					|| ( isset ($validCodes) && in_array($line->code, $validCodes) )
				) 
			{
				echo <<<EOS
<comm:ReturnLine>
	<comm:FieldCode>{$line->code}</comm:FieldCode>
	<comm:FieldValue>{$line->value}</comm:FieldValue>
</comm:ReturnLine>
EOS;
			}
		}
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

		foreach( $data as $row ) {
			$lineId = $row->line_id;
			if (	$row->line_type == 'TRC_L'
					&& is_numeric($row->field_code)
					&& $row->field_code >=   100
					/* && $row->field_code <= 8000 */ )
			{
				$returnLines[] = (object) array(
					'type'=>$row->line_type,
					'code'=> intval($row->field_code),
					'value'=>$row->line_value );
				/*if ( array_key_exists($row->line_type, static::$_SFDElementMap))
						${lcfirst( static::$_SFDElementMap[$row->line_type])} = TRUE;
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
		}

		$sfdData = DataSource::getSFDData( $userId = $userId, $taxYear = $taxYear);
		$SFDRecordCount = count( $sfdData);
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
	xmlns:tp="http://www.cra-arc.gc.ca/xmlns/tp/2012/1-0-0"
	xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"  
	xsi:schemaLocation="http://www.cra-arc.gc.ca/xmlns/netfile/2012/1-0-0 xsd/netfile.xsd">
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
					<?php $this->optElem("tp:TaxpayerCurrentResidenceProvinceCode", $taxpayerCurrentResidenceProvinceCode); ?>
					<?php $this->optElem("tp:YukonFirstNationsSettlementNumber", $yukonFirstNationsSettlementNumber); ?>
					<?php $this->optElem("tp:AboriginalLandResidenceIndicator", $aboriginalLandResidenceIndicator); ?>
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
			<?php if ($SFDRecordCount > 0): ?>
			<t1:SelectedFinancialDataRecordCount><?php printf( "%02d", $SFDRecordCount);  ?></t1:SelectedFinancialDataRecordCount>
			<?php foreach ($sfdData as $sfdRow): ?>
				<sfd:SelectedFinancialData>
					<?php $this->createSFDElement($sfdRow, $returnLines); ?>
				</sfd:SelectedFinancialData>
			<?php endforeach; ?>
			<?php endif; // $SFDRecordCount > 0  ?>
			<sfd:SelectedFinancialData>
				<sfd:EmploymentExpenseData>
					<?php $this->createReturnLines($returnLines, static::$_validSFDcodes['EmploymentExpenseData']); ?>
				</sfd:EmploymentExpenseData>
			</sfd:SelectedFinancialData>
			<sfd:SelectedFinancialData>
				<sfd:MealLodgingExpenseData>
					<?php $this->createReturnLines($returnLines, static::$_validSFDcodes['MealLodgingExpenseData']); ?>
				</sfd:MealLodgingExpenseData>
			</sfd:SelectedFinancialData>
		</t1:T1Return>
	</net:T1Netfile>
</net:Netfile>
<?php
		$xmlStr=ob_get_contents();
		ob_end_clean();

		$this->_raw = $xmlStr;
		$this->_xml = new DOMDocument();
		$this->_xml->loadXML($xmlStr);
		$this->_xml->xinclude();

		$this->_isValid = $this->_xml->schemaValidate( dirname(__FILE__) . '/xsd/netfile.xsd' );
		$this->_errors = $this->_isValid ? NULL : libxml_get_errors();
		libxml_clear_errors();
		return $this->_xml;

	}

	
	/**
	 * Renders an optional elemnent if the value is present (not empty)
	 * @param string $tag
	 * @param string $value
	 */
	private function optElem( $tag, $value ) {
		if (!empty($value)) echo "<".$tag.">".$value."</".$tag.">";
	}

	/**
	 *
	 * @param PDO row $row tabel row stdObject
	 */
	private function xmlEncodeRow( &$row) {
		
		foreach($row as $key => $value) {
			if ( !empty($value) && is_string($value) ) {
				$value = htmlentities($value);
			}
		}		
	}
	
	/**
	 * Renders SFD record
	 * @param object  $sfdRow SFD entry DB row
	 * @param array() $returnLines Return lines
	 */
	private function createSFDElement( $sfdRow, $returnLines)
	{
		$this->xmlEncodeRow($sfdRow);
		if ( $sfdRow->Data_Type=='REAL' ): ?>
			<sfd:RealEstateRentalData>
				<?php $this->optElem("sfd:BusinessNumber", $sfdRow->BusinessNumber); ?>
				<sfd:NAICSCode><?php echo $sfdRow->NAICSCODE;?></sfd:NAICSCode>
				<sfd:FiscalPeriodStartDate><?php echo $sfdRow->FiscalPeriodStartDate;?></sfd:FiscalPeriodStartDate>
				<sfd:FiscalPeriodEndDate><?php echo $sfdRow->FiscalPeriodeEndDate;?></sfd:FiscalPeriodEndDate>
				<?php $this->optElem("sfd:PartnershipNumber", $sfdRow->PartnershipNumber); ?>
				<?php $this->optElem("sfd:TaxShelterNumber", $sfdRow->TaxShelterNumber); ?>
				<?php $this->optElem("sfd:BusinessOwnershipPercent", $sfdRow->BusinessOwnershipPercent); ?>
				<?php $this->optElem("sfd:FinalBusinessYearIndicator", $sfdRow->FinalBusinessYearIndicator); ?>
				<?php $this->createPartnerCoOwnerDataElement( $sfdRow); ?>
				<?php $this->createReturnLines($returnLines, static::$_validSFDcodes['RealEstateRentalData']); ?>
			</sfd:RealEstateRentalData>
		<?php endif;
		if ($sfdRow->Data_Type=='BUSINESS'): ?>
			<sfd:BusinessActivityData>
				<?php $this->optElem("sfd:BusinessNumber", $sfdRow->BusinessNumber); ?>
				<sfd:NAICSCode><?php echo $sfdRow->NAICSCODE;?></sfd:NAICSCode>
				<sfd:BusinessName><?php echo htmlentities($sfdRow->BusinessName);?></sfd:BusinessName>
				<?php $this->optElem("sfd:PartnershipNumber", $sfdRow->PartnershipNumber); ?>
				<?php $this->optElem("sfd:TaxShelterNumber", $sfdRow->TaxShelterNumber); ?>
				<?php $this->createBusinessAddressElement($sfdRow); ?>
				<sfd:FiscalPeriodStartDate><?php echo $sfdRow->FiscalPeriodStartDate;?></sfd:FiscalPeriodStartDate>
				<sfd:FiscalPeriodEndDate><?php echo $sfdRow->FiscalPeriodeEndDate;?></sfd:FiscalPeriodEndDate>
				<?php $this->optElem("sfd:BusinessOwnershipPercent", $sfdRow->BusinessOwnershipPercent); ?>
				<?php $this->optElem("sfd:FinalBusinessYearIndicator", $sfdRow->FinalBusinessYearIndicator); ?>
				<?php $this->createPartnerCoOwnerDataElement( $sfdRow); ?>
				<?php $this->createReturnLines($returnLines, static::$_validSFDcodes['BusinessActivityData']); ?>
			</sfd:BusinessActivityData>
		<?php endif;
		if ($sfdRow->Data_Type=='PROFESSIONAL'): ?>
			<sfd:ProfessionalActivityData>
				<?php $this->optElem("sfd:BusinessNumber", $sfdRow->BusinessNumber); ?>
				<sfd:NAICSCode><?php echo $sfdRow->NAICSCODE; ?></sfd:NAICSCode>
				<sfd:BusinessName><?php echo htmlentities($sfdRow->BusinessName); ?></sfd:BusinessName>
				<?php $this->optElem("sfd:PartnershipNumber", $sfdRow->PartnershipNumber); ?>
				<?php $this->optElem("sfd:TaxShelterNumber", $sfdRow->TaxShelterNumber); ?>
				<?php $this->createBusinessAddressElement($sfdRow); ?>
				<sfd:FiscalPeriodStartDate><?php echo $sfdRow->FiscalPeriodStartDate;?></sfd:FiscalPeriodStartDate>
				<sfd:FiscalPeriodEndDate><?php echo $sfdRow->FiscalPeriodeEndDate;?></sfd:FiscalPeriodEndDate>
				<?php $this->optElem("sfd:BusinessOwnershipPercent", $sfdRow->BusinessOwnershipPercent); ?>
				<?php $this->optElem("sfd:FinalBusinessYearIndicator", $sfdRow->FinalBusinessYearIndicator); ?>
				<?php $this->createPartnerCoOwnerDataElement( $sfdRow); ?>
				<?php $this->createReturnLines($returnLines, static::$_validSFDcodes['ProfessionalActivityData']); ?>
			</sfd:ProfessionalActivityData>
		<?php endif;
		if ($sfdRow->Data_Type=='FISHING'): ?>
			<sfd:FishingActivityData>
				<?php $this->optElem("sfd:BusinessNumber", $sfdRow->BusinessNumber); ?>
				<sfd:NAICSCode><?php echo $sfdRow->NAICSCODE; ?></sfd:NAICSCode>
				<sfd:BoatName><?php echo htmlentities($sfdRow->BusinessName); ?></sfd:BoatName>
				<?php $this->optElem("sfd:PartnershipNumber", $sfdRow->PartnershipNumber); ?>
				<?php $this->optElem("sfd:TaxShelterNumber", $sfdRow->TaxShelterNumber); ?>
				<sfd:FiscalPeriodStartDate><?php echo $sfdRow->FiscalPeriodStartDate;?></sfd:FiscalPeriodStartDate>
				<sfd:FiscalPeriodEndDate><?php echo $sfdRow->FiscalPeriodeEndDate;?></sfd:FiscalPeriodEndDate>
				<?php $this->optElem("sfd:BusinessOwnershipPercent", $sfdRow->BusinessOwnershipPercent); ?>
				<?php $this->optElem("sfd:FinalBusinessYearIndicator", $sfdRow->FinalBusinessYearIndicator); ?>
				<?php $this->createPartnerCoOwnerDataElement( $sfdRow); ?>
				<?php $this->createReturnLines($returnLines, static::$_validSFDcodes['FishingActivityData']); ?>
			</sfd:FishingActivityData>
		<?php endif;
	}

	/**
	 * Renders PartnerCoOwnerData emlement
	 * @param object  $sfdRow SFD entry DB row with linked partner data
	 */
	private function createPartnerCoOwnerDataElement( $sfdRow )
	{
		if( isset($sfdRow->partners) ) foreach ( $sfdRow->partners as $p) { ?>
				<sfd:PartnerCoOwnerData>
					<sfd:PartnerName>
						<comm:GivenName><?php echo $p->GivenName;?></comm:GivenName>
						<comm:FamilyName><?php echo $p->FamilyName;?></comm:FamilyName>
					</sfd:PartnerName>
					<sfd:PartnerPIN><?php echo $p->PIN;?></sfd:PartnerPIN>
					<sfd:PartnerBusinessOwnershipPercent><?php echo $p->OwnershipPercent;?></sfd:PartnerBusinessOwnershipPercent>
				</sfd:PartnerCoOwnerData><?php
		}
	}

	/**
	 * Renders BusinessAddress emlement
	 * @param object $sfdRow SFD entry DB row with linked partner data
	 */
	private function createBusinessAddressElement( $sfdRow )
	{
		if( !empty($sfdRow->BusinessAddressStreetText) || !empty($sfdRow->AddressPostalCode) ) { ?>
				<sfd:BusinessAddress>
					<comm:BusinessAddressStreetText><?php echo $sfdRow->BusinessAddressStreetText; ?></comm:BusinessAddressStreetText>
					<comm:AddressPostalCode><?php echo $sfdRow->AddressPostalCode; ?></comm:AddressPostalCode>
				</sfd:BusinessAddress><?php
		}
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
		else if ($variable == 'Raw') return $this->_raw;
		return $this->{$variable};
	}
}
