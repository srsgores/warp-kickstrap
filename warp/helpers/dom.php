<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: DomWarpHelper
		Helper class for the DOM extension
*/
class DomWarpHelper extends WarpHelper {

	const HTML_DOCTYPE = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>%s</body></html>';	

	/*
		Function: create
			Get DOM object from file/string.

		Returns:
			Object
	*/
	public function create($input, $mode = 'html') {
		
		// is file ?
		if (substr(trim($input), 0, 1) != '<' && file_exists($input) && is_file($input)) {
			$input = file_get_contents($input);
		}

		// create object
		$dom = new WarpDOMDocument();

		// load xml/html
		if ($mode == 'xml') {
			$dom->loadXML($input);
		} else {

			// set doctype
			if (strpos($input, '<!DOCTYPE') === false) {
				$input = sprintf(self::HTML_DOCTYPE, $input);
			}
			
			$dom->loadHTML($input);
		}
		
		return $dom;
	}

}

/*
	Class: WarpDOMDocument
		Document Class with extended attributes and functions
*/
class WarpDOMDocument extends DOMDocument {
	
	public $_xpath;
	
	public function __construct($version = '1.0', $encoding = 'UTF-8') {
		parent::__construct($version, $encoding);
		
		// set node class
		$this->registerNodeClass('DOMElement', 'WarpDOMElement');
	}
	
	public function first($query) {

		if ($matches = $this->find($query)) {
			if ($matches->length) {
				return $matches->item(0);
			}
		}

		return null;
	}

	public function find($query) {
		return $this->xpath()->query(WarpCssSelectorParser::cssToXpath($query, 'descendant::'));
	}

	public function query($expression) {
		return $this->xpath()->query($expression);
	}

	public function xpath() {

		if (empty($this->_xpath)) {
			$this->_xpath = new DOMXPath($this);
		}
		
		return $this->_xpath;
	}
	
}

/*
	Class: WarpDOMElement
		Element Class with extended attributes and functions
*/
class WarpDOMElement extends DOMElement {

	public function first($query) {

		if ($matches = $this->find($query)) {
			if ($matches->length) {
				return $matches->item(0);
			}
		}

		return null;
	}
	
	public function find($query) {
		return $this->query(WarpCssSelectorParser::cssToXpath($query, 'descendant::'));
	}

	public function query($expression) {
		return $this->ownerDocument->xpath()->query($expression, $this);
	}

	public function parent() {
		return $this->parentNode;
	}

	public function next() {

		$sibling = $this->nextSibling;

		do {

			if ($sibling->nodeType == XML_ELEMENT_NODE) {
				break;
			}

		} while ($sibling = $sibling->nextSibling);
		
		return $sibling;
	}

	public function prev() {

		$sibling = $this->previousSibling;

		do {

			if ($sibling->nodeType == XML_ELEMENT_NODE) {
				break;
			}

		} while ($sibling = $sibling->previousSibling);
		
		return $sibling;
	}

	public function hasChildren() {
		return $this->hasChildNodes();
	}

	public function children($query = null) {

		$children = array();

		if (!$this->hasChildren()) {
			return $children;
		}

		if ($query == null) {
			
			foreach ($this->childNodes as $child) {
				if ($child->nodeType == XML_ELEMENT_NODE) {
					$children[] = $child;
				}
			}
			
			return $children;
		}

		return $this->query(WarpCssSelectorParser::cssToXpath($query, 'child::'));
	}
	
	public function removeChildren() {

		while ($child = $this->firstChild) {
			$this->removeChild($child);
		}

		return $this;
	}
	
	public function before($data) {

		$data = $this->prepareInsert($data);
		$this->parentNode->insertBefore($data, $this);

		return $this;
	}

	public function after($data) {

		$data = $this->prepareInsert($data);

		if (isset($this->nextSibling)) {
			$this->parentNode->insertBefore($data, $this->nextSibling);
		} else {
			$this->parentNode->appendChild($data);
		}

		return $this;
	}

	public function prepend($data) {

		$data = $this->prepareInsert($data);

		if (isset($data)) {
			if ($this->hasChildren()) {
				$this->insertBefore($data, $this->firstChild);
			} else {
				$this->appendChild($data);
			}
		}

		return $this;
	}

	public function append($data) {

		$data = $this->prepareInsert($data);

		if (isset($data)) {
			$this->appendChild($data);
		}

		return $this;
	}

	public function wrap($data) {

		$data = $this->prepareInsert($data);

		if (empty($data)) {
			return $this;
		}

		self::wrapNode($this, $data);

		return $this;  
	}	

	public function text($text = null) {

		if (isset($text)) {
			$this->removeChildren();
			$this->appendChild($this->ownerDocument->createTextNode($text));
			return $this;
		}

		return $this->textContent;
	}

	public function html($markup = null) {

		if (isset($markup)) {
			$this->removeChildren();
			$this->append($markup);
			return $this;
		}

		return $this->ownerDocument->saveXML($this);
	}

	public function tag() {
		return $this->tagName;
	}

	public function val($value = null) {

		if (isset($value)) {
			return $this->attr('value', $value);
		}

		return $this->attr('value');
	}
	
	public function hasClass($class){
		return self::attrClass('has', $this, $class);
    }

    public function addClass($class) {
		return self::attrClass('add', $this, $class);
    }

    public function removeClass($class){
		return self::attrClass('remove', $this, $class);
    }

    public function toggleClass($class){
		return self::attrClass('toggle', $this, $class);
    }

	public function attr($name = null, $value = null) {

		if (is_null($name)) {
			$attributes = array();

			foreach ($this->attributes as $name => $node) {
				$attributes[$name] = $node->value;
			}

			return $attributes;
		}

		if (isset($value)) {
			$this->setAttribute($name, $value);
			return $this;
		}

		return $this->getAttribute($name);
	}

	public function removeAttr($name) {
		$this->removeAttribute($name);
		return $this;
	}

	protected function prepareInsert($item) {

		if (empty($item)) {
			return;
		} 
		
		if (is_string($item)) {

			$item = WarpDOMEntities::replaceAllEntities($item);
			$frag = $this->ownerDocument->createDocumentFragment();

			try {
				$frag->appendXML($item);
			} catch (Exception $e) {}

			return $frag;
		}

		if ($item instanceof DOMNode) {

			if ($item->ownerDocument !== $this->ownerDocument) {
				return $this->ownerDocument->importNode($item, true);
			}

			return $item;
		}

	}

	protected static function wrapNode(DOMNode $node, DOMNode $wrapper) {

		if ($wrapper->hasChildNodes()) {
			$deepest = self::deepestNode($wrapper); 
			$wrapper = $deepest[0];
		}

		$parent = $node->parentNode;
		$parent->insertBefore($wrapper, $node);
		$wrapper->appendChild($parent->removeChild($node));
	}

	protected static function deepestNode(DOMNode $node, $depth = 0, $current = null, &$deepest = null) {

		if (!isset($current)) $current = array($node);
		if (!isset($deepest)) $deepest = $depth;

		if ($node->hasChildNodes()) {
			foreach ($node->childNodes as $child) {
				if ($child->nodeType === XML_ELEMENT_NODE) {
					$current = self::deepestNode($child, $depth + 1, $current, $deepest);
				}
			}
		} elseif ($depth > $deepest) {
			$current = array($node);
			$deepest = $depth;
		} elseif ($depth === $deepest) {
			$current[] = $node;
		}
		
		return $current;
	}

	protected static function attrClass($action, DOMNode $node, $class) {

	    $classes = $node->getAttribute('class');
		$found   = stripos($classes, $class) !== false && in_array(strtolower($class), explode(' ', strtolower($classes)));

		if ($action == 'has') {
			return $found;
		}

		if ($action == 'toggle') {
			$action = $found ? 'remove' : 'add';
		}

		if ($action == 'add' && !$found) {
			$node->setAttribute('class', trim(preg_replace('/\s{2,}/i', ' ', $classes.' '.$class)));
		} 

		if ($action == 'remove' && $found) {

			$classes = trim(preg_replace('/\s{2,}/i', ' ', preg_replace('/(^|\s)'.preg_quote($class, '/').'(?:\s|$)/i', ' ', $classes)));

			if ($classes !== '') {
				$node->setAttribute('class', $classes);
			} else {
				$node->removeAttribute('class');
			}
		}

		return $node;
    }

}

/*
	Class: WarpDOMEntities
		HTML/XML entity processing
		Based on QueryPath (http://querypath.org, 2009 Matt Butcher <matt@aleph-null.tv>, LGPL/MIT License)
*/
class WarpDOMEntities {
  
	protected static $regex = '/&([\w]+);|&#([\d]+);|&#(x[0-9a-fA-F]+);|(&)/m';
  
	public static function replaceAllEntities($string) {
		return preg_replace_callback(self::$regex, 'WarpDOMEntities::doReplacement', $string);
	}
  
	protected static function doReplacement($matches) {
		// From count, we can tell whether we got a 
		// char, num, or bare ampersand.
		$count = count($matches);
		switch ($count) {
			case 2:
			// We have a character entity
			return '&#' . self::replaceEntity($matches[1]) . ';';
			case 3:
			case 4:
			// we have a numeric entity
			return '&#' . $matches[$count-1] . ';'; 
			case 5:
			// We have an unescaped ampersand.
			return '&#38;';
		}
	}
  
	public static function replaceEntity($entity) {
		return self::$entity_array[$entity];
	}
  
	private static $entity_array = array(
		'nbsp' => 160, 'iexcl' => 161, 'cent' => 162, 'pound' => 163, 
		'curren' => 164, 'yen' => 165, 'brvbar' => 166, 'sect' => 167, 
		'uml' => 168, 'copy' => 169, 'ordf' => 170, 'laquo' => 171, 
		'not' => 172, 'shy' => 173, 'reg' => 174, 'macr' => 175, 'deg' => 176, 
		'plusmn' => 177, 'sup2' => 178, 'sup3' => 179, 'acute' => 180, 
		'micro' => 181, 'para' => 182, 'middot' => 183, 'cedil' => 184, 
		'sup1' => 185, 'ordm' => 186, 'raquo' => 187, 'frac14' => 188, 
		'frac12' => 189, 'frac34' => 190, 'iquest' => 191, 'Agrave' => 192, 
		'Aacute' => 193, 'Acirc' => 194, 'Atilde' => 195, 'Auml' => 196, 
		'Aring' => 197, 'AElig' => 198, 'Ccedil' => 199, 'Egrave' => 200, 
		'Eacute' => 201, 'Ecirc' => 202, 'Euml' => 203, 'Igrave' => 204, 
		'Iacute' => 205, 'Icirc' => 206, 'Iuml' => 207, 'ETH' => 208, 
		'Ntilde' => 209, 'Ograve' => 210, 'Oacute' => 211, 'Ocirc' => 212, 
		'Otilde' => 213, 'Ouml' => 214, 'times' => 215, 'Oslash' => 216, 
		'Ugrave' => 217, 'Uacute' => 218, 'Ucirc' => 219, 'Uuml' => 220, 
		'Yacute' => 221, 'THORN' => 222, 'szlig' => 223, 'agrave' => 224, 
		'aacute' => 225, 'acirc' => 226, 'atilde' => 227, 'auml' => 228, 
		'aring' => 229, 'aelig' => 230, 'ccedil' => 231, 'egrave' => 232, 
		'eacute' => 233, 'ecirc' => 234, 'euml' => 235, 'igrave' => 236, 
		'iacute' => 237, 'icirc' => 238, 'iuml' => 239, 'eth' => 240, 
		'ntilde' => 241, 'ograve' => 242, 'oacute' => 243, 'ocirc' => 244, 
		'otilde' => 245, 'ouml' => 246, 'divide' => 247, 'oslash' => 248, 
		'ugrave' => 249, 'uacute' => 250, 'ucirc' => 251, 'uuml' => 252, 
		'yacute' => 253, 'thorn' => 254, 'yuml' => 255, 'quot' => 34, 
		'amp' => 38, 'lt' => 60, 'gt' => 62, 'apos' => 39, 'OElig' => 338, 
		'oelig' => 339, 'Scaron' => 352, 'scaron' => 353, 'Yuml' => 376, 
		'circ' => 710, 'tilde' => 732, 'ensp' => 8194, 'emsp' => 8195, 
		'thinsp' => 8201, 'zwnj' => 8204, 'zwj' => 8205, 'lrm' => 8206, 
		'rlm' => 8207, 'ndash' => 8211, 'mdash' => 8212, 'lsquo' => 8216, 
		'rsquo' => 8217, 'sbquo' => 8218, 'ldquo' => 8220, 'rdquo' => 8221, 
		'bdquo' => 8222, 'dagger' => 8224, 'Dagger' => 8225, 'permil' => 8240, 
		'lsaquo' => 8249, 'rsaquo' => 8250, 'euro' => 8364, 'fnof' => 402, 
		'Alpha' => 913, 'Beta' => 914, 'Gamma' => 915, 'Delta' => 916, 
		'Epsilon' => 917, 'Zeta' => 918, 'Eta' => 919, 'Theta' => 920, 
		'Iota' => 921, 'Kappa' => 922, 'Lambda' => 923, 'Mu' => 924, 'Nu' => 925, 
		'Xi' => 926, 'Omicron' => 927, 'Pi' => 928, 'Rho' => 929, 'Sigma' => 931,
		'Tau' => 932, 'Upsilon' => 933, 'Phi' => 934, 'Chi' => 935, 'Psi' => 936,
		'Omega' => 937, 'alpha' => 945, 'beta' => 946, 'gamma' => 947, 
		'delta' => 948, 'epsilon' => 949, 'zeta' => 950, 'eta' => 951, 
		'theta' => 952, 'iota' => 953, 'kappa' => 954, 'lambda' => 955, 
		'mu' => 956, 'nu' => 957, 'xi' => 958, 'omicron' => 959, 'pi' => 960, 
		'rho' => 961, 'sigmaf' => 962, 'sigma' => 963, 'tau' => 964, 
		'upsilon' => 965, 'phi' => 966, 'chi' => 967, 'psi' => 968, 
		'omega' => 969, 'thetasym' => 977, 'upsih' => 978, 'piv' => 982, 
		'bull' => 8226, 'hellip' => 8230, 'prime' => 8242, 'Prime' => 8243, 
		'oline' => 8254, 'frasl' => 8260, 'weierp' => 8472, 'image' => 8465, 
		'real' => 8476, 'trade' => 8482, 'alefsym' => 8501, 'larr' => 8592, 
		'uarr' => 8593, 'rarr' => 8594, 'darr' => 8595, 'harr' => 8596, 
		'crarr' => 8629, 'lArr' => 8656, 'uArr' => 8657, 'rArr' => 8658, 
		'dArr' => 8659, 'hArr' => 8660, 'forall' => 8704, 'part' => 8706, 
		'exist' => 8707, 'empty' => 8709, 'nabla' => 8711, 'isin' => 8712, 
		'notin' => 8713, 'ni' => 8715, 'prod' => 8719, 'sum' => 8721, 
		'minus' => 8722, 'lowast' => 8727, 'radic' => 8730, 'prop' => 8733, 
		'infin' => 8734, 'ang' => 8736, 'and' => 8743, 'or' => 8744, 'cap' => 8745, 
		'cup' => 8746, 'int' => 8747, 'there4' => 8756, 'sim' => 8764, 
		'cong' => 8773, 'asymp' => 8776, 'ne' => 8800, 'equiv' => 8801, 
		'le' => 8804, 'ge' => 8805, 'sub' => 8834, 'sup' => 8835, 'nsub' => 8836, 
		'sube' => 8838, 'supe' => 8839, 'oplus' => 8853, 'otimes' => 8855, 
		'perp' => 8869, 'sdot' => 8901, 'lceil' => 8968, 'rceil' => 8969, 
		'lfloor' => 8970, 'rfloor' => 8971, 'lang' => 9001, 'rang' => 9002, 
		'loz' => 9674, 'spades' => 9824, 'clubs' => 9827, 'hearts' => 9829, 
		'diams' => 9830
	);

}

/*
	Class: WarpCssSelectorParser
		Converts CSS Selectors to XPath Query
*/
class WarpCssSelectorParser {

	protected static $regex = array('element' => '/^\s*(\*|[\w\-]+)(?:\b|$)?/i', 'id' => '/^#([\w\-\*]+)(?:\b|$)/i', 'class' => '/^\.([\w\-\*]+)(?:\b|$)/i', 'attr1' => '/^\[((?:[\w-]+:)?[\w-]+)\]/i', 'attr2' => '/^\[\s*([^~\*\!\^\$\|=\s]+)\s*([~\*\^\!\$\|]?=)\s*["\']?([^"\'\]]*)["\']?\s*\]/i', 'pseudo' => '/^:((?:first|last|only)-child|(?:en|dis)abled|first|last|empty|checked|not|contains)(?:\((.*?)\))?(?:\b|$|(?=\s|[:+~>]))/i', 'combinator' => '/^(?:\s*[>+~\s])?/i');

	protected static $xpath = array('id' => "@id = '%s'", 'class' => "contains(concat(' ', normalize-space(@class), ' '), ' %s ')", 'attr' => "@%s", 'contains' => "contains(string(.), '%s')", 'not' => 'not(%s)', 'operators' => array("=" => "@%1 = '%3'", "!=" => "not(@%1) or @%1 != '%3'", "^=" => "starts-with(@%1, '%3')", "$=" => "substring(@%1, (string-length(@%1) - string-length('%3') + 1)) = '%3'", "*=" => "contains(@%1, '%3')", "~=" => "contains(concat(' ', normalize-space(@%1), ' '), ' %3 ')", "|=" => "@%1 = '%3' or starts-with(@%1, '%3-')"), 'pseudos' => array('first-child' => 'not(preceding-sibling::*)', 'last-child' => 'not(following-sibling::*)', 'only-child' => 'not(preceding-sibling::* or following-sibling::*)', 'enabled' => "not(@disabled) and (@type!='hidden')", 'disabled' => "(@disabled) and (@type!='hidden')", 'first' => 'position() = 1', 'last' => 'last()', 'empty' => 'count(*) = 0 and (count(text()) = 0)', 'checked' => '@checked'), 'combinators' => array('>' => 'child', '~' => 'general-sibling', '+' => 'adjacent-sibling'));

	protected static $cache = array();

	public static function cssToXpath($selector, $prefix = 'descendant-or-self::') {

		if (!isset(self::$cache[$prefix][$selector])) {

			$xpath = array();

			foreach (explode(',', $selector) as $sel) {
				if ($sel = trim($sel)) {
					$xpath[] = self::convertSelector($sel, $prefix);
				}
			}

	  		if ($xpath = implode(' | ', $xpath)) {
				self::$cache[$prefix][$selector] = $xpath;
			} else {
				return null;
			}
		}

		return self::$cache[$prefix][$selector];
	}

	protected static function convertSelector($selector, $prefix) {

        $element  = array('element' => '*', 'combinator' => null, 'conditions' => array());
		$elements = array();
		$selector = trim($selector);
        $index    = 0;
        $last     = null;
		$xpath    = null;

		while (strlen($selector) > 0 && $selector != $last) {
			$last = $selector;

			// create element
			if (!isset($elements[$index])) {
				$elements[$index] = array_merge($element);
			}

			// match element name
			if (preg_match(self::$regex['element'], $selector, $matches)) {
				$elements[$index]['element'] = $matches[1];
				$selector = substr($selector, strlen($matches[0]));
			}

			// match id
			if (preg_match(self::$regex['id'], $selector, $matches)) {
				$elements[$index]['conditions'][] = sprintf(self::$xpath['id'], $matches[1]);
				$selector = substr($selector, strlen($matches[0]));
			}

			// match class name
			if (preg_match(self::$regex['class'], $selector, $matches)) {
				$elements[$index]['conditions'][] = sprintf(self::$xpath['class'], $matches[1]);
				$selector = substr($selector, strlen($matches[0]));
			}

			// match attribute presence
			if ($attr1 = preg_match(self::$regex['attr1'], $selector, $matches)) {
				$elements[$index]['conditions'][] = sprintf(self::$xpath['attr'], $matches[1]);
				$selector = substr($selector, strlen($matches[0]));
			}

			// match attribute and value
			if (!$attr1 && preg_match(self::$regex['attr2'], $selector, $matches)) {
				$elements[$index]['conditions'][] = str_replace(array('%1', '%3'), array($matches[1], $matches[3]), self::$xpath['operators'][$matches[2]]);				
				$selector = substr($selector, strlen($matches[0]));
			}

			// match pseudo
			if (preg_match(self::$regex['pseudo'], $selector, $matches)) {

				if (isset(self::$xpath['pseudos'][$matches[1]])) {
					$elements[$index]['conditions'][] = self::$xpath['pseudos'][$matches[1]];				
				} else if ($matches[1] == 'not') {
					$elements[$index]['conditions'][] = sprintf(self::$xpath['not'], self::cssToXpath($matches[2]));
				} else if ($matches[1] == 'contains') {
					$elements[$index]['conditions'][] = sprintf(self::$xpath['contains'], $matches[2]);
				}

				$selector = substr($selector, strlen($matches[0]));
			}

			// match combinators
			if (preg_match(self::$regex['combinator'], $selector, $matches) && strlen($matches[0])) {
				$combinator = 'descendant';

				if (($comb = trim($matches[0])) && isset(self::$xpath['combinators'][$comb])) {
					$combinator = self::$xpath['combinators'][$comb];
				}
				
				$elements[++$index] = array_merge($element, compact('combinator'));
				$selector = substr($selector, strlen($matches[0]));
			}

			$selector = trim($selector);
		}

		// create xpath expression
		foreach ($elements as $element) {

			switch ($element['combinator']) {

				case 'descendant':
					$xpath .= '/descendant::';
					break;
					
				case 'child':
					$xpath .= '/child::';
					break;

				case 'general-sibling':
					$xpath .= '/following-sibling::';
					break;

				case 'adjacent-sibling':
					$xpath .= '/following-sibling::';
					
					array_unshift($element['conditions'], 'position() = 1');
					
					if ($element['element'] != '*') {
						array_unshift($element['conditions'], sprintf("name() = '%s'", $element['element']));
						$element['element'] = '*';
					}
										
					break;				

				default:
					$xpath .= $prefix;
			}

			$xpath .= $element['element'];

			if (count($element['conditions'])) {
				$xpath .= '[';

				foreach ($element['conditions'] as $i => $condition) {
					$xpath .= $i == 0 ? $condition : sprintf(' and (%s)', $condition);
				}

				$xpath .= ']';
			}

		}

		return $xpath;
    }

}