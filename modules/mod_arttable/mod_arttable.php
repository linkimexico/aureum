<?php
/**
* @module		Art Table Lite Edition
* @copyright	Copyright (C) 2010 artetics.com
* @license		GPL
*/

defined('_JEXEC') or die('Restricted access');
error_reporting(E_ERROR);
define ("DS", DIRECTORY_SEPARATOR);
if (!function_exists('arttable_prepareHeaders')) {
	function arttable_prepareHeaders($headerNames, $rows) {
		if (!$headerNames) {
			if (is_array($rows[0])) {
				$arKeys = array_keys($rows[0]);
				$newArKeys = array();
				foreach ($arKeys as $key) {
					if (is_string($key)) {
						$newArKeys[] = $key;
					}
				}
				$headerNames = $newArKeys;
			} else {
				$result = get_object_vars($rows[0]);
				$headerNames = array_keys($result);
			}
		}
		return $headerNames;
	}
}
if (!function_exists('arttable_isImage')) {
	function arttable_isImage($fileName) {
		$extensions = array('.jpeg', '.jpg', '.gif', '.png', '.bmp', '.tiff', '.tif', '.ico', '.rle', '.dib', '.pct', '.pict');
		$extension = substr($fileName, strrpos($fileName,"."));
		if (in_array(strtolower($extension), $extensions)) return true;
		return false;
	}
}

if (!function_exists('arttable_isLink')) {
	function arttable_isLink($fileName) {
		if ((strpos($fileName, 'http://') === 0 || strpos($fileName, 'http://') === 0 || strpos($fileName, 'www.') === 0) && !arttable_isImage($fileName)) {
			return true;
		}
		return false;
	}
}

$document = &JFactory::getDocument();
$moduleId = $module->id;
if (!$id) $id = uniqid('arttable_', false);

$moduleclass_sfx = $params->get('moduleclass_sfx', '');
$tableType = $params->get('tableType', 'tablesorter');
$source = $params->get('source', 'sql');
$title = $params->get('title', '');
$className = $params->get('className', '');
$sqlQuery = $params->get('sqlQuery', '');
$userId = 0;
$user =& JFactory::getUser();
if ($user && $user->id) {
  $userId = $user->id;
}
$sqlQuery = str_replace('USER_ID', $userId, $sqlQuery); 

$convertLinks = $params->get('convertLinks', 1);
$showFirstLink = $params->get('showFirstLink', 0);
$loadJQuery = $params->get('loadJQuery', 1);
$connectionString = $params->get('connectionString', ''); 
if ($showFirstLink) {
	$convertLinks = 0;
}

$searchText = $params->get('searchText', 'Search:');
$searchSize = $params->get('searchSize', 15);
$headerStyle = $params->get('headerStyle', '');
$cellStyle = $params->get('cellStyle', '');

if ($headerStyle) {
	$hStyleArray = explode("\n", $headerStyle);
}
if ($cellStyle) {
	$styleArray = explode("\n", $cellStyle);
}
if ($chartLeftHeader) {
  $chartLeftHeaderArray = explode(",", $chartLeftHeader);
}

if ($tableType != 'tinytable' && $tableType != 'htmltable') {
	if ($loadJQuery) {
		$document->addScript(JURI::root() . 'modules/mod_arttable/js/jquery.js');
		JHtml::_('jquery.framework'); //load joomla's core jquery
	}
	$document->addScript(JURI::root() . 'modules/mod_arttable/js/jquery.nc.js');
}
$document->addStylesheet(JURI::root() . 'modules/mod_arttable/css/style.css');
if ($tableType == 'tablesorter') {
	$document->addScript(JURI::root() . 'modules/mod_arttable/tables/tablesorter/js/jquery.tablesorter.js');
	$document->addScript(JURI::root() . 'modules/mod_arttable/tables/tablesorter/js/jquery.tablesorter.pager.js');
	$document->addStylesheet(JURI::root() . 'modules/mod_arttable/tables/tablesorter/js/themes/blue/style.css');
	$document->addStylesheet(JURI::root() . 'modules/mod_arttable/tables/tablesorter/js/jquery.tablesorter.pager.css');
	
	$pagerContainerDiv .= '<div><input name="filter" value="" id="filter-box' . $moduleId . '" class="filter-box" value="" maxlength="' . $searchSize . '" size="' . $searchSize . '" type="text" /><div class="searchbox">' . $searchText . '&nbsp;</div></div>';
	$pagerContainerId = uniqid('arttablesorterpager_', false);
	$pagerContainerDiv .= '<div id="' . $pagerContainerId . '" class="pager">';
	$pagerContainerDiv .= '<img src="' . JURI:: base() . 'modules/mod_arttable/tables/tablesorter/js/icons/first.png" class="first"/>
	<img src="' . JURI:: base() . 'modules/mod_arttable/tables/tablesorter/js/icons/prev.png" class="prev"/>
	<input type="text" class="pagedisplay" size="4"/>
	<img src="' . JURI:: base() . 'modules/mod_arttable/tables/tablesorter/js/icons/next.png" class="next"/>
	<img src="' . JURI:: base() . 'modules/mod_arttable/tables/tablesorter/js/icons/last.png" class="last"/>
		
	<select class="pagesize">';
	$pagerContainerDiv .= '<option value="10">10</option><option selected="selected" value="20">20</option><option value="50">50</option>';
	$pagerContainerDiv .= '</select>';
	$pagerContainerDiv .= '</div>';
	$pagerContainerDiv .= '<input id="filter-clear-button' . $moduleId . '" type="submit" value="Clear" style="display:none;"/>';
	if (!$firstPageSize) {
		$firstPageSize = 20;
	}
	$pagerString = '.tablesorterPager({container: atjQuery("#' . $pagerContainerId . '"), size: ' . $firstPageSize . '})';
	$pagerString .= '.tablesorterFilter({filterContainer: atjQuery("#filter-box' . $moduleId . '"),filterClearContainer: atjQuery("#filter-clear-button' . $moduleId . '"),';
	$pagerString .= 'filterCaseSensitive: false})';
	$pagerString .= ';';
	$document->addCustomTag('<script type="text/javascript">atjQuery(document).ready(function() { atjQuery("#container_' . $moduleId . ' > table").addClass("tablesorter");atjQuery("#container_' . $moduleId . ' > table").tablesorter({})' . $pagerString . '; }); </script>');

}
$returnStr = '<div class="arttable_container" id="container_' . $moduleId . '">';
if ($title) {
	$returnStr .= '<span class="arttable_title">' . $title . '</span>';
}
if ($tableType == 'tablesorter') {
	$returnStr .= $pagerContainerDiv;
}
echo $returnStr;
if ($source == 'sql') {
	if (!$connectionString) {
		$db	=& JFactory::getDBO();
		$db->setQuery($sqlQuery);
    
		if (!$db->query()) {
			echo "Error executing query $sqlQuery: " . $db->getErrorMsg();
		}
		$rows = $db->loadObjectList();
	} else {
		require_once(JPATH_SITE . DS . 'modules' . DS . 'mod_arttable' . DS . 'library' . DS . 'db' . DS . 'adodb' . DS . 'adodb.inc.php');
		$connectionStringArray = explode(",", $connectionString);
		$dbType = $connectionStringArray[0];
		$host = $connectionStringArray[1];
		$database = $connectionStringArray[2];
		$userElm = $connectionStringArray[3];
		$pass = $connectionStringArray[4];
		$db = ADONewConnection($dbType);
		$result = $db->Connect("$host", "$userElm", "$pass", "$database");
		if (!$db->IsConnected()) {
			echo "Cannot connect to database $database on host $host";
		}
		if ($dbType != 'odbc' && $dbType != 'mssql') {
        		$db->EXECUTE("set names 'utf8'"); 
                }
		$rows = $db->GetArray($sqlQuery);
		if ($db->ErrorNo()) {
			echo "Error executing query $sqlQuery: " . $db->ErrorMsg();
		}
		$db->Close();
	}

	if (count($rows) > 0) {
		$headerNames = arttable_prepareHeaders($headerNames, $rows);
		echo '<table id="' . $id . '" ' . $attributes;
		if ($tableType == 'sortable') {
			echo ' class="sortable" ';
		}
		if ($className) {
			echo ' class="' . $className .' '.$moduleclass_sfx. '" ';
		}
    ?>
		>
    <?php
		if ($caption) echo '<caption>' . $caption . '</caption>';
		$i = 0;
		if ($headerNames) {
			if (is_array($headerNames)) {
				$headerNamesArray = $headerNames;
			} else {
				$headerNamesArray = explode(',', $headerNames);
			}
      ?>
			<thead 
      <?php
			if ($hideHeader == 'true') {
				echo ' style="display:none"';
			}
      ?>
			><tr>
      <?php
			if ($chartLeftHeaderArray) echo '<th></th>';
			foreach($headerNamesArray as $arrayElement) {
        ?>
				<th
        <?php
				if ($hStyleArray && $hStyleArray[$i]) {
					echo ' class="header' . $i . '" style="' . $hStyleArray[$i] . '"';
				}
        ?>
				>
        <?php
				echo $arrayElement;
        ?>
				</th>
        <?php
				$i++;
			}
      ?>
			</tr></thead>
      <?php
		}
		?>
    <tbody>
    <?php
		$j = 0;
		$headers = arttable_prepareHeaders($headers, $rows);
		foreach ($rows as $row) {
      ?>
			<tr>
      <?php
			if (is_array($headers)) {
				$headersArray = $headers;
			} else {
				$headersArray = explode(',', $headers);
			}
			if ($chartLeftHeaderArray && $j == 0) echo '<th>' . $chartLeftHeaderArray[$j] . '</th>';
			$count = count($headersArray);
			$i = 1;
			$k = 0;
			foreach($headersArray as $headerElement) {
				$headerElement = trim($headerElement);
				if ($styleArray && $styleArray[$k - 1]) {
					$td = '<td class="cell' . ($k - 1) . '" style="' . $styleArray[($k - 1)] . '">';
				} else {
					$td = '<td class="cell' . ($k - 1)  . '">';
				}
				$tdEnd = '</td>';
				if ($firstHeaderElement && $i == 1) {
					if ($styleArray && $styleArray[($k - 1) ]) {
						$td = '<th class="cell' . ($k - 1)  . '" style="' . $styleArray[($k - 1)] . '">';
					} else {
						$td = '<th>';
					}
					$tdEnd = '</th>';
				}
				if (is_array($row)) {
					$cell = $row[$headerElement];
				} else {
					$cell = $row->$headerElement;
				}
				if (is_array($row)) {
					if ($convertLinks == 1 && arttable_isLink($cell)) {
						echo $td . '<a ';
						if ($linksNofollow) {
							echo ' rel="nofollow" ';
						}
						if ($linksNewWindow) {
						  echo ' target="_blank" ';
						}
						echo 'href="' . htmlspecialchars($cell) . '">' . htmlspecialchars($cell) . '</a>' . $tdEnd;
					} else if ($convertLinks == 1 && arttable_isImage($cell)) {
						echo $td . '<img src="' . htmlspecialchars($cell) . '" alt="image"/></td>' . $tdEnd;
					} else {
						if ($i == 1 && $showFirstLink) {
							echo $td . '<a ';
							if ($linksNofollow) {
								echo ' rel="nofollow" ';
							}
						  if ($linksNewWindow) {
							echo ' target="_blank" ';
						  }
							echo 'href="#" onclick="filterTable(atjQuery(\'#' . $id . '\'), ' . $j . ')">' . $row[$headerElement] . '</a>' . $tdEnd;
						} else {
							echo $td . $row[$headerElement] . $tdEnd;
						}
					}
				} else {
					if (get_class($row) == 'stdClass') {
						if ($convertLinks == 1 && arttable_isLink($cell)) {
							echo $td . '<a ';
							if ($linksNofollow) {
								echo ' rel="nofollow" ';
							}
						  if ($linksNewWindow) {
							echo ' target="_blank" ';
						  }
							echo 'href="' . htmlspecialchars($cell) . '">' . htmlspecialchars($cell) . '</a>' . $tdEnd;
						} else if ($convertLinks == 1 && arttable_isImage($cell)) {
							echo $td . '<img src="' . htmlspecialchars($cell) . '" alt="image"/></td>' . $tdEnd;
						} else if ($convertLinks == 2 && $linkConversionPattern) {
							$cDelimiter = str_replace('TITLE', '', $linkConversionPattern);
							$cDelimiter = str_replace('URL', '', $cDelimiter);
							$cellArray = explode($cDelimiter, $cell);
							if (count($cellArray) > 1) {
								echo $td . '<a ';
								if ($linksNofollow) {
									echo ' rel="nofollow" ';
								}
								if ($linksNewWindow) {
								  echo ' target="_blank" ';
								}
								echo 'href="' . htmlspecialchars($cellArray[1]) . '">' . htmlspecialchars($cellArray[0]) . '</a>' . $tdEnd;
							} else {
								echo $td . htmlspecialchars($cell) . $tdEnd;
							}
						} else {
							if ($i == 1 && $showFirstLink) {
								echo $td . '<a ';
								if ($linksNofollow) {
									echo ' rel="nofollow" ';
								}
								if ($linksNewWindow) {
								  echo ' target="_blank" ';
								}
								echo 'href="#" onclick="filterTable(atjQuery(\'#' . $id . '\'), ' . $j . ')">' . $row->$headerElement . '</a>' . $tdEnd;
							} else {
								echo $td . $row->$headerElement . $tdEnd;
							}
						}
					} else {
						if ($convertLinks == 1 && arttable_isLink($cell)) {
							echo $td . '<a ';
							if ($linksNofollow) {
								echo ' rel="nofollow" ';
							}
							  if ($linksNewWindow) {
								echo ' target="_blank" ';
							  }
							echo 'href="' . htmlspecialchars($cell) . '">' . htmlspecialchars($cell) . '</a>' . $tdEnd;
						} else if ($convertLinks == 1 && arttable_isImage($cell)) {
							echo $td . '<img src="' . htmlspecialchars($cell) . '" alt="image"/></td>' . $tdEnd;
						} else if ($convertLinks == 2 && $linkConversionPattern) {
							$cDelimiter = str_replace('TITLE', '', $linkConversionPattern);
							$cDelimiter = str_replace('URL', '', $cDelimiter);
							$cellArray = explode($cDelimiter, $cell);
							if (count($cellArray) > 1) {
                ?>
								<td 
                <?php
								if ($styleArray && $styleArray[$j]) {
									echo ' style="' . $styleArray[$j] . '" ';
								}
								echo 'class="cell' . $j . '"><a ';
								if ($linksNofollow) {
									echo ' rel="nofollow" ';
								}
								if ($linksNewWindow) {
								  echo ' target="_blank" ';
								}
								echo 'href="' . htmlspecialchars($cellArray[1]) . '">' . htmlspecialchars($cellArray[0]) . '</a></td>';
							} else {
                ?>
								<td 
                <?php
								if ($styleArray && $styleArray[$j]) {
									echo ' style="' . $styleArray[$j] . '" ';
								}
								echo 'class="cell' . $j . '">' . htmlspecialchars($cell) . '</td>';
							}
						} else {
							if ($i == 1 && $showFirstLink) {
								echo $td . '<a ';
								if ($linksNofollow) {
									echo ' rel="nofollow" ';
								}
								if ($linksNewWindow) {
								  echo ' target="_blank" ';
								}
								echo 'href="#" onclick="filterTable(atjQuery(\'#' . $id . '\), ' . $j . ')">' . $row[$headerElement] . '</a>' . $tdEnd;
							} else {
								echo $td . $row[$headerElement] . $tdEnd;
							}
						}
					}
				}
				$i++;
				$k++;
			}
      ?>
			</tr>
      <?php
			$j++;
		}
    ?>
		</tbody>
		</table>
    <?php
	}
}

echo '</div>';

?>