<?php

namespace Logger;

use Workerman\Worker;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

/**
 * Logger/Server.
 *
 * @author 不再迟疑
 */
class Server {
	private $logerlist = array ();
	public $dir = __DIR__;
	public $logger_level = Logger::DEBUG;
	/**
	 * Construct.
	 *
	 * @param string $ip        	
	 * @param int $port        	
	 */
	public function __construct($address) {
		$worker = new Worker ( "frame://$address" );
		$worker->count = 2;
		$worker->name = 'LoggerServer';
		$worker->onMessage = array (
				$this,
				'onMessage' 
		);
		$worker->onClose = array (
				$this,
				'onClose' 
		);
	}
	
	/**
	 * onClose
	 *
	 * @return void
	 */
	public function onClose($connection) {
	}
	
	/**
	 * onMessage.
	 *
	 * @param TcpConnection $connection        	
	 * @param string $data        	
	 */
	public function onMessage($connection, $data) {
		if (! $data) {
			return;
		}
		$data = unserialize ( $data );
		$logger = $this->getLoger ( $data ['logger_name'] );
		$logger->addRecord ( $data ['logger_level'], $data ['logger_message'] );
	}
	private function getLoger($loger_name) {
		if (! array_key_exists ( $loger_name, $this->logerlist )) {
			$stream = new StreamHandler ( $this->dir . "/$loger_name.log", $this->logger_level );
			$stream->setFormatter ( new LineFormatter () );
			$logger = new Logger ( $loger_name );
			$logger->pushHandler ( $stream );
			$this->logerlist [$loger_name] = $logger;
		}
		return $this->logerlist [$loger_name];
	}
}
