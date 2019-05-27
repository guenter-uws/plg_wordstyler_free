<?php
/**
 * Content Plugin for Joomla!
 * @author    Guenter Staendecke 
 * @copyright  Copyright 2019 Guenter Standecke
 * @license    GNU Public License version 3 or later
 * @link       http://www.uws-webagentur.de
 */
defined('_JEXEC') or die ('So long and thanks for all the fish');
jimport('joomla.plugin.plugin');
/**
 * Class PlgContentWordstyler01
 *
 * @since  April 2019
 */

class PlgContentWordstyler01 extends JPlugin
{
	/**
	 * Load the language file on instantiation (for Joomla! 3.X only)
	 *
	 * @var    boolean
	 * @since  3.3
	 */
	protected $autoloadLanguage = true;
	protected $app;
	

	/**
	 * Constructor.
	 *
	 * @param   object  &$subject  The object to observe.
	 * @param   array   $config    An optional associative array of configuration settings.
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		// Load the language file on instantiation (for Joomla! 2.5 and Joomla! 3.x)
		$this->loadLanguage();
	}
	
	//DB Conenction
	//$db = JFactory::getDBO();//Get Database Object

	// hier wird die ID von Artikel ermitteln, 
	//$db->setQuery('SELECT id FROM gtgcm_facileforms_records where name="mastermenuekarte" ORDER BY gtgcm_facileforms_records.id  DESC limit 1') ; // ID des neuen Records ermitteln
	//$artikelid = $db->loadResult();   // Id des neuen Records setzen
	/**
	 * Event method onContentBeforeDisplay
	 *
	 * @param   string  $context  The context of the content being passed to the plugin
	 * @param   mixed   &$row     An object with a "text" property
	 * @param   mixed   &$params  Additional parameters
	 * @param   int     $page     Optional page number
	 *
	 * @return  null
	 */
	
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{

		// Load categorie-IDs
		
		$category = $this->params->get('category');
		$category = trim($category);
		$category = chop($category, ";");
		
		$cat_array = explode(';', $category);
		
		if (preg_match('/[;/D]/', $cat_array)) {return true;};
	
		
		// Load word param value and check
      	$word_string = $this->params->get('word1');
		$word_string = trim($word_string);
		$word_string = chop($word_string, ";");
		// If last character of $word_string is ";" remove it
		$regex= '";[ ]{1}$"';
		
        $word_array = explode(';',$word_string);
		$n = count($word_array);
		  
		
	// Load Color param and check
		$colorw_1 = $this->params->get('colorw1');
		$regex_col = '"^#[A-Za-z|0-9]{3,6}$"' ;
		
		if ( !preg_match($regex_col, $colorw_1 )) { return true; }
		
	// Load Style param and check	
	    $allowed_style = array('em', 'strong', 'both', 'no');
		$style_1 = $this->params->get('style1');

		if (!in_array($style_1, $allowed_style)) {	return true; } 
		
	// Return if is not an article	
		if ($context != 'com_content.article' ) { return true; } 

	
    // Change style for each selected word in content(text)

		foreach( $word_array as $word_1) {
			$word_1= trim($word_1);
			
		$word_regex='/(^|\<p\>|[ \"]+)(?<name>'.$word_1.'\b)/';   //Searchstring !!!
		$word_1= ' '. $word_1;
                  
		// Style items for any style combo without color
		$chg_style1='<' . $style_1 .'>' . $word_1 . '</'. $style_1.'>';
		$chg_both1='<em><strong>'. $word_1 .'</strong></em>';
		
		// Style items for any style and color
		$chg_color1='<span style="color:' . $colorw_1 .'">' . $word_1 .'</span>';
		$chg_color_style1='<'. $style_1.'><span style="color:'. $colorw_1 .'">'.$word_1 .'</span></'. $style_1.'>';
		$chg_color_both1='<em><strong><span style="color:'. $colorw_1 .'">'. $word_1.'</span></strong></em>';	
		
		// Set of style replacements
		$replace = 'Musterstring';
		if ( $colorw_1 != '#rrggbb' && $style_1 == 'no') { $replace=$chg_color1; } ;
		if ( $colorw_1 != '#rrggbb' && $style_1 != 'no' ||$style_1 != 'both' ) {$replace=$chg_color_style1; } ;
		if ( $colorw_1 != '#rrggbb' && $style_1 == 'both') { $replace=$chg_color_both1; } ;
		if ( $colorw_1 == '#rrggbb' && $style_1 == 'both') { $replace=$chg_both1; } ;
			
       	// check aricle-catid with given catid	
		$cat_art = $row->catid ;
		if (!in_array($cat_art, $cat_array)) {	return true; } 
		
		$content = $row->text ;
		$row->text = preg_replace($word_regex,$replace,$content) ;
        } 
	}
}
?>
