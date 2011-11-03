SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

TRUNCATE TABLE partner;

REPLACE INTO partner (SFD_ID, Partner_ID, GivenName, FamilyName, PIN, OwnershipPercent)
VALUES
(1, 1, 'John', 'Doe', 0, 155000),
(1, 2, 'Traver', 'Smith', 1234, 105000),
(2, 3, 'MAC', 'BLACK', 0, 150000),
(2, 4, 'John', 'Spencer', 0, 100000),
(0, 5, NULL, '', 0, 0),
(3, 6, NULL, '', 0, 0),
(6, 7, 'Hiralal', 'Larson & Turbo', 12345678, 50000),
(6, 8, NULL, 'Partner valid mandatory only', 87654321, 750000),
(8, 9, NULL, 'Business partner mandatory', 87654321, 50000),
(8, 10, 'painting', 'Business Partner optional', 23456789, 750000),
(11, 11, NULL, 'Dentist mandatory', 0, 750000),
(11, 12, 'OPTIONAL NAME', 'Dentist all optional', 0, 62500),
(11, 13, NULL, 'Dentist mandatory', 12345678, 750000),
(11, 14, 'OPTIONAL NAME', 'Dentist all optional', 87654321, 62500),
(13, 15, NULL, 'fISHING mandatory', 12345678, 750000),
(13, 16, 'OPTIONAL NAME', 'Fishing all optional', 87654321, 62500),
(15, 17, NULL, 'farming partner mandatory', 23456789, 73547),
(15, 18, 'RObert', 'Farming optional', 98765432, 89765);
