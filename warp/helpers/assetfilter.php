<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

/*
	Class: AssetFilterWarpHelper
		Asset filter helper class, to filter assets
*/
class AssetFilterWarpHelper extends WarpHelper {

	/*
		Function: create
			Create filter object(s)

		Parameters:
			$filters - String|Array

		Returns:
			Mixed
	*/
	public function create($filters = array()) {

		$prefix = 'WarpAssetFilter';

		// one filter
		if (is_string($filters)) {
			$class = $prefix.$filters;
			return new $class();
		}

		// multiple filter
		$collection = new WarpAssetFilterCollection();

		foreach ($filters as $name) {
			$class = $prefix.$name;
			$collection->add(new $class());
		}

		return $collection;
	}

}

/*
	Interface:  WarpAssetFilterInterface
		Asset filter interface
*/
interface WarpAssetFilterInterface {

    public function filterLoad($asset);

    public function filterContent($asset);

}

/*
	Class:  WarpAssetFilterCollection
		Asset filter collection
*/
class WarpAssetFilterCollection implements WarpAssetFilterInterface, Iterator {

	protected $filters;

	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct() {
		$this->filters = new SplObjectStorage();
	}

	/*
		Function: filterLoad
			On load filter callback

		Parameters:
			$asset - Object

		Returns:
			Void
	*/
	public function filterLoad($asset) {
		foreach ($this->filters as $filter) {
			$filter->filterLoad($asset);
		}
	}

	/*
		Function: filterContent
			On content filter callback

		Parameters:
			$asset - Object

		Returns:
			Void
	*/
	public function filterContent($asset) {
		foreach ($this->filters as $filter) {
			$filter->filterContent($asset);
		}
	}

	/*
		Function: add
			Add filter to collection

		Parameters:
			$filter - Object

		Returns:
			Void
	*/
	public function add($filter) {
		if ($filter instanceof Traversable) {
			foreach ($filter as $f) {
				$this->add($f);
			}
		} else {
			$this->filters->attach($filter);
		}
	}

	/*
		Function: remove
			Remove filter from collection

		Parameters:
			$filter - Object

		Returns:
			Void
	*/
	public function remove($filter) {
		$this->filters->detach($filter);
	}

	/* Iterator interface implementation */

	public function current() {
		return $this->filters->current();
	}

	public function key() {
		return $this->filters->key();
	}

	public function valid() {
		return $this->filters->valid();
	}

	public function next() {
		$this->filters->next();
	}

	public function rewind() {
		$this->filters->rewind();
	}

}

/*
	Class:  WarpAssetFilterCSSImportResolver
		Stylesheet import resolver, replaces @imports with it's content
*/
class WarpAssetFilterCSSImportResolver implements WarpAssetFilterInterface {

	/*
		Function: filterLoad
			On load filter callback

		Parameters:
			$asset - Object

		Returns:
			Void
	*/
    public function filterLoad($asset) {

		// is file asset?
		if (!is_a($asset, 'WarpFileAsset')) {
			return;
		}

		// resolve @import rules
        $content = $this->load($asset->getPath(), $asset->getContent());

        // move unresolved @import rules to the top
        $regexp = '/@import[^;]+;/i';
        if (preg_match_all($regexp, $content, $matches)) {
            $content = preg_replace($regexp, '', $content);
            $content = implode("\n", $matches[0])."\n".$content;
        }

		$asset->setContent($content);
    }

	/*
		Function: filterContent
			On content filter callback

		Parameters:
			$asset - Object

		Returns:
			Void
	*/
    public function filterContent($asset) {}

	/*
		Function: load
			Load file and get it's content

		Parameters:
			$file - String
			$content - String

		Returns:
			String
	*/
	protected function load($file, $content = '') {
		static $path;

		$oldpath = $path;

		if ($path && !strpos($file, '://')) {
			$file = realpath($path.'/'.$file);
		}

		$path = dirname($file);

		// get content from file, if not already set
		if (!$content && file_exists($file)) {
			$content = @file_get_contents($file);
		}

        // remove multiple charset declarations and resolve @imports to its actual content
		if ($content) {
			$content = preg_replace('/^@charset\s+[\'"](\S*)\b[\'"];/i', '', $content);
			$content = preg_replace_callback('/@import\s*(?:url\(\s*)?[\'"]?(?![a-z]+:)([^\'"\()]+)[\'"]?\s*\)?\s*;/', array($this, '_load'), $content);
		}

		$path = $oldpath;

		return $content;
	}

	/*
		Function: _load
			Load file recursively and fix url paths

		Parameters:
			$matches - Array

		Returns:
			String
	*/
    protected function _load($matches) {

		// resolve @import rules recursively
        $file = $this->load($matches[1]);

        // get file's directory remove '.' if its the current directory
        $directory = dirname($matches[1]);
        $directory = $directory == '.' ? '' : $directory . '/';

		// add directory file's to urls paths
        return preg_replace('/url\s*\(([\'"]?)(?![a-z]+:|\/+)/i', 'url(\1' . $directory, $file);
    }

}

/*
	Class:  WarpAssetFilterCSSRewriteURL
		Rewrite stylesheet urls, rewrites relative urls to absolute urls
*/
class WarpAssetFilterCSSRewriteURL implements WarpAssetFilterInterface {

	protected static $path;

	/*
		Function: filterLoad
			On load filter callback

		Parameters:
			$asset - Object

		Returns:
			Void
	*/
    public function filterLoad($asset) {

		// has url?
		if (!$asset->getUrl()) {
			return;
		}

		// set base path
		self::$path = dirname($asset->getUrl()).'/';

        $asset->setContent(preg_replace_callback('/url\(\s*[\'"]?(?![a-z]+:|\/+)([^\'")]+)[\'"]?\s*\)/i', array($this, 'rewrite'), $asset->getContent()));
    }

	/*
		Function: filterContent
			On content filter callback

		Parameters:
			$asset - Object

		Returns:
			Void
	*/
    public function filterContent($asset) {}

	/*
		Function: rewrite
			Rewrite url callback

		Parameters:
			$matches - Array

		Returns:
			String
	*/
    protected function rewrite($matches) {

        // prefix with base and remove '../' segments if possible
        $path = self::$path.$matches[1];
        $last = '';

        while ($path != $last) {
            $last = $path;
            $path = preg_replace('`(^|/)(?!\.\./)([^/]+)/\.\./`', '$1', $path);
        }

        return 'url("'.$path.'")';
    }

}

/*
	Class:  WarpAssetFilterCSSImageBase64
		Replace stylesheets image urls with base64 image strings
*/
class WarpAssetFilterCSSImageBase64 implements WarpAssetFilterInterface {

	/*
		Function: filterLoad
			On load filter callback

		Parameters:
			$asset - Object

		Returns:
			Void
	*/
    public function filterLoad($asset) {}

	/*
		Function: filterContent
			On content filter callback

		Parameters:
			$asset - Object

		Returns:
			Void
	*/
    public function filterContent($asset) {

        $images  = array();
		$content = $asset->getContent();

		// get images and the related path
        if (preg_match_all('/url\(\s*[\'"]?([^\'"]+)[\'"]?\s*\)/Ui', $asset->getContent(), $matches)) {
			foreach ($matches[0] as $i => $url) {
				if ($path = realpath($asset['base_path'].'/'.ltrim(preg_replace('/'.preg_quote($asset['base_url'], '/').'/', '', $matches[1][$i], 1), '/'))) {
	                $images[$url] = $path;
				}
			}
        }

        // check if image exists and filesize < 10kb
        foreach ($images as $url => $path) {
            if (filesize($path) <= 10240 && preg_match('/\.(gif|png|jpg)$/i', $path, $extension)) {
               $content = str_replace($url, sprintf('url(data:image/%s;base64,%s)', str_replace('jpg', 'jpeg', strtolower($extension[1])), base64_encode(file_get_contents($path))), $content);
            }
        }

		$asset->setContent($content);
    }

}

/*
	Class:  WarpAssetFilterCSSCompressor
		Stylesheet compressor, minifies css
		Based on Minify_CSS_Compressor (https://github.com/mrclay/minify, Ryan Grove <ryan@wonko.com>, Stephen Clay <steve@mrclay.org>, BSD License)
*/
class WarpAssetFilterCSSCompressor implements WarpAssetFilterInterface {

    /**
     * @var bool Are we "in" a hack?
     *
     * I.e. are some browsers targetted until the next comment?
     */
    protected $_inHack = false;

	/**
	 * Filter callbacks
	 */
    public function filterLoad($asset) {}

    public function filterContent($asset) {
        $asset->setContent($this->process($asset->getContent()));
    }

    /**
     * Minify a CSS string
     *
     * @param string $css
     *
     * @return string
     */
    public function process($css) {

        $css = str_replace("\r\n", "\n", $css);

        // preserve empty comment after '>'
        // http://www.webdevout.net/css-hacks#in_css-selectors
        $css = preg_replace('@>/\\*\\s*\\*/@', '>/*keep*/', $css);

        // preserve empty comment between property and value
        // http://css-discuss.incutio.com/?page=BoxModelHack
        $css = preg_replace('@/\\*\\s*\\*/\\s*:@', '/*keep*/:', $css);
        $css = preg_replace('@:\\s*/\\*\\s*\\*/@', ':/*keep*/', $css);

        // apply callback to all valid comments (and strip out surrounding ws
        $css = preg_replace_callback('@\\s*/\\*([\\s\\S]*?)\\*/\\s*@'
            ,array($this, '_commentCB'), $css);

        // remove ws around { } and last semicolon in declaration block
        $css = preg_replace('/\\s*{\\s*/', '{', $css);
        $css = preg_replace('/;?\\s*}\\s*/', '}', $css);

        // remove ws surrounding semicolons
        $css = preg_replace('/\\s*;\\s*/', ';', $css);

        // remove ws around urls
        $css = preg_replace('/
                url\\(      # url(
                \\s*
                ([^\\)]+?)  # 1 = the URL (really just a bunch of non right parenthesis)
                \\s*
                \\)         # )
            /x', 'url($1)', $css);

        // remove ws between rules and colons
        $css = preg_replace('/
                \\s*
                ([{;])              # 1 = beginning of block or rule separator
                \\s*
                ([\\*_]?[\\w\\-]+)  # 2 = property (and maybe IE filter)
                \\s*
                :
                \\s*
                (\\b|[#\'"-])       # 3 = first character of a value
            /x', '$1$2:$3', $css);

        // remove ws in selectors
        $css = preg_replace_callback('/
                (?:              # non-capture
                    \\s*
                    [^~>+,\\s]+  # selector part
                    \\s*
                    [,>+~]       # combinators
                )+
                \\s*
                [^~>+,\\s]+      # selector part
                {                # open declaration block
            /x'
            ,array($this, '_selectorsCB'), $css);

        // minimize hex colors
        $css = preg_replace('/([^=])#([a-f\\d])\\2([a-f\\d])\\3([a-f\\d])\\4([\\s;\\}])/i'
            , '$1#$2$3$4$5', $css);

        // remove spaces between font families
        $css = preg_replace_callback('/font-family:([^;}]+)([;}])/'
            ,array($this, '_fontFamilyCB'), $css);

        $css = preg_replace('/@import\\s+url/', '@import url', $css);

        // replace any ws involving newlines with a single newline
        $css = preg_replace('/[ \\t]*\\n+\\s*/', "\n", $css);

        // separate common descendent selectors w/ newlines (to limit line lengths)
        $css = preg_replace('/([\\w#\\.\\*]+)\\s+([\\w#\\.\\*]+){/', "$1\n$2{", $css);

        // Use newline after 1st numeric value (to limit line lengths).
        $css = preg_replace('/
            ((?:padding|margin|border|outline):\\d+(?:px|em)?) # 1 = prop : 1st numeric value
            \\s+
            /x'
            ,"$1\n", $css);

        // prevent triggering IE6 bug: http://www.crankygeek.com/ie6pebug/
        $css = preg_replace('/:first-l(etter|ine)\\{/', ':first-l$1 {', $css);

        return trim($css);
    }

    /**
     * Replace what looks like a set of selectors
     *
     * @param array $m regex matches
     *
     * @return string
     */
    protected function _selectorsCB($m) {

        // remove ws around the combinators
        return preg_replace('/\\s*([,>+~])\\s*/', '$1', $m[0]);
    }

    /**
     * Process a comment and return a replacement
     *
     * @param array $m regex matches
     *
     * @return string
     */
    protected function _commentCB($m) {

        $hasSurroundingWs = (trim($m[0]) !== $m[1]);
        $m = $m[1];

        // $m is the comment content w/o the surrounding tokens,
        // but the return value will replace the entire comment.
        if ($m === 'keep') {
            return '/**/';
        }

        if ($m === '" "') {
            // component of http://tantek.com/CSS/Examples/midpass.html
            return '/*" "*/';
        }

        if (preg_match('@";\\}\\s*\\}/\\*\\s+@', $m)) {
            // component of http://tantek.com/CSS/Examples/midpass.html
            return '/*";}}/* */';
        }

        if ($this->_inHack) {
            // inversion: feeding only to one browser
            if (preg_match('@
                    ^/               # comment started like /*/
                    \\s*
                    (\\S[\\s\\S]+?)  # has at least some non-ws content
                    \\s*
                    /\\*             # ends like /*/ or /**/
                @x', $m, $n)) {
                // end hack mode after this comment, but preserve the hack and comment content
                $this->_inHack = false;
                return "/*/{$n[1]}/**/";
            }
        }

        if (substr($m, -1) === '\\') { // comment ends like \*/
            // begin hack mode and preserve hack
            $this->_inHack = true;
            return '/*\\*/';
        }

        if ($m !== '' && $m[0] === '/') { // comment looks like /*/ foo */
            // begin hack mode and preserve hack
            $this->_inHack = true;
            return '/*/*/';
        }

        if ($this->_inHack) {
            // a regular comment ends hack mode but should be preserved
            $this->_inHack = false;
            return '/**/';
        }

        // Issue 107: if there's any surrounding whitespace, it may be important, so
        // replace the comment with a single space
        return $hasSurroundingWs // remove all other comments
            ? ' '
            : '';
    }

    /**
     * Process a font-family listing and return a replacement
     *
     * @param array $m regex matches
     *
     * @return string
     */
    protected function _fontFamilyCB($m) {

        $m[1] = preg_replace('/
                \\s*
                (
                    "[^"]+"      # 1 = family in double qutoes
                    |\'[^\']+\'  # or 1 = family in single quotes
                    |[\\w\\-]+   # or 1 = unquoted family
                )
                \\s*
            /x', '$1', $m[1]);

        return 'font-family:' . $m[1] . $m[2];
    }

}

/*
	Class: WarpAssetFilterJSCompressor
		Javascript compressor, minifies javascript
		Based on JSMin (https://github.com/mrclay/minify, Ryan Grove <ryan@wonko.com>, Stephen Clay <steve@mrclay.org>, BSD License)
*/		
class WarpAssetFilterJSCompressor implements WarpAssetFilterInterface {

    const ORD_LF            = 10;
    const ORD_SPACE         = 32;
    const ACTION_KEEP_A     = 1;
    const ACTION_DELETE_A   = 2;
    const ACTION_DELETE_A_B = 3;

    protected $a           = "\n";
    protected $b           = '';
    protected $input       = '';
    protected $inputIndex  = 0;
    protected $inputLength = 0;
    protected $lookAhead   = null;
    protected $output      = '';
    protected $lastByteOut = '';

	/**
	 * Filter callbacks
	 */
    public function filterLoad($asset) {}

    public function filterContent($asset) {
        $asset->setContent($this->process($asset->getContent()));
    }

    /**
     * Minify Javascript.
     *
     * @param string $script Javascript to be minified
     *
     * @return string
     */
	public function process($script) {

		// init vars
		$this->a           = "\n";
		$this->b           = '';
		$this->input       = $script;
		$this->inputIndex  = 0;
		$this->inputLength = 0;
		$this->lookAhead   = null;
		$this->output      = '';
		$this->lastByteOut = '';

		try { 
			$script = trim($this->min());
		} catch (Exception $e) {}

		return $script;
	}

    /**
     * Perform minification, return result
     *
     * @return string
     */
    public function min() {
        if ($this->output !== '') { // min already run
            return $this->output;
        }

        $mbIntEnc = null;
        if (function_exists('mb_strlen') && ((int)ini_get('mbstring.func_overload') & 2)) {
            $mbIntEnc = mb_internal_encoding();
            mb_internal_encoding('8bit');
        }
        $this->input = str_replace("\r\n", "\n", $this->input);
        $this->inputLength = strlen($this->input);

        $this->action(self::ACTION_DELETE_A_B);

        while ($this->a !== null) {
            // determine next command
            $command = self::ACTION_KEEP_A; // default
            if ($this->a === ' ') {
                if (($this->lastByteOut === '+' || $this->lastByteOut === '-') 
                    && ($this->b === $this->lastByteOut)) {
                    // Don't delete this space. If we do, the addition/subtraction
                    // could be parsed as a post-increment
                } elseif (! $this->isAlphaNum($this->b)) {
                    $command = self::ACTION_DELETE_A;
                }
            } elseif ($this->a === "\n") {
                if ($this->b === ' ') {
                    $command = self::ACTION_DELETE_A_B;
                // in case of mbstring.func_overload & 2, must check for null b,
                // otherwise mb_strpos will give WARNING
                } elseif ($this->b === null
                          || (false === strpos('{[(+-', $this->b)
                              && ! $this->isAlphaNum($this->b))) {
                    $command = self::ACTION_DELETE_A;
                }
            } elseif (! $this->isAlphaNum($this->a)) {
                if ($this->b === ' '
                    || ($this->b === "\n" 
                        && (false === strpos('}])+-"\'', $this->a)))) {
                    $command = self::ACTION_DELETE_A_B;
                }
            }
            $this->action($command);
        }
        $this->output = trim($this->output);

        if ($mbIntEnc !== null) {
            mb_internal_encoding($mbIntEnc);
        }
        return $this->output;
    }

    /**
     * ACTION_KEEP_A = Output A. Copy B to A. Get the next B.
     * ACTION_DELETE_A = Copy B to A. Get the next B.
     * ACTION_DELETE_A_B = Get the next B.
     *
     * @param int $command
     * @throws Exception
     */
    protected function action($command) {
        if ($command === self::ACTION_DELETE_A_B 
            && $this->b === ' '
            && ($this->a === '+' || $this->a === '-')) {
            // Note: we're at an addition/substraction operator; the inputIndex
            // will certainly be a valid index
            if ($this->input[$this->inputIndex] === $this->a) {
                // This is "+ +" or "- -". Don't delete the space.
                $command = self::ACTION_KEEP_A;
            }
        }
        switch ($command) {
            case self::ACTION_KEEP_A:
                $this->output .= $this->a;
                $this->lastByteOut = $this->a;
                
                // fallthrough
            case self::ACTION_DELETE_A:
                $this->a = $this->b;
                if ($this->a === "'" || $this->a === '"') { // string literal
                    $str = $this->a; // in case needed for exception
                    while (true) {
                        $this->output .= $this->a;
                        $this->lastByteOut = $this->a;
                        
                        $this->a       = $this->get();
                        if ($this->a === $this->b) { // end quote
                            break;
                        }
                        if (ord($this->a) <= self::ORD_LF) {
                            throw new Exception(
                                "JSMin: Unterminated String at byte "
                                . $this->inputIndex . ": {$str}");
                        }
                        $str .= $this->a;
                        if ($this->a === '\\') {
                            $this->output .= $this->a;
                            $this->lastByteOut = $this->a;
                            
                            $this->a       = $this->get();
                            $str .= $this->a;
                        }
                    }
                }
                // fallthrough
            case self::ACTION_DELETE_A_B:
                $this->b = $this->next();
                if ($this->b === '/' && $this->isRegexpLiteral()) { // RegExp literal
                    $this->output .= $this->a . $this->b;
                    $pattern = '/'; // in case needed for exception
                    while (true) {
                        $this->a = $this->get();
                        $pattern .= $this->a;
                        if ($this->a === '/') { // end pattern
                            break; // while (true)
                        } elseif ($this->a === '\\') {
                            $this->output .= $this->a;
                            $this->a       = $this->get();
                            $pattern      .= $this->a;
                        } elseif (ord($this->a) <= self::ORD_LF) {
                            throw new Exception(
                                "JSMin: Unterminated RegExp at byte "
                                . $this->inputIndex .": {$pattern}");
                        }
                        $this->output .= $this->a;
                        $this->lastByteOut = $this->a;
                    }
                    $this->b = $this->next();
                }
            // end case ACTION_DELETE_A_B
        }
    }

    /**
     * @return bool
     */
    protected function isRegexpLiteral() {
        if (false !== strpos("\n{;(,=:[!&|?", $this->a)) { // we aren't dividing
            return true;
        }
        if (' ' === $this->a) {
            $length = strlen($this->output);
            if ($length < 2) { // weird edge case
                return true;
            }
            // you can't divide a keyword
            if (preg_match('/(?:case|else|in|return|typeof)$/', $this->output, $m)) {
                if ($this->output === $m[0]) { // odd but could happen
                    return true;
                }
                // make sure it's a keyword, not end of an identifier
                $charBeforeKeyword = substr($this->output, $length - strlen($m[0]) - 1, 1);
                if (! $this->isAlphaNum($charBeforeKeyword)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Get next char. Convert ctrl char to space.
     *
     * @return string
     */
    protected function get() {
        $c = $this->lookAhead;
        $this->lookAhead = null;
        if ($c === null) {
            if ($this->inputIndex < $this->inputLength) {
                $c = $this->input[$this->inputIndex];
                $this->inputIndex += 1;
            } else {
                return null;
            }
        }
        if ($c === "\r" || $c === "\n") {
            return "\n";
        }
        if (ord($c) < self::ORD_SPACE) { // control char
            return ' ';
        }
        return $c;
    }

    /**
     * Get next char. If is ctrl character, translate to a space or newline.
     *
     * @return string
     */
    protected function peek() {
        $this->lookAhead = $this->get();
        return $this->lookAhead;
    }

    /**
     * Is $c a letter, digit, underscore, dollar sign, escape, or non-ASCII?
     *
     * @param string $c
     *
     * @return bool
     */
    protected function isAlphaNum($c) {
        return (preg_match('/^[0-9a-zA-Z_\\$\\\\]$/', $c) || ord($c) > 126);
    }

    /**
     * @return string
     */
    protected function singleLineComment() {
        $comment = '';
        while (true) {
            $get = $this->get();
            $comment .= $get;
            if (ord($get) <= self::ORD_LF) { // EOL reached
                // if IE conditional comment
                if (preg_match('/^\\/@(?:cc_on|if|elif|else|end)\\b/', $comment)) {
                    return "/{$comment}";
                }
                return $get;
            }
        }
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function multipleLineComment() {
        $this->get();
        $comment = '';
        while (true) {
            $get = $this->get();
            if ($get === '*') {
                if ($this->peek() === '/') { // end of comment reached
                    $this->get();
                    // if comment preserved by YUI Compressor
                    if (0 === strpos($comment, '!')) {
                        return "\n/*!" . substr($comment, 1) . "*/\n";
                    }
                    // if IE conditional comment
                    if (preg_match('/^@(?:cc_on|if|elif|else|end)\\b/', $comment)) {
                        return "/*{$comment}*/";
                    }
                    return ' ';
                }
            } elseif ($get === null) {
                throw new Exception(
                    "JSMin: Unterminated comment at byte "
                    . $this->inputIndex . ": /*{$comment}");
            }
            $comment .= $get;
        }
    }

    /**
     * Get the next character, skipping over comments.
     * Some comments may be preserved.
     *
     * @return string
     */
    protected function next() {
        $get = $this->get();
        if ($get !== '/') {
            return $get;
        }
        switch ($this->peek()) {
            case '/': return $this->singleLineComment();
            case '*': return $this->multipleLineComment();
            default: return $get;
        }
    }

}