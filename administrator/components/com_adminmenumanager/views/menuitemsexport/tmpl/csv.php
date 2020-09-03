<?php
/**
* @package Admin-Menu-Manager (com_adminmenumanager)
* @version 2.3.2
* @copyright Copyright (C) 2012 - 2019 Carsten Engel. All rights reserved.
* @license GNU/GPL http://www.gnu.org/licenses/gpl-2.0.html 
* @author http://www.pages-and-items.com
*/



// no direct access
defined('_JEXEC') or die('Restricted access');

$out = $this->csv_string;

$out = chr(255).chr(254).mb_convert_encoding( $out, 'UTF-16LE', 'UTF-8');

@ob_end_clean();
$file_name = 'menuitems_export'.date('YmdHis').'.csv';
@ini_set("zlib.output_compression", "Off");
header("Content-Type: text/comma-separated-values; charset=utf-8");
header("Content-Disposition: attachment;filename=\"$file_name\"");
header("Content-Transfer-Encoding: 8bit");
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: private");
header("Content-Length: ".strlen($out));
echo $out;
exit;

?>