INSERT IGNORE INTO `#__rsform_component_types` (`ComponentTypeId`, `ComponentTypeName`) VALUES (2525, 'joomlacaptcha');

DELETE FROM #__rsform_component_type_fields WHERE ComponentTypeId = 2525;
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(2525, 'NAME', 'textbox', '', 0);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(2525, 'CAPTION', 'textbox', '', 1);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(2525, 'ADDITIONALATTRIBUTES', 'textarea', '', 2);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(2525, 'DESCRIPTION', 'textarea', '', 3);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(2525, 'VALIDATIONMESSAGE', 'textarea', 'INVALIDINPUT', 4);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(2525, 'CAPTCHA', 'select', '//<code>\n$mainframe = JFactory::getApplication();\n$response = $mainframe->triggerEvent(\'rsfp_bk_getAvailableCaptchas\', array());\nreturn $response[0];\n//</code>', 5);
INSERT IGNORE INTO `#__rsform_component_type_fields` (`ComponentTypeId`, `FieldName`, `FieldType`, `FieldValues`, `Ordering`) VALUES(2525, 'COMPONENTTYPE', 'hidden', '2525', 6);