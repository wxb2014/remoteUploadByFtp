<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: pengyong <i@pengyong.info>
// +----------------------------------------------------------------------
require_once 'remoteUploadInterface.class.php';
class sftp implements remoteUploadInterface{
        
    //FTP 连接资源
    private static $link;

    private $sftp;

    private $root = '/home/ftp/';

    //FTP连接时间

    private $config = [
        'host' => '192.168.2.253',
        'user' => 'ftp',
        'port' => 22,
        'passwd' => '123456',
    ];

    public $link_time;

    //错误代码
    private $err_mess = '';
    
    //传送模式{文本模式:FTP_ASCII, 二进制模式:FTP_BINARY}
    public $mode = FTP_BINARY;

    /**
     * 连接FTP服务器
     * @param string $host    　　 服务器地址
     * @param string $username　　　用户名
     * @param string $password　　　密码
     * @param integer $port　　　　   服务器端口，默认值为21
     * @param boolean $pasv        是否开启被动模式 true 使用固定端口20 false 随机端口传输
     * @param boolean $ssl　　　　 　是否使用SSL连接
     * @param integer $timeout     超时时间　
     */
    public function start($data = []) {
        if(!self::$link){
            $this->config = array_merge($this->config,$data);
            self::$link = ssh2_connect($this->config['host'], $this->config['port']);
            $res = ssh2_auth_password( self::$link, $this->config['user'],$this->config['passwd']);
            $this->sftp = ssh2_sftp(self::$link);
        }
        register_shutdown_function(array(&$this, 'close'));
        return $res;
    }

    /**
     * 创建文件夹
     * @param string $dirname 目录名，
     */
    public function mkdir($dirname) {
        return ssh2_sftp_mkdir($this->sftp, $dirname,0777,true);
    }

    /**
     * 上传文件
     * @param string $remote_file 远程存放地址
     * @param string $local_file 本地存放地址
     */
    public function put($remote_file, $local_file) {
        $dir = pathinfo($remote_file,PATHINFO_DIRNAME);
        if(!is_dir("ssh2.sftp://{$this->sftp}/{$this->root}{$dir}")){//查看远程文件夹是否存在
            //生成远程文件夹
            $this->mkdir($dir);
        }
        return ssh2_scp_send ( self::$link ,$local_file,$remote_file);
    }
    /**
     * 删除文件夹
     * @param string $dirname  目录地址
     * @param boolean $enforce 强制删除
     */
    public function rmdir($dirname, $enforce = false) {
        // ssh 删除文件夹
        $rc = ssh2_sftp_rmdir($this->sftp, $dirname);
        return $rc;
    }

    /**
     * 删除指定文件
     * @param string $filename 文件名
     */
    public function delete($filename)
    {
        // 删除文件
        $rc = ssh2_sftp_unlink($this->sftp, $filename);
        return $rc;
    }

    //删除文件
    public function remove($remote){
        $rc  = false;
        if (is_dir("ssh2.sftp://{$this->sftp}/{$remote}")) {
            // ssh 删除文件夹
            $rc = ssh2_sftp_rmdir($this->sftp, $remote);
        } else {
            // 删除文件
            $rc = ssh2_sftp_unlink($this->sftp, $remote);
        }
        return $rc;
    }

    /**
     * 返回给定目录的文件列表
     * @param string $dirname  目录地址
     * @return array 文件列表数据
     */
    public function nlist($dirname) {
        return true;
    }

    private function exeCommon($command){
        $stream = ssh2_exec(self::$link, $command);
        stream_set_blocking($stream, true);
        $errorStream = ssh2_fetch_stream($stream, SSH2_STREAM_STDERR);
// Enable blocking for both streams
        stream_set_blocking($errorStream, true);
        $cont = stream_get_contents($errorStream);
        fclose($errorStream);
        if($cont){
            $this->err_mess = $cont;
            return false;
        }
        $res = stream_get_contents($stream);
        fclose($stream);
        return $res;
    }

    /**
     * 在 FTP 服务器上改变当前目录
     * @param string $dirname 修改服务器上当前目录
     */
    public function chdir($dirname) {
        return true;
    }

    /**
     * 获取错误信息
     */
    public function get_error() {
        return $this->err_mess;
    }

    /**
     * 关闭FTP连接
     */

    public function close() {
        return true;
    }

    // 传输数据 传输层协议,获得数据
    public function download($remote_file, $local_file){
        return ssh2_scp_recv(self::$link, $remote_file, $local_file);
    }
}