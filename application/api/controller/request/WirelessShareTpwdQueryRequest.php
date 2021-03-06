<?php
/**
 * TOP API: taobao.wireless.share.tpwd.query request
 * 
 * @author auto create
 * @since 1.0, 2017.12.20
 */
namespace app\api\controller\request;
use app\api\controller\RequestCheckUtil;

class WirelessShareTpwdQueryRequest
{
	/** 
	 * 淘口令
	 **/
	private $passwordContent;
	
	private $apiParas = array();
	
	public function setPasswordContent($passwordContent)
	{
		$this->passwordContent = $passwordContent;
		$this->apiParas["password_content"] = $passwordContent;
	}

	public function getPasswordContent()
	{
		return $this->passwordContent;
	}

	public function getApiMethodName()
	{
		return "taobao.wireless.share.tpwd.query";
	}
	
	public function getApiParas()
	{
		return $this->apiParas;
	}
	
	public function check()
	{
		
		RequestCheckUtil::checkNotNull($this->passwordContent,"passwordContent");
	}
	
	public function putOtherTextParam($key, $value) {
		$this->apiParas[$key] = $value;
		$this->$key = $value;
	}
}
