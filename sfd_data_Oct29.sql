SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

/*
alter table sfd_data add column AccountingMethodCode char(1) null;
alter table sfd_data add column FarmAddress boolean null;
ALTER TABLE sfd_data CHANGE COLUMN Data_Type Data_Type ENUM('BUSINESS','REAL','PROFESSIONAL','FISHING','FARMING') NOT NULL  ;
*/
-- Database: taxtool

TRUNCATE TABLE sfd_data;

REPLACE INTO sfd_data
(id, Data_Type, user_id, tax_year, BusinessNumber, NAICSCODE, BusinessName, PartnershipNumber, 
TaxShelterNumber, BusinessAddressStreetText, AddressPostalCode, FiscalPeriodStartDate, 
FiscalPeriodeEndDate, BusinessOwnershipPercent, FinalBusinessYearIndicator, 
date_created, date_updated, AccountingMethodCode, FarmAddress)
VALUES
-- (5, 'REAL', 6, 2010, NULL, 531111, 'Real Mandatory Valid', NULL, NULL, NULL, NULL, '2011-01-01', '2011-12-31', NULL, NULL, '2011-10-28 22:30:57', '2011-10-28 22:30:57', NULL, NULL),
-- (6, 'REAL', 6, 2010, '123456789012345', 531111, 'Real Valid All Optional', 123456789, 'AB123456', NULL, NULL, '2010-11-01', '2011-10-31', 6000, '2', '2011-10-28 22:34:52', '2011-10-28 22:34:52', NULL, NULL),
-- (7, 'BUSINESS', 6, 2010, NULL, 311211, 'Floor milling mandatory', NULL, NULL, '250 Mortgage street', 'm2n6l7', '2010-11-01', '2011-10-31', NULL, NULL, '2011-10-28 22:49:32', '2011-10-28 22:49:32', NULL, NULL),
(8, 'BUSINESS', 6, 2010, '543210987654321', 238320, 'Painting & wall all optional', 987654321, 'BB654321', '456 PAINTING STREET', 'P4N5N6', '2011-01-01', '2011-12-31', 3333, '2', '2011-10-28 22:49:32', '2011-10-28 22:49:32', NULL, NULL),
-- (10, 'PROFESSIONAL', 6, 2010, NULL, 622120, 'DENTIST mandatory', NULL, NULL, '250 Mortgage street', 'm2n6l7', '2010-11-01', '2011-10-31', NULL, NULL, '2011-10-28 23:04:06', NULL, NULL, NULL),
(11, 'PROFESSIONAL', 6, 2010, '543210987654321', 621330, 'Painting & wall all optional', 987654321, 'BB654321', '456 PAINTING STREET', 'P4N5N6', '2011-01-01', '2011-12-31', 3333, '2', '2011-10-28 23:04:06', NULL, NULL, NULL),
-- (12, 'FISHING', 6, 2010, NULL, 114113, 'FISHING mandatory', NULL, NULL, '250 Mortgage street', 'm2n6l7', '2010-11-01', '2011-10-31', NULL, NULL, '2011-10-28 23:04:06', NULL, NULL, NULL),
-- (13, 'FISHING', 6, 2010, '543210987654321', 114114, 'FISHING all optional', 987654321, 'BB654321', '456 PAINTING STREET', 'P4N5N6', '2011-01-01', '2011-12-31', 3333, '2', '2011-10-28 23:04:06', NULL, NULL, NULL),
(14, 'FARMING', 6, 2010, NULL, 111140, 'FARMING mandatory', NULL, NULL, '250 Mortgage street', 'm2n6l7', '2010-11-01', '2011-10-31', NULL, NULL, '2011-10-28 23:04:06', NULL, 1, NULL),
(15, 'FARMING', 6, 2010, '543210987654321', 111110, 'FARMING all optional', 987654321, 'BB654321', '456 PAINTING STREET', 'P4N5N6', '2011-01-01', '2011-12-31', 3333, '2', '2011-10-28 23:04:06', NULL, 2, NULL);
