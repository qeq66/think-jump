<?php

/**
 * 用法：
 * class index
 * {
 *     use qeq66\think\Jump;
 *     public function index(){
 *         $this->error();
 *         $this->redirect();
 *     }
 * }
 */
namespace qeq66\think;

use think\Container;
use think\exception\HttpResponseException;
use think\Response;

trait Jump
{
    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param  mixed     $msg 提示信息
     * @param  string    $url 跳转的URL地址
     * @param  mixed     $data 返回的数据
     * @param  integer   $wait 跳转等待时间
     * @param  array     $header 发送的Header信息
     * @return void
     */
    protected function success($msg = '', $url = null, $data = '', $wait = 3, array $header = [])
    {
        if (is_null($url) && isset($_SERVER["HTTP_REFERER"])) {
            $url = $_SERVER["HTTP_REFERER"];
        } elseif ('' != $url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : $this->app['route']->buildUrl($url)->build();
        }

        $result = [
            'code' => 1,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];

        $type = $this->getResponseType();
 
         if ('html' == strtolower($type)) {
            $this->app['view']->assign($result);
            return $this->app['view']->fetch($this->app['config']->get('jump.dispatch_error_tmpl'));
        }

        $response = Response::create($result, $type)->header($header)->options(['jump_template' => $this->app['config']->get('dispatch_success_tmpl')]);

        throw new HttpResponseException($response);
    }

    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param  mixed     $msg 提示信息
     * @param  string    $url 跳转的URL地址
     * @param  mixed     $data 返回的数据
     * @param  integer   $wait 跳转等待时间
     * @param  array     $header 发送的Header信息
     * @return void
     */
    protected function error($msg = '', $url = null, $data = '', $wait = 3, array $header = [])
    {
        $type = $this->getResponseType();
        if (is_null($url)) {
            $url = $this->app['request']->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ('' != $url) {
            $url = (strpos($url, '://') || 0 === strpos($url, '/')) ? $url : $this->app['route']->buildUrl($url)->build();
        }

        $result = [
            'code' => 0,
            'msg'  => $msg,
            'data' => $data,
            'url'  => $url,
            'wait' => $wait,
        ];

        if ('html' == strtolower($type)) {
            $this->app['view']->assign($result);
            return $this->app['view']->fetch($this->app['config']->get('jump.dispatch_error_tmpl'));
        }

        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * 返回封装后的API数据到客户端
     * @access protected
     * @param  mixed     $data 要返回的数据
     * @param  integer   $code 返回的code
     * @param  mixed     $msg 提示信息
     * @param  string    $type 返回数据格式
     * @param  array     $header 发送的Header信息
     * @return void
     */
    protected function result($data, $code = 0, $msg = '', $type = 'json', array $header = [])
    {
        $result = [
            'code' => $code,
            'msg'  => $msg,
            'time' => time(),
            'data' => $data,
        ];

        $response = Response::create($result, $type)->header($header);

        throw new HttpResponseException($response);
    }

    /**
     * URL重定向
     * @access protected
     * @param  string         $url 跳转的URL表达式
     * @param  integer        $code http code
     * @param  array          $with 隐式传参
     * @return void
     */
    protected function redirect($url, $code = 302)
    {

        $response = Response::create($url, 'redirect', $code);

        throw new HttpResponseException($response);
    }

    /**
     * 获取当前的response 输出类型
     * @access protected
     * @return string
     */
    protected function getResponseType()
    {
        if (!$this->app) {
            $this->app = Container::get('app');
        }

        $isAjax = $this->app['request']->isAjax();

        return $isAjax ? 'json' : 'html';
    }
}
