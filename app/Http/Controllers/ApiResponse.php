<?php

namespace App\Http\Controllers;

/*
 |--------------------------------------------------------------------------
 | 错误返回格式
 |--------------------------------------------------------------------------
 |
 */


define('INIT_ERRNO', 1);
define('INIT_ERROR', '');
define('INIT_ERRNO_KEY', 'errno');
define('INIT_ERROR_KEY', 'error');

trait ApiResponse {
	protected $_successno = 0;
	protected $_initerrno = INIT_ERRNO;
	protected $_initerror = INIT_ERROR;
	protected $_errno = INIT_ERRNO_KEY;
    protected $_error = INIT_ERROR_KEY;
    protected $_result = [
		INIT_ERRNO_KEY => INIT_ERRNO,
		INIT_ERROR_KEY => INIT_ERROR,
	];
	
	//初始化
	public function _init() {
		$this->_result[$this->_errno] = $this->_initerrno;
		$this->_result[$this->_error] = $this->_initerror;
	}

	/**
	 * 设置错误代码
	 * 
	 * @param  Int  $errno  错误代码
	 * @return Errorjson
	 */
	public function set_errno($errno) {
		$this->_result[$this->_errno] = $errno;
		return $this;
	}

	/**
	 * 设置错误信息
	 * 
	 * @param  String  $error  错误信息
	 * @return Errorjson
	 */
	public function set_error($error) {
		$this->_result[$this->_error] = $error;
		return $this;
	}

	/**
	 * 设置附加数据
	 * 
	 * @param  String  $key  数据的键名
	 * @param  String  $val  数据的键值
	 * @return Errorjson
	 */
	public function set_data($key, $val) {
		$this->_result[$key] = $val;
		return $this;
	}

	/**
	 * 获取错误代码
	 * 
	 * @return Int
	 */
	public function get_errno() {
		return $this->_result[$this->_errno];
	}

	/**
	 * 获取错误代码的键名
	 * 
	 * @return String
	 */
	public function get_errno_key() {
		return $this->_errno;
	}
	
	/**
	 * 获取错误信息
	 * 
	 * @return String
	 */
	public function get_error() {
		return $this->_result[$this->_error];
	}
	
	/**
	 * 获取错误信息的键名
	 * 
	 * @return String
	 */
	public function get_error_key() {
		return $this->_error;
	}

	/**
	 * 获取成功的代码
	 * 
	 * @return Int
	 */
	public function get_successno() {
		return $this->_successno;
	}

	/**
	 * 获取成功的错误代码
	 * 
     * @param  String  $msg  操作成功的提示
	 * @return Errorjson
	 */
	public function set_success($msg = '') {
        $this->_result[$this->_errno] = $this->_successno;
        if(!empty($msg) && empty($this->get_error())) $this->set_error($msg);
		return $this;
	}

	/**
	 * 验证结果是否正确
	 * 
	 * @return Bool
	 */
	public function is_success() {
		return $this->_result[$this->_errno] == $this->_successno;
	}

	/**
	 * 获取所有数据
	 * 
	 * @param  Bool   $clear  是否还原类的初始内容, 默认: true
	 * @return Array
	 */
	public function get_result($clear = true) {
		$result = $this->_result;
		if($clear) $this->_init();
		return $result;
	}

	/**
	 * 返回json错误, 结束运行
	 * 
	 * @echo Json
	 */
	public function die_json() {
		die(json_encode($this->get_result()));
	}
}

