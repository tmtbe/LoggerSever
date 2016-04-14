<?php

namespace Logger;

use Workerman\Lib\Timer;
use Workerman\Connection\AsyncTcpConnection;

/**
 * Logger/Client
 * 
 * @author 不再迟疑
 */
class Client {
	/**
	 * Detailed debug information
	 */
	const DEBUG = 100;
	
	/**
	 * Interesting events
	 *
	 * Examples: User logs in, SQL logs.
	 */
	const INFO = 200;
	
	/**
	 * Uncommon events
	 */
	const NOTICE = 250;
	
	/**
	 * Exceptional occurrences that are not errors
	 *
	 * Examples: Use of deprecated APIs, poor use of an API,
	 * undesirable things that are not necessarily wrong.
	 */
	const WARNING = 300;
	
	/**
	 * Runtime errors
	 */
	const ERROR = 400;
	
	/**
	 * Critical conditions
	 *
	 * Example: Application component unavailable, unexpected exception.
	 */
	const CRITICAL = 500;
	
	/**
	 * Action must be taken immediately
	 *
	 * Example: Entire website down, database unavailable, etc.
	 * This should trigger the SMS alerts and wake you up.
	 */
	const ALERT = 550;
	
	/**
	 * Urgent alert.
	 */
	const EMERGENCY = 600;
	/**
	 * onMessage.
	 * 
	 * @var callback
	 */
	public static $onMessage = null;
	
	protected static $address = null;
	
	/**
	 * Connction to channel server.
	 * 
	 * @var TcpConnection
	 */
	protected static $_remoteConnection = null;
	
	/**
	 * Reconnect timer.
	 * 
	 * @var Timer
	 */
	protected static $_reconnectTimer = null;
	
	/**
	 * Ping timer.
	 * 
	 * @var Timer
	 */
	protected static $_pingTimer = null;
	
	/**
	 * Ping interval.
	 * 
	 * @var int
	 */
	public static $pingInterval = 25;
	
	/**
	 * Connect to channel server
	 * 
	 * @param string $ip        	
	 * @param int $port        	
	 * @return void
	 */
	public static function connect($address='127.0.0.1:2207') {
		if (! self::$_remoteConnection) {
			self::$address = $address;
			self::$_remoteConnection = new AsyncTcpConnection ( "frame://$address" );
			self::$_remoteConnection->onClose = 'Logger\Client::onRemoteClose';
			self::$_remoteConnection->onConnect = 'Logger\Client::onRemoteConnect';
			self::$_remoteConnection->connect ();
			
			if (empty ( self::$_pingTimer )) {
				self::$_pingTimer = Timer::add ( self::$pingInterval, 'Logger\Client::ping' );
			}
		}
	}
	
	/**
	 * Ping.
	 * 
	 * @return void
	 */
	public static function ping() {
		if (self::$_remoteConnection) {
			self::$_remoteConnection->send ( '' );
		}
	}
	/**
	 * onRemoteConnect.
	 *
	 * @return void
	 */
	public static function onRemoteConnect() {
		self::log('test', self::NOTICE, 'test');
	}
	/**
	 * onRemoteClose.
	 * 
	 * @return void
	 */
	public static function onRemoteClose() {
		echo "Waring log connection closed and try to reconnect\n";
		self::$_remoteConnection = null;
		self::clearTimer ();
		self::$_reconnectTimer = Timer::add ( 1, 'Logger\Client::connect', self::$address);
	}
	
	/**
	 * clearTimer.
	 * 
	 * @return void
	 */
	public static function clearTimer() {
		if (self::$_reconnectTimer) {
			Timer::del ( self::$_reconnectTimer );
			self::$_reconnectTimer = null;
		}
	}
	public static function log($logger_name, $logger_level, $logger_message) {
		self::connect (self::$address);
		self::$_remoteConnection->send ( serialize ( array (
				'logger_name' => $logger_name,
				'logger_level' => $logger_level,
				'logger_message' => $logger_message 
		) ) );
		echo "Logger::[$logger_name]".$logger_message."\n";
	}
}
