<?php
/**
 * @version    1.3.3
 * @package    Joomla.Plugin
 * @subpackage Content.printfriendly
 * @copyright  Copyright (C) 2005 - 2017 PrintFriendly. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgContentPrintfriendly extends JPlugin {
  private $pfScriptInserted = false;

  function onContentBeforeDisplay($context, &$article, &$params, $limitstart = 0)
  {
    if (in_array('0', $this->params->get('button-place'))) {
      return $this->buttonInDivCode();
    }
  }

  function onContentAfterDisplay($context, &$article, &$params, $limitstart = 0)
  {
    if (in_array('1', $this->params->get('button-place'))) {
      return $this->buttonInDivCode();
    }
  }

  function onContentPrepare($context, &$article, &$params, $limitstart = 0)
  {
    if (in_array('2', $this->params->get('button-place'))) {
      $article->text = str_replace('[printfriendly]', $this->buttonCode(), $article->text);
    }
  }

  function buttonInDivCode()
  {
    $position = $this->params->get('button-position') == '0' ? 'left' : 'right';
    return '<div class="printfriendly-button-wrapper" style="text-align: ' . $position . '">' . $this->buttonCode() . '</div>';
  }

  function buttonCode()
  {
    $this->insertPfScript();

    $cdn = 'https://cdn.printfriendly.com/';

    switch ($this->params->get('button-style')) {
        case '0':
            $img = $cdn . 'buttons/printfriendly-button.png';
            break;
        case '1':
            $img = $cdn . 'buttons/printfriendly-pdf-button.png';
            break;
        case '2':
            $img = $cdn . 'buttons/printfriendly-button-lg.png';
            break;
        case '3':
            $img = $cdn . 'buttons/print-button.png';
            break;
        case '4':
            $img = $cdn . 'buttons/print-button-nobg.png';
            break;
        case '5':
            $img = $cdn . 'buttons/print-button-gray.png';
            break;
        case '6':
            $img = $this->params->get('button-custom-image');
            break;
        case '7':
            $img = $cdn . 'buttons/printfriendly-pdf-email-button.png';
            break;
        case '8':
            $img = $cdn . 'buttons/printfriendly-pdf-email-button-md.png';
            break;
        case '9':
            $img = $cdn . 'buttons/printfriendly-pdf-email-button-notext.png';
            break;
        case '10':
            $img = $cdn . 'buttons/printfriendly-pdf-button-nobg.png';
            break;
        case '11':
            $img = $cdn . 'buttons/printfriendly-pdf-button-nobg-md.png';
            break;
        case '12':
            $img = $cdn . 'buttons/printfriendly-button.png';
            break;
        case '13':
            $img = $cdn . 'buttons/printfriendly-button-nobg.png';
            break;
        case '14':
            $img = $cdn . 'buttons/printfriendly-button-md.png';
            break;
    }

    return '<a class="printfriendly-button print-no" style="cursor: pointer" onclick="window.print(); return false;" title="Print Friendly, PDF & Email"><img style="border:none;-webkit-box-shadow:none; box-shadow:none;" src="' . $img . '" alt="Print Friendly, PDF & Email"></a>';
  }

  function insertPfScript()
  {
    if (!$this->pfScriptInserted) {
      $this->pfScriptInserted = true;
      $document = JFactory::getDocument();
      $document->addScriptDeclaration('
        var pfCustomCSS = "' . $this->params->get('custom-css-url') . '";
      ');
      $document->addScript('https://cdn.printfriendly.com/printfriendly.js');
    }
  }

  function consoleLog($data) {
    echo '<script> console.log(' . json_encode($data) . '); </script>';
  }
}
