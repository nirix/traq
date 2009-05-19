<?php
/**
 * BBCode Class
 * @author Jack Polgar <xocide@gmail.com>
 * @copyright Copyright (c)2009 Jack Polgar
 * @version 0.4
 */
class BBCode {
	
	/**
	 * Format
	 * The main format function.
	 */
	public function format($text,$safehtml=true) {
		$text = ($safehtml ? htmlspecialchars($text) : $text);
		$text = $this->quotes($text);
		$text = $this->textformat($text);
		$text = $this->links($text);
		$text = $this->lists($text);
		$text = $this->images($text);
		$text = $this->code($text);
		$text = $this->color($text);
		$text = $this->size($text);
		$text = $this->align($text);
		return $text;
	}
	
	/**
	 * Text Format
	 * Formats bold, underline, italics and strikethrough.
	 * @since 0.1
	 */
	public function textformat($text) {
		$bbcode = array(
			"/\[b\](.*?)\[\/b\]/is" => "<strong>$1</strong>",
			"/\[u\](.*?)\[\/u\]/is" => "<u>$1</u>",
			"/\[i\](.*?)\[\/i\]/is" => "<i>$1</i>",
			"/\[s\](.*?)\[\/s\]/is" => "<s>$1</s>"
			);
		$text = preg_replace(array_keys($bbcode), array_values($bbcode), $text);
		return $text;
	}

	/**
	 * Links
	 * Formats links.
	 * @since 0.2
	 */
	public function links($text) {
		$bbcode = array(
			"/\[url\](.*?)\[\/url\]/is" => '<a href="$1">$1</a>',
			"/\[url\=(.*?)\](.*?)\[\/url\]/is" => '<a href="$1">$2</a>'
			);
		$text = preg_replace(array_keys($bbcode), array_values($bbcode), $text);
		return $text;
	}
	
	/**
	 * Lists
	 * Formats lists.
	 * @since 0.3
	 */
	public function lists($text) {
		// Unordered Lists
		while(preg_match("#\[list\](.*?)\[/list\]#esi", $text))
		{
			$text = preg_replace("#\s?\[list\](.*?)\[/list\](\r\n?|\n?)#esi", "\$this->parse_lists('$1')\n", $text);
		}
		// Ordered Lists
		while(preg_match("#\[list=(a|A|i|I|1)\](.*?)\[/list\](\r\n?|\n?)#esi", $text))
		{
			$text = preg_replace("#\s?\[list=(1|a|A|i|I)\](.*?)\[/list\]#esi", "\$this->parse_lists('$2', '$1')\n", $text);
		}
		return $text;
	}
	
	// Used to format the lists properly... the old way didn't work so good.
	private function parse_lists($text,$type=NULL) {
		$text = preg_replace("#\s*\[\*\]\s*#", "</li>\n<li>", $text)."</li>";
		if($type) {
			$text = '<ol type="'.$type.'">'.$text.'</ol>'."";
		} else {
			$text = "<ul>".$text."</ul>";
		}
		$text = preg_replace('#<(ol type="'.$type.'"|ul)>\s*</li>#', "<$1>", $text);
		return $text;
	}
	
	/**
	 * Images
	 * Formats images.
	 * @since 0.3
	 */
	public function images($text) {
		$bbcode = array(
			"/\[img\](.*?)\[\/img\]/is" => '<img src="$1" alt="" />'
			);
		$text = preg_replace(array_keys($bbcode), array_values($bbcode), $text);
		return $text;
	}
	
	/**
	 * Code
	 * Formats code.
	 * @since 0.3
	 */
	public function code($text) {
		$bbcode = array(
			"/\[code\](.*?)\[\/code\]/is" => '<pre>$1</pre>',
			);
		$text = preg_replace(array_keys($bbcode), array_values($bbcode), $text);
		return $text;
	}
	
	/**
	 * Font Color
	 * Formats text color.
	 * @since 0.3
	 */
	public function color($text) {
		$bbcode = array(
			"/\[color\=(.*?)\](.*?)\[\/color\]/is" => '<span style="color:$1;">$2</span>'
			);
		$text = preg_replace(array_keys($bbcode), array_values($bbcode), $text);
		return $text;
	}
	
		
	/**
	 * Font Size
	 * Formats text size.
	 * @since 0.3
	 */
	public function size($text) {
		$bbcode = array(
			"/\[size\=(.*?)\](.*?)\[\/size\]/is" => '<span style="font-size:$1;">$2</span>'
			);
		$text = preg_replace(array_keys($bbcode), array_values($bbcode), $text);
		return $text;
	}
	
	/**
	 * Quotes
	 * Formats quotes.
	 * @since 0.3
	 */
	public function quotes($text) {
		// Normal lists
		while(preg_match("#\[quote\](.*?)\[/quote\]#esi", $text))
		{
			$text = preg_replace("/\[quote\](.*?)\[\/quote\]/is", '<blockquote class="quote"><p>$1</p></blockquote>', $text);
		}
		// Lists with a set type
		while(preg_match("#\[quote=(.*?)\](.*?)\[/quote\](\r\n?|\n?)#esi", $text))
		{
			$text = preg_replace("/\[quote\=(.*?)\](.*?)\[\/quote\]/is",'<blockquote class="quote"><div class="quote-head">$1:</div><p>$2</p></blockquote>', $text);
		}
		return $text;
	}

	/**
	 * Align
	 * Formats align
	 * @since 0.4
	 */
	public function align($text) {
		$bbcode = array(
			"/\[align\=(left|center|right|justify)\](.*?)\[\/align\]/is" => '<div style="text-align:$1;">$2</div>'
			
			);
		$text = preg_replace(array_keys($bbcode), array_values($bbcode), $text);
		return $text;
	}
}
?>