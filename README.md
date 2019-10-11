# think-jump

基于thinkphp6的跳转扩展

## 安装

~~~php
composer require qeq66/think-jump
~~~

## 用法示例

在所需控制器内引用该扩展：
~~~php
<?php
namespace app\controller;

use app\BaseController;

class Index extends BaseController
{
  	use \qeq66\think\Jump;
    public function index()
    {
      return $this->error('错误');
      return $this->success('正确');
      return $this->redirect('index/hello');
      return $this->result(['username' => 'qeq66', 'content' => '输出json']);
    }
}

~~~
