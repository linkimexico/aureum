INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES (512, 'payfast');
DELETE FROM #__rsform_component_type_fields WHERE ComponentTypeId = 512;

INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES
(512, 'NAME', 'textbox', '', 0),
(512, 'LABEL', 'textbox', '', 1),
(512, 'COMPONENTTYPE', 'hidden', '512', 2),
(512, 'LAYOUTHIDDEN', 'hiddenparam', 'YES', 3);

CREATE TABLE IF NOT EXISTS `#__rsform_payfast` (
  `submission_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `signature` text NOT NULL,
  PRIMARY KEY (`submission_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `#__rsform_config` (`SettingName`, `SettingValue`) VALUES
('payfast.merchantid', ''),
('payfast.merchantkey', ''),
('payfast.test', '0'),
('payfast.tax.type', '1'),
('payfast.tax.value', '');