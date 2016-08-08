<?php
	
	/*
	* class Sanitize
	*
	* Some methods to sanitize data
	*/
	class Sanitize {

		public static function process_array(&$array, $options) {
			if (in_array("htmlspecialchars", $options)) {
				$array = self::htmlspecialchars($array);
			}
			if (in_array("utf8_encode", $options)) {
				$array = self::utf8_encode($array);
			}
			if (in_array("stripslashes", $options)) {
				$array = self::stripslashes($array);
			}
			if (in_array("make_save_str_in", $options)) {
				$array = self::make_save_str_in($array);
			}
			if (in_array("htmlspecialchars_decode", $options)) {
				$array = self::htmlspecialchars_decode($array);
			}
			if (in_array("purify", $options)) {
				$array = self::purify($array);
			}
			if (in_array("trim", $options)) {
				$array = self::trim($array);
			}
			if (in_array("andto", $options)) {
				$array = self::andto($array);
			}
			if(in_array("urlencode", $options)) {
				$array = self::urlencode($array);
			}
	}		
		
		public static function htmlspecialchars($array) {
			foreach($array as $key => $item) {
				$array[$key] = is_array($item)
				? self::htmlspecialchars($item)
				: htmlspecialchars($item, ENT_QUOTES | ENT_DISALLOWED | ENT_IGNORE, 'UTF-8');
			} 
			return $array;
		}
		public static function htmlspecialchars_decode($array) {
			foreach($array as $key => $item) {
				$array[$key] = is_array($item)
				? self::htmlspecialchars_decode($item)
				: htmlspecialchars_decode($item, ENT_QUOTES | ENT_DISALLOWED | ENT_IGNORE);
			} 
			return $array;
		}
		public static function andto($array) {
			foreach($array as $key => $item) {
				$array[$key] = is_array($item)
				? self::andto($item)
				: str_replace("&amp;", "&", $item);
			} 
			return $array;
		}
		public static function purify($array) {
			foreach($array as $key => $item) {
				$array[$key] = is_array($item)
				? self::purify($item)
				: self::purify_check($item);
			} 
			return $array;
		}
		public static function urlencode($array) {
			foreach($array as $key => $item) {
				$array[$key] = is_array($item)
				? self::urlencode($item)
				: urlencode($item);
			} 
			return $array;
		}

		public static function purify_check($string) {
			require_once 'libs/htmlpurifier-4.6.0/library/HTMLPurifier.auto.php';
$config = HTMLPurifier_Config::createDefault();
//$config->set('HTML.DefinitionID', 'enduser-customize.html tutorial');
//$config->set('HTML.DefinitionRev', 1);
$config->set('Cache.DefinitionImpl', null); // remove this later!
$config->set('CSS.AllowTricky', true);
$config->set('HTML.SafeEmbed',true);
$config->set('HTML.SafeObject',true);
$config->set('HTML.SafeIframe', true);

$def = $config->getHTMLDefinition(true);



$def->addAttribute('a', 'target', new HTMLPurifier_AttrDef_Enum(
	array('_blank','_self','_target','_top')
));

// http://developers.whatwg.org/sections.html
$def->addElement('section', 'Block', 'Flow', 'Common');
$def->addElement('nav',     'Block', 'Flow', 'Common');
$def->addElement('article', 'Block', 'Flow', 'Common');
$def->addElement('aside',   'Block', 'Flow', 'Common');
$def->addElement('header',  'Block', 'Flow', 'Common');
$def->addElement('footer',  'Block', 'Flow', 'Common');
// Content model actually excludes several tags, not modelled here
$def->addElement('address', 'Block', 'Flow', 'Common');
$def->addElement('hgroup', 'Block', 'Required: h1 | h2 | h3 | h4 | h5 | h6', 'Common');

// http://developers.whatwg.org/grouping-content.html
$def->addElement('figure', 'Block', 'Optional: (figcaption, Flow) | (Flow, figcaption) | Flow', 'Common');
$def->addElement('figcaption', 'Inline', 'Flow', 'Common');

// http://developers.whatwg.org/the-video-element.html#the-video-element
$def->addElement('video', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
'src' => 'URI',
'type' => 'Text',
'width' => 'Length',
'height' => 'Length',
'poster' => 'URI',
'preload' => 'Enum#auto,metadata,none',
'controls' => 'Bool',
));
$def->addElement('audio', 'Block', 'Optional: (source, Flow) | (Flow, source) | Flow', 'Common', array(
'src' => 'URI',
'type' => 'Text',
'width' => 'Length',
'height' => 'Length',
'poster' => 'URI',
'preload' => 'Enum#auto,metadata,none',
'controls' => 'Bool',
));
$def->addElement('canvas', 'Block', 'Flow', 'Common');
$def->addElement('source', 'Block', 'Flow', 'Common', array(
'src' => 'URI',
'type' => 'Text',
));

// http://developers.whatwg.org/text-level-semantics.html
$def->addElement('s',    'Inline', 'Inline', 'Common');
$def->addElement('var',  'Inline', 'Inline', 'Common');
$def->addElement('sub',  'Inline', 'Inline', 'Common');
$def->addElement('sup',  'Inline', 'Inline', 'Common');
$def->addElement('mark', 'Inline', 'Inline', 'Common');
$def->addElement('wbr',  'Inline', 'Empty', 'Core');

// http://developers.whatwg.org/edits.html
$def->addElement('ins', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));
$def->addElement('del', 'Block', 'Flow', 'Common', array('cite' => 'URI', 'datetime' => 'CDATA'));

$def->addElement('progress', 'Inline', 'Flow', 'Common');
$def->addElement('form', 'Block', 'Flow', 'Common');
$def->addElement('fieldset', 'Block', 'Flow', 'Common');
$def->addElement('legend', 'Inline', 'Flow', 'Common');

$def->addElement('input', 'Inline', 'Flow', 'Common');
$def->addAttribute('input', 'type', 'Text');
$def->addAttribute('input', 'placeholder', 'Text');
$def->addAttribute('input', 'pattern', 'Text');
$def->addAttribute('input', 'name', 'Text');
$def->addAttribute('input', 'checked', 'Text');
$def->addAttribute('input', 'value', 'Text');
$def->addAttribute('input', 'min', 'Number');
$def->addAttribute('input', 'max', 'Number');
$def->addAttribute('input', 'id', 'Text');

$def->addElement('textarea', 'Inline', 'Flow', 'Common');
$def->addAttribute('textarea', 'rows', 'Number');
$def->addAttribute('textarea', 'cols', 'Number');
$def->addAttribute('textarea', 'placeholder', 'Text');

$def->addElement('label', 'Inline', 'Flow', 'Common');
$def->addAttribute('label', 'for', 'Text');


// TinyMCE
$def->addAttribute('img', 'data-mce-src', 'Text');
$def->addAttribute('img', 'data-mce-json', 'Text');

// Others
$def->addAttribute('iframe', 'allowfullscreen', 'Bool');
$def->addAttribute('iframe', 'src', 'Text');
$def->addAttribute('table', 'height', 'Text');
$def->addAttribute('td', 'border', 'Text');
$def->addAttribute('th', 'border', 'Text');
$def->addAttribute('tr', 'width', 'Text');
$def->addAttribute('tr', 'height', 'Text');
$def->addAttribute('tr', 'border', 'Text');

$def->addElement('select', 'Inline', 'Flow', 'Common');
$def->addElement('optgroup', 'Block', 'Flow', 'Common');
$def->addElement('option', 'Inline', 'Flow', 'Common');

$def->addElement('button', 'Inline', 'Flow', 'Common');
$def->addAttribute('button', 'type', 'Text');

$def->addElement('pre', 'Block', 'Flow', 'Common');
$def->addElement('ol', 'Block', 'Flow', 'Common');
$def->addElement('li', 'Block', 'Flow', 'Common');
$def->addElement('span', 'Block', 'Flow', 'Common');

$purifier = new HTMLPurifier($config);

$clean_html = $purifier->purify($string);

return $clean_html;
		}

		// public static function htmlspecialchars_decode($array) {
		// 	foreach($array as $key => $item) {
		// 		$array[$key] = is_array($item)
		// 		? self::htmlspecialchars_decode($item)
		// 		: htmlspecialchars_decode($item, ENT_QUOTES | ENT_DISALLOWED);
		// 	} 
		// 	return $array;
		// }

		public static function utf8_encode($array) {
			foreach($array as $key => $item)
				$array[$key] = is_array($item)
				? self::utf8_encode($item)
				: utf8_encode($item);
			return $array;
		}
		
		public static function utf8_decode($array) {
			foreach($array as $key => $item)
				$array[$key] = is_array($item)
				? self::utf8_decode($item)
				: utf8_decode($item);
			return $array;
		}
		public static function stripslashes($array) {
			foreach($array as $key => $item)
				$array[$key] = is_array($item)
				? self::stripslashes($item)
				: stripslashes($item);
			return $array;
		}
		
		public static function addslashes($array) {
			foreach($array as $key => $item)
				$array[$key] = is_array($item)
				? self::addslashes($item)
				: addslashes($item);
			return $array;
		}
		
		public static function trim($array) {
			foreach($array as $key => $item)
				$array[$key] = is_array($item)
				? self::trim($item)
				: self::ubertrim($item);
			return $array;
		}

		public static function ubertrim($s) {
			$s = preg_replace('/\xA0/u', ' ', $s);  // strips UTF-8 NBSP: "\xC2\xA0"
			$s = trim($s);
			return $s;
		}

		
		public static function make_save_str_in($array) {
			#$string = self::addslashes(self::stripslashes($array)); // stripslashes to avoid double slashing
			$string = self::trim($array);
			return $string;
		}
				
		public static function StringToPost($string){        
            $array = explode('&', $string);
            foreach($array as $key => $item) {
                $single = explode("=", $item);
                $array[$single[0]] = urldecode($single[1]);
                unset($array[$key]);
            }
            return $array;
        }
        
        public static function FileName($string) {
        	return preg_replace('/[^a-z\d_]/iu', '', $string);
        }
                
        public static function RemoveTagsFromPre($string) {
        	require_once("libs/simple_html_dom.php");   
	
           	$html = new simple_html_dom();
        	$html->load($string, true, false);   
        	foreach($html->find('pre') as $pre) {
        		$pre->innertext = str_replace("</li>", "\n", $pre->innertext);
        		$pre->innertext = preg_replace("/<(.|\n)*?>/", "", $pre->innertext);
        	} 
        	return $html->save();
        }
        
       #strippedValue.replace(/<li[^>]*>([\s\S]*?)<\/li>/ig, "* $1\n");
		
	}  // <!-- end class ’Sanitize’ -->
	
?>
