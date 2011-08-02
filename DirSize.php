#!/usr/bin/php -q
<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MuninPlugin.php';

/**
 * Directory size plugin for munin
 *
 * - Add symlink:
 * ln -s <pathtothisfile> /etc/munin/plugins/dirsize
 *
 * - Add configuration to /etc/munin/plugin-conf.d/
 * [dirsize]
 * env.DIRS /dir1,/dir2
 *
 * @author Fabrizio Branca
 */
class DirSize extends MuninPlugin {

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
			'graph_title' => 'Directory usage',
				// @see http://munin-monitoring.org/wiki/graph_category
			'graph_category' => 'disk',
				// "second" or "minute" @see http://munin-monitoring.org/wiki/graph_period
			'graph_period' => 'minute',
				// "yes" or "no" @see http://munin-monitoring.org/wiki/graph_scale
			'graph_scale' => 'yes',
			'graph_vlabel' => 'Bytes',
				// @see http://munin-monitoring.org/wiki/graph_args
			'graph_args' => '--base 1024',
			'graph_info' => 'This graph shows the size of several directories'
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
		foreach (explode(',', getenv('DIRS')) as $dir) {
			$graphs[$this->normalizeFieldName($dir)] = array(
				'label' => $dir,
				'info' => 'Size of '. $dir,
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
		foreach (explode(',', getenv('DIRS')) as $dir) {
			list($size) = explode("\t", shell_exec('du -s '.$dir));
			$values[$this->normalizeFieldName($dir)] = $size;
		}
		return $values;
	}

}

$plugin = new DirSize($argv);
$plugin->process($argv);
