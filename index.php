<?php
define( 'IN' , true );
define( 'ROOT' , dirname( __FILE__ ) . '/' );
define( 'VCL' , ROOT . 'vcl/'  );
define( 'BIN' , ROOT . 'bin/'  );

//define( 'DEBUG' , true  );

Session_start();

require(VCL.'vcl.delphi.php');
require(BIN.'app.config.php');

$m = isset($_GET['m']) ? $_GET['m'] : 'TFrmMain';
$class_name = $m; 
if( ! class_exists( $class_name ) ){
	$mod_file = BIN . $class_name .'.class.php';
	if( !file_exists( $mod_file ) )
		die('Can\'t find controller file - ' . $m . '.class.php');
	require( $mod_file );
}
if( class_exists( $class_name ) ){
	$Session = new TWebSession();
	$mainface_class = 'TScreen' . $Session->ScreenWidth;
	$mainface = new $mainface_class;
	$o = new $class_name;
	if($o){
		$a = 'Execute';
		if( method_exists( $o , $a ) ){
			call_user_func( array( $o , $a ) );
		}
		else{
			die('Can\'t find method - '   . $a . ' ');
		}
	}
}
else{
	die('Can\'t find class - ' . $class_name);
}
?>
