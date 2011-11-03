SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

TRUNCATE TABLE sfd_data;

REPLACE INTO sfd_data (id, Data_Type, user_id, tax_year, BusinessNumber, NAICSCODE, BusinessName, PartnershipNumber, TaxShelterNumber, BusinessAddressStreetText, AddressPostalCode, FiscalPeriodStartDate, FiscalPeriodeEndDate, BusinessOwnershipPercent, FinalBusinessYearIndicator, date_created, date_updated, AccountingMethodCode, FarmAddress) VALUES
(5, 'REAL', 6, 2010, NULL, 531111, 'Real Mandatory Valid', NULL, NULL, NULL, NULL, '2011-01-01', '2011-12-31', NULL, NULL, '2011-10-28 22:30:57', '2011-10-28 22:30:57', NULL, NULL),
(6, 'REAL', 6, 2010, '123456789012345', 531112, 'Real valid and all optional', 123456789, 'AB123456', NULL, NULL, '2010-11-01', '2011-10-31', 6000, '2', '2011-10-28 22:34:52', '2011-10-28 22:34:52', NULL, NULL);
