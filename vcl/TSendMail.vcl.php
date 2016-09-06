<?
if( !defined('IN') ) die('bad request');

class TSendMail
{
	public $smtpAccount = 'kwservice@sina.com';
	public $smtpPassword = 'Kw123456';
	public $smtpServer = 'smtp.sina.com';
	public $smtpPort = 25;
	public $Message = '';
	public $AttachFile;

	public function Send($to, $subject, $body)
	{
		$mail = new SaeMail();
		if($this->AttachFile){
			//$mail->setAttach( array( 'my_photo' => '照片的二进制数据' ) );
			$mail->setAttach( $this->AttachFile );
		}
		$ret = $mail->quickSend( $to ,$subject, $body,
			$this->smtpAccount , $this->smtpPassword ,
			$this->smtpServer , $this->smtpPort );
		//发送失败时输出错误码和错误信息
		if($ret){
			$this->Message = '发送成功！';
		}else{
            $this->Message = $mail->errno() . ': '. $mail->errmsg();
		}
		return $ret;
	}
}
?>