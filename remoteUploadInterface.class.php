<?php

/**
 * Created by PhpStorm.
 * User: me
 * Date: 2016/7/28
 * Time: 16:41
 */
interface remoteUploadInterface
{

    function start($data);

    /**
     * 上传文件
     * @param string $remote 远程存放地址
     * @param string $local 本地存放地址
     */
    public function put($remote, $local);

    /**
     * 创建文件夹
     * @param string $dirname 目录名，
     */
    public function mkdir($dirname);

    /**
     * 删除文件夹
     * @param string $dirname  目录地址
     * @param boolean $enforce 强制删除
     */
    public function rmdir($dirname, $enforce = false);

    /**
     * 删除指定文件
     * @param string $filename 文件名
     */
    public function delete($filename);

    /**
     * 返回给定目录的文件列表
     * @param string $dirname  目录地址
     * @return array 文件列表数据
     */
    public function nlist($dirname);

    /**
     * 在 FTP 服务器上改变当前目录
     * @param string $dirname 修改服务器上当前目录
     */
    public function chdir($dirname);

    /**
     * 获取错误信息
     */
    public function get_error();

    /**
     * 关闭本资源
     */
    function close();

}