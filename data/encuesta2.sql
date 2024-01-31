DROP DATABASE IF EXISTS `encuesta2`;

CREATE DATABASE `encuesta2`;

use `encuesta2`;

CREATE TABLE `User` (
  `user_id` integer PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `tlfn` bigint NOT NULL,
  `country_id` int NOT NULL,
  `city` varchar(255) NOT NULL,
  `postal_code` int NOT NULL,
  `email_token` varchar(255),
  `terms_of_use` BOOLEAN NOT NULL
);

CREATE TABLE `Survey` (
  `survey_id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `user_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `state` varchar(255) NOT NULL,
  `creation` datetime NOT NULL
);

CREATE TABLE `Question` (
  `question_id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `questionText` varchar(255) NOT NULL,
  `survey_id` int NOT NULL
);

CREATE TABLE `Answer` (
  `answer_id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `question_id` int NOT NULL,
  `answer_text` varchar(255) NOT NULL
);

CREATE TABLE `Country` (
  `country_id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `country_name` varchar(255) NOT NULL,
  `phone_prefix` int -- de 7 a 15 caracteres
);

CREATE TABLE `UserVote` (
  `user_id` int NOT NULL,
  `answer_id` int NOT NULL,
  PRIMARY KEY (`user_id`, `answer_id`)
);

CREATE TABLE `UserSurveyAccess` (
  `user_id` int NOT NULL,
  `survey_id` int NOT NULL,
  PRIMARY KEY (`user_id`, `survey_id`)
);

CREATE TABLE `SendEmailTo` (
  `email` varchar(255) NOT NULL,
  `survey_id` int NOT NULL
);

CREATE TABLE `InvitedUser` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `survey_id` int NOT NULL
);


ALTER TABLE `Survey` ADD CONSTRAINT FK_Survey_User
FOREIGN KEY (user_id)
    REFERENCES User (user_id);

ALTER TABLE `Question` ADD CONSTRAINT FK_Question_Survey
FOREIGN KEY (survey_id)
    REFERENCES Survey (survey_id);

ALTER TABLE `Answer` ADD CONSTRAINT FK_Answer_Question
FOREIGN KEY (question_id)
    REFERENCES Question (question_id);

ALTER TABLE `UserVote` ADD CONSTRAINT FK_UserVote_Answer
FOREIGN KEY (answer_id)
    REFERENCES Answer (answer_id);

ALTER TABLE `UserVote` ADD CONSTRAINT FK_UserVote_User
FOREIGN KEY (user_id)
    REFERENCES User (user_id);

ALTER TABLE `UserSurveyAccess` ADD CONSTRAINT FK_UserSurveyAccess_User
FOREIGN KEY (user_id)
    REFERENCES User (user_id);

ALTER TABLE `UserSurveyAccess` ADD CONSTRAINT FK_UserSurveyAccess_Survey
FOREIGN KEY (survey_id)
    REFERENCES Survey (survey_id);
    
ALTER TABLE `User` ADD CONSTRAINT FK_User_Country
FOREIGN KEY (country_id)
    REFERENCES Country (country_id);

-- Insertar datos en la tabla Country
INSERT INTO `Country` (`country_name`, `phone_prefix`) VALUES
('Aruba', '297'),
('Afghanistan', '93'),
('Angola', '244'),
('Anguilla', '1264'),
('Albania', '355'),
('Andorra', '376'),
('Netherlands Antilles', '599'),
('United Arab Emirates', '971'),
('Argentina', '54'),
('Armenia', '374'),
('American Samoa', '1684'),
('Antarctica', NULL), -- No phone prefix available for Antarctica
('French Southern territories', NULL), -- No phone prefix available
('Antigua and Barbuda', '1268'),
('Australia', '61'),
('Austria', '43'),
('Azerbaijan', '994'),
('Burundi', '257'),
('Belgium', '32'),
('Benin', '229'),
('Burkina Faso', '226'),
('Bangladesh', '880'),
('Bulgaria', '359'),
('Bahrain', '973'),
('Bahamas', '1242'),
('Bosnia and Herzegovina', '387'),
('Belarus', '375'),
('Belize', '501'),
('Bermuda', '1441'),
('Bolivia', '591'),
('Brazil', '55'),
('Barbados', '1246'),
('Brunei', '673'),
('Bhutan', '975'),
('Bouvet Island', NULL), -- No phone prefix available for Bouvet Island
('Botswana', '267'),
('Central African Republic', '236'),
('Canada', '1'),
('Cocos (Keeling) Islands', '61'),
('Switzerland', '41'),
('Chile', '56'),
('China', '86'),
('Côte d’Ivoire', '225'),
('Cameroon', '237'),
('Congo, The Democratic Republic of the', '243'),
('Congo', '242'),
('Cook Islands', '682'),
('Colombia', '57'),
('Comoros', '269'),
('Cape Verde', '238'),
('Costa Rica', '506'),
('Cuba', '53'),
('Christmas Island', '61'),
('Cayman Islands', '1345'),
('Cyprus', '357'),
('Czech Republic', '420'),
('Germany', '49'),
('Djibouti', '253'),
('Dominica', '1767'),
('Denmark', '45'),
('Dominican Republic', '1809'),
('Algeria', '213'),
('Ecuador', '593'),
('Egypt', '20'),
('Eritrea', '291'),
('Western Sahara', '212'),
('Spain', '34'),
('Estonia', '372'),
('Ethiopia', '251'),
('Finland', '358'),
('Fiji Islands', '679'),
('Falkland Islands', '500'),
('France', '33'),
('Faroe Islands', '298'),
('Micronesia, Federated States of', '691'),
('Gabon', '241'),
('United Kingdom', '44'),
('Georgia', '995'),
('Ghana', '233'),
('Gibraltar', '350'),
('Guinea', '224'),
('Guadeloupe', '590'),
('Gambia', '220'),
('Guinea-Bissau', '245'),
('Equatorial Guinea', '240'),
('Greece', '30'),
('Grenada', '1473'),
('Greenland', '299'),
('Guatemala', '502'),
('French Guiana', '594'),
('Guam', '1671'),
('Guyana', '592'),
('Hong Kong', '852'),
('Heard Island and McDonald Islands', NULL), -- No phone prefix available
('Honduras', '504'),
('Croatia', '385'),
('Haiti', '509'),
('Hungary', '36'),
('Indonesia', '62'),
('India', '91'),
('British Indian Ocean Territory', '246'),
('Ireland', '353'),
('Iran', '98'),
('Iraq', '964'),
('Iceland', '354'),
('Israel', '972'),
('Italy', '39'),
('Jamaica', '1876'),
('Jordan', '962'),
('Japan', '81'),
('Kazakstan', '7'),
('Kenya', '254'),
('Kyrgyzstan', '996'),
('Cambodia', '855'),
('Kiribati', '686'),
('Saint Kitts and Nevis', '1869'),
('South Korea', '82'),
('Kuwait', '965'),
('Laos', '856'),
('Lebanon', '961'),
('Liberia', '231'),
('Libyan Arab Jamahiriya', '218'),
('Saint Lucia', '1758'),
('Liechtenstein', '423'),
('Sri Lanka', '94'),
('Lesotho', '266'),
('Lithuania', '370'),
('Luxembourg', '352'),
('Latvia', '371'),
('Macao', '853'),
('Morocco', '212'),
('Monaco', '377'),
('Moldova', '373'),
('Madagascar', '261'),
('Maldives', '960'),
('Mexico', '52'),
('Marshall Islands', '692'),
('Macedonia', '389'),
('Mali', '223'),
('Malta', '356'),
('Myanmar', '95'),
('Mongolia', '976'),
('Northern Mariana Islands', '1670'),
('Mozambique', '258'),
('Mauritania', '222'),
('Montserrat', '1664'),
('Martinique', '596'),
('Mauritius', '230'),
('Malawi', '265'),
('Malaysia', '60'),
('Mayotte', '262'),
('Namibia', '264'),
('New Caledonia', '687'),
('Niger', '227'),
('Norfolk Island', '672'),
('Nigeria', '234'),
('Nicaragua', '505'),
('Niue', '683'),
('Netherlands', '31'),
('Norway', '47'),
('Nepal', '977'),
('Nauru', '674'),
('New Zealand', '64'),
('Oman', '968'),
('Pakistan', '92'),
('Panama', '507'),
('Pitcairn', '64'),
('Peru', '51'),
('Philippines', '63'),
('Palau', '680'),
('Papua New Guinea', '675'),
('Poland', '48'),
('Puerto Rico', '1787'),
('North Korea', '850'),
('Portugal', '351'),
('Paraguay', '595'),
('Palestine', '970'),
('French Polynesia', '689'),
('Qatar', '974'),
('Réunion', '262'),
('Romania', '40'),
('Russian Federation', '7'),
('Rwanda', '250'),
('Saudi Arabia', '966'),
('Sudan', '249'),
('Senegal', '221'),
('Singapore', '65'),
('South Georgia and the South Sandwich Islands', NULL), -- No phone prefix available
('Saint Helena', '290'),
('Svalbard and Jan Mayen', '47'),
('Solomon Islands', '677'),
('Sierra Leone', '232'),
('El Salvador', '503'),
('San Marino', '378'),
('Somalia', '252'),
('Saint Pierre and Miquelon', '508'),
('Sao Tome and Principe', '239'),
('Suriname', '597'),
('Slovakia', '421'),
('Slovenia', '386'),
('Sweden', '46'),
('Swaziland', '268'),
('Seychelles', '248'),
('Syria', '963'),
('Turks and Caicos Islands', '1649'),
('Chad', '235'),
('Togo', '228'),
('Thailand', '66'),
('Tajikistan', '992'),
('Tokelau', '690'),
('Turkmenistan', '993'),
('East Timor', '670'),
('Tonga', '676'),
('Trinidad and Tobago', '1868'),
('Tunisia', '216'),
('Turkey', '90'),
('Tuvalu', '688'),
('Taiwan', '886'),
('Tanzania', '255'),
('Uganda', '256'),
('Ukraine', '380'),
('United States Minor Outlying Islands', '1'),
('Uruguay', '598'),
('United States', '1'),
('Uzbekistan', '998'),
('Holy See (Vatican City State)', '379'),
('Saint Vincent and the Grenadines', '1784'),
('Venezuela', '58'),
('Virgin Islands, British', '1284'),
('Virgin Islands, U.S.', '1340'),
('Vietnam', '84'),
('Vanuatu', '678'),
('Wallis and Futuna', '681'),
('Samoa', '685'),
('Yemen', '967'),
('Yugoslavia', NULL), -- No longer exists
('South Africa', '27'),
('Zambia', '260'),
('Zimbabwe', '263');