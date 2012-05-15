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
 * Common Searches Plugin
 *
 * @package		ExpressionEngine
 * @subpackage	Addons
 * @category	Plugin
 * @author		Nuno Albuquerque
 * @link		http://www.nainteractive.com
 */

$plugin_info = array(
	'pi_name'		=> 'Common Searches',
	'pi_version'	=> '1.0',
	'pi_author'		=> 'Nuno Albuquerque',
	'pi_author_url'	=> 'http://www.nainteractive.com',
	'pi_description'=> 'This plugin provides a list of most common search terms stored in the search log.',
	'pi_usage'		=> Common_searches::usage()
);


class Common_searches {

	public $return_data;
    
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->EE =& get_instance();

		// execute default
		$this->get_common_searches();

		return;
	}

	private function get_common_searches()
	{

		// get parameters
		$limit = $this->EE->TMPL->fetch_param('limit', '10');
		$search_type = $this->EE->TMPL->fetch_param('search_type');
		$threshold = $this->EE->TMPL->fetch_param('threshold');
		$site_id = $this->EE->TMPL->fetch_param('site_id', $this->EE->config->item('site_id'));
		$member_id = $this->EE->TMPL->fetch_param('member_id');

		// build query

		// if a search type
		if($search_type != '')
		{
			$this->EE->db->where('search_type', strtolower($search_type));
		}

		// Get current member ID from session due to parsing order
		if($member_id == "CURRENT_MEMBER")
		{
			$member_id = $this->EE->session->userdata['member_id'];
		}

		// If there is an id and its greater than 0 apply where clause
		if(is_numeric($member_id) && $member_id != 0)
		{
			$this->EE->db->where('member_id', $member_id);
		}

		// if has a threshold
		if(is_numeric($threshold))
		{
			$this->EE->db->having('term_count >=', $threshold);
		}


		$this->EE->db->select('search_terms as term, COUNT(*) as term_count');
		$this->EE->db->group_by('search_terms');
		$this->EE->db->order_by('term_count', 'desc');
		$this->EE->db->limit($limit);
		$this->EE->db->where('site_id',$site_id);
		$query = $this->EE->db->get('search_log');

		// If no results, exit now
		if($query->num_rows() == 0)
		{
			return $this->EE->TMPL->no_results();
		}
		
		$tagdata = $this->EE->TMPL->tagdata;

		$this->return_data = $this->EE->TMPL->parse_variables($tagdata, $query->result_array());

		return;
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Plugin Usage
	 */
	public static function usage()
	{
		ob_start();
?>

This plugin provides a list of most common search terms stored in the search log.


Example:

		{exp:common_searches member_id="CURRENT_MEMBER"}
			{if no_results}
				Sorry, there are not Common Searches available.
			{/if}

			{count} - {term} - {term_count}
		{/exp:common_searches}
<?php
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}


/* End of file pi.common_searches.php */
/* Location: /system/expressionengine/third_party/common_searches/pi.common_searches.php */