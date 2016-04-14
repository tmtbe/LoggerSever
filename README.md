# LoggerSever
基于workerman的日志服务器集成Monolog，采用udp上传日志，对应用性能没有影响，日志服务器支持多进程，符合psr-3日志规范。还在继续完善中。
# init
  初始化Logger  
```php
  \Logger\Client::init( $this->loggerAddress, $this->name );
```
# log
  发送日志  
  \Logger\Client::log( $logger_level, $logger_message, $logger_name='' );

# 日志等级符合Psr-3日志规范
  DEBUG：详细的debug信息  
  INFO：感兴趣的事件。像用户登录，SQL日志  
  NOTICE：正常但有重大意义的事件。  
  WARNING：发生异常，使用了已经过时的API。  
  ERROR：运行时发生了错误，错误需要记录下来并监视，但错误不需要立即处理。  
  CRITICAL：关键错误，像应用中的组件不可用。  
  ALETR：需要立即采取措施的错误，像整个网站挂掉了，数据库不可用。  
  
# 例子
```php
  use \Workerman\Worker;
  
  // 自动加载类
  require_once __DIR__ . '/../../Workerman/Autoloader.php';
  require_once __DIR__ . '/../../Logger/Autoloader.php';
  $log_server = new \Logger\Server( '0.0.0.0:2207' );
  //收集日志的级别
  $log_server->logger_level = Logger::DEBUG;
  //日志默认按天分文件
  $log_server->logger_name_dataFormat = 'Y-m-d';
  // 如果不是在根目录启动，则运行runAll方法
  if (! defined ( 'GLOBAL_START' )) {
  	Worker::runAll ();
  }
```
