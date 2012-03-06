<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter Pager class
 *
 * Generates pretty paged links with middle (current page) indicator.
 *
 * @package		  CodeIgniter
 * @subpackage   Pager
 * @version		  1.1
 * @author		  Kerem Güneþ <http://qeremy.com>
 * @license		  Apache License 2.0
 * @copyright	  2010 Kerem Güneþ
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Core Pager class
 */
class Pager
{

	/**
	 * SQL query offset {SELECT * FROM `table` LIMIT $offset}
	 *
	 * @since 1.0
	 * @access public
	 * @var int
	 */
	var $offset;
	
	/**
	 * SQL query limit {SELECT * FROM `table` LIMIT $offset, $limit}
	 *
	 * @since 1.0
	 * @access public
	 * @var int
	 */
	var $limit = 10;
	
	/**
	 * Handles the 'page' segment name
	 *
	 * @since 1.0
	 * @access public
	 * @var string
	 */
	var $segment = 'page';
	
	/**
	 * Handles the 'page' segment value
	 *
	 * @since 1.0
	 * @access public
	 * @var int
	 */
	var $page = 1;
	
	/**
	 * Count of total DB records will be used
	 *
	 * @since 1.0
	 * @access public
	 * @var int
	 */
	var $total_records = 0;
	
	/**
	 * Total DB records paged will be paged
	 *
	 * @since 1.0
	 * @access public
	 * @var int
	 */
	var $total_pages = 0;
	
	/**
	 * How many pages will be visible // if(3){<< < 1 2 3 > >>}
	 *
	 * @since 1.0
	 * @access public
	 * @var int
	 */
	var $links = 5;
	
	/**
	 * Display configs of the nav links
	 *
	 * @since 1.0
	 * @access public
	 * @var array
	 */
	var $tools = array('first' => '<<', 'prev' => '<', 'next' => '>', 'last' => '>>');
	
	/**
	 * HTML or Array output of the paged links
	 *
	 * @since 1.0
	 * @access public
	 * @var array
	 */
	var $pages = array();
	
	/**
	 * File extension like site.com/page/1.html or site.com/page/5.php
	 *
	 * @since 1.1
	 * @access public
	 * @var string
	 */
	var $ext = '';
	
	/**
	 * Setup the Pager.
	 *
	 * @since 1.0
	 *
	 * @param array $args (
	 * 	@param int $count Count of total records      (Required)
	 * 	@param int $limit Limit of the query          (Optinal)
	 * 	@param string $segment Page segment keeper    (Optinal)
	 * )
	 */
	function __construct($args = array())
	{
		if(!isset($args['count']) or $args['count'] < 1)
			return;
		
		$this->limit = $args['limit'] ? $args['limit'] : $this->limit;
		$this->segment = $args['segment'] ? $args['segment'] : $this->segment;
		
		preg_match('#/'. $this->segment .'/(\d+)#', $_SERVER['REQUEST_URI'], $matches);
		
		if(count($matches))
			$this->page = intval($matches[1]);
		
		$start = ($this->page >= 1) ? $this->page : 0;
		
		$this->offset = ($start <= 1) ? 0 : ($start * $this->limit) - $this->limit;
		$this->total_records = $args['count'];
		$this->total_pages = ceil($this->total_records / $this->limit);
	}
	
	/**
	 * Generate the links as HTML output or Array.
	 *
	 * @since 1.0
	 *
	 * @param int $links Links to display
	 * @param string|array $ignore Segments to be ignored
	 * @param bool $format Sets the output as imploded
	 */
	function generate($links = null, $ignore = null, $format = true)
	{
		$this->links = $links ? $links : $this->links;
		if($this->total_pages <= 5)
			$this->links = $this->total_pages;
		elseif($this->total_pages > 5 and $this->total_pages < 9)
			$this->links = 5;
		
		if($this->total_records < 1)
			return;
		
		if($this->links > $this->total_pages)
			$this->links = $this->total_pages;
		
		$uri_tmp = preg_replace('#'. $this->segment .'/\d+.*#', '', $_SERVER['REQUEST_URI']);
		$uri_tmp = trim($uri_tmp, '/');
		$segments_exp = explode('/', $uri_tmp);
		$segments_arr = array();
		
		if(count($segments_exp)) {
			foreach($segments_exp as $segment) {
				if($segment == $this->segment)
					continue;
				
				if($ignore) {
					if(is_string($ignore) and $segment == $ignore)
						continue;
					if(is_array($ignore) and in_array($segment, $ignore))
						continue;
				}
				
				$segments_arr[] = $segment;
			}
			$segments = implode('/', $segments_arr);
		}
		
		$uri = 'http://'. $_SERVER['HTTP_HOST'] .'/'. ($segments ? $segments : '');
		
		$start = ($this->page >= 1) ? $this->page : 1;
		$stop = $start + $this->links;
		
		if(($start - 1) >= 1) {
			$this->pages['first'] = $this->tools['first']
				? '<a class="first" href="'. $uri .'/'. $this->segment .'/1'. $this->ext .'">'. $this->tools['first'] .'</a>'
				: '';
			$this->pages['prev'] = '<a class="prev" href="'. $uri .'/'. $this->segment .'/'. ($start - 1) . $this->ext .'">'. $this->tools['prev'] .'</a>';
		}
		
		// ohh, math is soo boring :\
		$sub = 1;
		$middle = ceil($this->links / 2);
		$middle_sub = $middle - $sub;
		if($start >= $middle) {
			$i = $start - $middle_sub;
			$loop = $stop - $middle_sub;
		}
		else {
			$i = $sub;
			$loop = $start == $middle_sub ? $stop - $sub : $stop;
			if($loop >= $this->links) {
				$diff = $loop - $this->links;
				$loop = $loop - $diff + $sub;
			}
		}
		
		for($i; $i < $loop; $i++) {
			if($loop <= $this->total_pages) {
				@$this->pages['nums'] .= ($i == $start)
					? ' <span class="current">'. $i .'</span> '
					: ' <a href="'. $uri .'/'. $this->segment .'/'. $i . $this->ext .'">'. $i .'</a> ';
			}
			else {
				$extra = $this->total_pages - $start;
				$j = $start;
				
				if($extra < $this->links)
					$j = $j - (($this->links - 1) - $extra);
				
				for($j; $j <= $this->total_pages; $j++) {
					@$this->pages['nums'] .= ($j == $start)
						? ' <span class="current">'. $j .'</span> '
						: ' <a href="'. $uri .'/'. $this->segment .'/'. $j . $this->ext .'">'. $j .'</a> ';
				}
				
				break;
			}
		}
		
		if($start != $this->total_pages) {
			$this->pages['next'] = '<a class="next" href="'. $uri .'/'. $this->segment .'/'. ($start + 1) . $this->ext .'">'. $this->tools['next'] .'</a>';
			$this->pages['last'] = $this->tools['last']
				? '<a class="last" href="'. $uri .'/'. $this->segment .'/'. $this->total_pages . $this->ext .'">'. $this->tools['last'] .'</a>'
				: '';
		}
		
		if($format)
			$this->pages = implode(' ', $this->pages);
		
		return $this->pages;
	}
}

/* End of file pager.php */
/* Location: ./application/libraries/pager-1.1.php */