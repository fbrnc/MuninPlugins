#!/usr/bin/php -q
<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MuninPlugin.php';

/**
 * Sprit plugin for Munin
 * 
 * - Add symlink:
 * ln -s <pathtothisfile> /etc/munin/plugins/wetter
 * 
 * - Add configuration to /etc/munin/plugin-conf.d/
 * [sprit]
 * env.PLACES Wiesbaden,Bundesweit
 *
 * @author Fabrizio Branca
 */
class SpritMunin extends MuninPlugin {
	
	protected $cacheTime = 60;
	
	/**
	 * Setup
	 * 
	 * @return array
	 */
	protected function getSetup() {
		return array(
			'graph_title' => 'Spritpreise (Super)',
				// @see http://munin-monitoring.org/wiki/graph_category
			'graph_category' => 'other',
				// "second" or "minute" @see http://munin-monitoring.org/wiki/graph_period
			'graph_period' => 'minute', 
				// "yes" or "no" @see http://munin-monitoring.org/wiki/graph_scale
			'graph_scale' => 'no', 
			'graph_vlabel' => 'Euro',
				// @see http://munin-monitoring.org/wiki/graph_args
			'graph_args' => '--base 1000 -r --lower-limit 0'
		);
	}
	
	/**
	 * Get graphs
	 * Reading configuration from environment variable (set in munin's plugin configuration)
	 * 
	 * @return array
	 */
	protected function getGraphs() {
		$graphs = array();
		foreach (explode(',', getenv('PLACES')) as $place) {
			$place = trim($place);
			$graphs[$place] = array(
				'label' => ucfirst($place),
				'info' => 'Durchschnittspreise '. ucfirst($place),
				'type' => MuninPlugin::GRAPH_GAUGE,
				'draw' => MuninPlugin::DRAW_LINE1
			);
		}
		return $graphs;
	}
	
	/**
	 * Get values
	 * 
	 * @return array
	 */
	protected function _getValues() {
		$values = array();
		$html = file_get_contents('http://www.clever-tanken.de/statistik2.asp');
		foreach (array_keys($this->getGraphs()) as $graph) {
			$matches = array();
			$pattern = '/'.$graph.'.*(\d,\d{3}).*(\d,\d{3}).*(\d,\d{3})/msiU';
			preg_match($pattern, $html, $matches);
			$values[$graph] = str_replace(',', '.', $matches[3]);
		}
		
		return $values;		
	}
	
}

$plugin = new SpritMunin($argv);
$plugin->process($argv);
