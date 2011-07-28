<?php
/**
 * Abstract class for munin plugins
 *
 * @author Fabrizio Branca
 */
abstract class MuninPlugin {
	
	const GRAPH_GAUGE = 'GAUGE';
	const DRAW_LINE1 = 'LINE1';
	
	/**
	 * @var int cachetime in minutes.
	 */
	protected $cacheTime = 0;
	
	/**
	 * Process
	 * 
	 * @param array $argv
	 * @return void
	 */
	public function process($argv) {
		// logging (for debugging purposes)
		file_put_contents(sys_get_temp_dir() . '/' . get_class($this) . '.log', date('Y-m-d H:i:s') . ' ' . var_export($argv, 1) . "\n", FILE_APPEND);		

		if (isset($argv[1]) && $argv[1] == "autoconf") {
			$this->autoconf();
		} elseif (isset($argv[1]) && $argv[1] == "config") {
			$this->config();
		} else {
			$this->printValues();
		}
	}
	
	/**
	 * Autoconf
	 * 
	 * @return void
	 */
	public function autoconf() {
		echo "yes\n";
	}
	
	/**
	 * Config
	 * 
	 * @return void
	 */
	public function config() {
		foreach ($this->getSetup() as $key => $value) {
			echo "$key $value\n";
		}
		foreach ($this->getGraphs() as $graph => $setup) {
			foreach ($setup as $key => $value) {
				echo "$graph.$key $value\n";
			}
		}
	}
	
	/**
	 * Print values
	 * 
	 * @return void
	 */
	public function printValues() {
		foreach ($this->getValues() as $graph => $value) {
			echo "$graph.value $value\n";
		}
	}
	
	/**
	 * Get values.
	 * Wraps _getValues() that must be implemented in your inheriting class and takes care of caching
	 * 
	 * @return array 
	 */
	protected function getValues() {
		
		if (empty($this->cacheTime)) { // caching is disabled
			return $this->_getValues();
		}
		
		$cacheFile = sys_get_temp_dir() . '/Munin_' . get_class($this) . '_' . md5(serialize($this->getSetup())) . '_' . md5(serialize($this->getGraphs())) . '.cache';
		if (is_file($cacheFile)) {
			if (filemtime($cacheFile) >= (time() - $this->cacheTime * 60)) {
				$values = unserialize(file_get_contents($cacheFile));
				if ($values !== false) {
					return $values;
				}
			}
		}
		$values = $this->_getValues();
		file_put_contents($cacheFile, serialize($values));
		chmod($cacheFile, 0666);
		return $values;
	}
	
	/**
	 * Retrieve actual values.
	 * This must be implemented in your inheriting class
	 * 
	 * @return array
	 */
	abstract protected function _getValues();
	
	/**
	 * Get graphs.
	 * Expects information on the graphs to be displayed. 
	 * Format
	 * array(<GraphName> => array(<GraphConfiguration> => <ConfigurationValue>, ...), ... )
	 * 
	 * @return array
	 */
	abstract protected function getGraphs();
	
	/**
	 * Get setup
	 * Expects rrdtool configuration
	 * 
	 * @return array
	 */
	abstract protected function getSetup();
	
}