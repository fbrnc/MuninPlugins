#!/usr/bin/php -q
<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MuninPlugin.php';

/**
 * Weather plugin for munin
 *
 * - Add symlink:
 * ln -s <pathtothisfile> /etc/munin/plugins/wetter
 *
 * - Add configuration to /etc/munin/plugin-conf.d/
 * [wetter]
 * env.PLACES GMXX0138,ITXX0156,USCA0987
 *
 * @author Fabrizio Branca
 */
class WetterMunin extends MuninPlugin {

	protected $cacheTime = 0;

	/**
	 * @var array internal cache
	 */
	protected $data = array();

	/**
	 * Setup
	 *
	 * @return array
	 */
	protected function getSetup() {
		return array(
			'graph_title' => 'Wetter',
				// @see http://munin-monitoring.org/wiki/graph_category
			'graph_category' => 'other',
				// "second" or "minute" @see http://munin-monitoring.org/wiki/graph_period
			'graph_period' => 'minute',
				// "yes" or "no" @see http://munin-monitoring.org/wiki/graph_scale
			'graph_scale' => 'no',
			'graph_vlabel' => 'C',
				// @see http://munin-monitoring.org/wiki/graph_args
			'graph_args' => '--base 1000 -r --lower-limit 0'
		);
	}

	/**
	 * Graphs
	 * Reading configuration from environment variable (set in munin's plugin configuration)
	 *
	 * @return array
	 */
	protected function getGraphs() {
		$graphs = array();
		foreach (explode(',', getenv('PLACES')) as $place) {
			$place = trim($place);
			$data = $this->getData($place);
			$graphs[$place] = array(
				'label' => $data['name'],
				'info' => 'Temperatur in '. $data['name'],
				'type' => MuninPlugin::GRAPH_GAUGE,
				'draw' => MuninPlugin::DRAW_LINE1
			);
		}
		return $graphs;
	}

	/**
	 * Read data from website and extract values
	 * Caches values internally to avoid fetching website multiple times
	 *
	 * @param string $place
	 * @return data
	 */
	protected function getData($place) {
		if (!isset($this->data[$place])) {
			$html = file_get_contents('http://www.weather.com/weather/today/'.$place);

			// fetch name
			$matches = array();
			preg_match('/locName: "([\w ]+)"/', $html, $matches);
			$name = $matches[1];

			// fetch temperature
			$matches = array();
			preg_match('/realTemp: "(\d+)"/', $html, $matches);
			$fahrenheit = $matches[1];
			$celsius = round(($fahrenheit - 32) * 5/9);

			$this->data[$place] = array('name' => $name, 'temp' => $celsius);
		}
		return $this->data[$place];
	}

	/**
	 * Get values
	 *
	 * @return array
	 */
	protected function _getValues() {
		$values = array();
		foreach (explode(',', getenv('PLACES')) as $place) {
			$data = $this->getData($place);
			$values[$place] = $data['temp'];
		}
		return $values;
	}

}

$plugin = new WetterMunin($argv);
$plugin->process($argv);
