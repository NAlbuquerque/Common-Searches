<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2011, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */
 
// ------------------------------------------------------------------------

/**
 * Search Stats Plugin
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Plugin
 * @author		Nuno Albuquerque
 * @link		
 */

$plugin_info = array(
	'pi_name'		=> 'Search Stats',
	'pi_version'	=> '1.0',
	'pi_author'		=> 'Nuno Albuquerque',
	'pi_author_url'	=> '',
	'pi_description'=> 'Provides additional information about stored search data.',
	'pi_usage'		=> Search_stats::usage()
);


class Search_stats {

	public $return_data;
    
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();
	}
	
	public function common_searches()
	{
		
		//bulld
		$this->EE->db->select('search_terms as term, COUNT(*) as term_count');
		$this->EE->db->group_by('search_terms');
		$this->EE->db->order_by('term_count', 'desc');
		$this->EE->db->limit(20);

		$query = $this->EE->db->get('search_log');

		// If no results, exit now
		if($query->num_rows() == 0)
		{

			return $this->EE->TMPL->no_results();

		}
		
		$tagdata = $this->EE->TMPL->tagdata;

		$output = '';

		$output .= $this->EE->TMPL->parse_variables($tagdata, $query->result_array());

		$return_data = $output;

		return $return_data;

	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Plugin Usage
	 */
	public static function usage()
	{
		ob_start();
?>

 Since you did not provide instructions on the form, make sure to put plugin documentation here.
<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}


/* End of file pi.search_stats.php */
/* Location: /system/expressionengine/third_party/search_stats/pi.search_stats.php */