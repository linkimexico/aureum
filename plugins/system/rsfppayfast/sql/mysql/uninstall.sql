DROP TABLE IF EXISTS `#__rsform_payfast`;
DELETE FROM #__rsform_component_types WHERE ComponentTypeId = 512;
DELETE FROM #__rsform_component_type_fields WHERE ComponentTypeId = 512;

DELETE FROM #__rsform_config WHERE SettingName = 'payfast.merchantid';
DELETE FROM #__rsform_config WHERE SettingName = 'payfast.merchantkey';
DELETE FROM #__rsform_config WHERE SettingName = 'payfast.test';
DELETE FROM #__rsform_config WHERE SettingName = 'payfast.tax.type';
DELETE FROM #__rsform_config WHERE SettingName = 'payfast.tax.value';