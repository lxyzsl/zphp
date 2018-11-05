<?php
/**
 * User: shenzhe
 * Date: 13-6-17
 */


namespace ZPHP\Server\Adapter;

use ZPHP\Core;
use ZPHP\Server\IServer;
use ZPHP\Protocol;

class Http implements IServer
{

    public function run()
    {
        unset($_GET);
        Protocol\Request::setServer(
            Protocol\Factory::getInstance(
                Core\Config::getField('Project', 'protocol', 'Http')
            )
        );
        $router = new Url();
        $router->addRoute("/alpha:module/alpha:controller/[0-9]+?:a1a");
        $router->addRoute("/alpha:module/alpha:controller/alpha:action");
        $router->addRoute("/alpha:module/alpha:controller/alpha:action/(www[0-9]+?):id");
        $url=$_SERVER['REQUEST_URI'];
        $urls=$router->getRoute($url);
        $_GET=$urls;
        Protocol\Request::parse($urls);
        return Core\Route::route();
    }


}

class Url
{
    //定义正则表达式常量
    const REGEX_ANY="([^/]+?)";                   #非/开头任意个任意字符
    const REGEx_INT="([0-9]+?)";                  #数字
    const REGEX_ALPHA="([a-zA-Z_-]+?)";           #字母
    const REGEX_ALPHANUMERIC="([0-9a-zA-Z_-]+?)"; #任意个字母数字_-
    const REGEX_STATIC="%s";                      #占位符
    const ANY="(/[^/]+?)*";                       #任意个非/开头字符

    protected $routes=array(); #保存路径正则表达式

    #添加路由正则表达式
    public function addRoute($route)
    {
        $this->routes[]=$this->_ParseRoute($route);
    }

    /*private
      @input :$route 输入路由规则
      @output:return 返回路由正则规则
    */
    private function _ParseRoute($route)
    {
        $parts=explode("/", $route);  #分解路由规则
        $regex="@^";  #开始拼接正则路由规则
        if(!$parts[0])
        {
            array_shift($parts);  #除去第一个空元素
        }
        foreach ($parts as $part)
        {
            $regex.="/";
            $args=explode(":",$part);
            if(!sizeof($args)==2)
            {
                continue;
            }
            $type=array_shift($args);
            $key=array_shift($args);
            $this->_normalize($key);  #使参数标准化，排除其他非法符号
            $regex.='(?P<'.$key.'>';    #为了后面preg_match正则匹配做铺垫
            switch (strtolower($type))
            {
                case 'int':      #纯数字
                    $regex.=self::REGEX_INT;
                    break;
                case 'alpha':    #纯字母
                    $regex.=self::REGEX_ALPHA;
                    break;
                case 'alphanum':  #字母数字
                    $regex.=self::REGEX_ALPHANUMBERIC;
                    break;
                default:
                    $regex.=$type; #自定义正则表达式
                    break;
            }
            $regex.=")";
        }
        $regex.=self::ANY;   #其他URL参数
        $regex.='$@u';
        return $regex;
    }

    /*public,将输入的URL与定义正则表达式进行匹配
      @input  :$request 输入进来的URL
      @output :return 成功则输出规则数组数据 失败输出false
    */
    public function getRoute($request)
    {
//        echo "<pre>";
//        echo "url中getRoute中开头 routes第二个输出<br/>";
//        print_r($this->routes);
//        echo "<pre>";
        #处理request，进行参数处理,不足M、C、A,则自动补为home、index、index,即构建MVC结构URL
        $request=rtrim($request,'/');           #除去右边多余的斜杠/
        $arguments=explode('/',$request);
        $arguments=array_filter($arguments);    #除去数组中的空元素
        $long=sizeof($arguments);               #数组中的个数
        switch ($long)                          #判断个数,不足就补够
        {
            case '0':
                $request='/control/main/main';
                break;
            case '1':
                $request.='/main/main';
                break;
            case '2':
                $request.='/main';
                break;
        }
        $matches=array();                       #定义匹配后存贮的数组
        $temp=array();                          #中间缓存数组

        foreach ($this->routes as $v)           #开始匹配
        {
            preg_match($v, $request, $temp);    #需要重点理解这个数组
            $temp?$matches=$temp:'';
        }
//        echo "<pre>";
//        echo "url中getRoute中正则匹配后 routes第三个输出<br/>";
//        print_r($matches);
//        echo "<pre>";
        if($matches)                            #判断$matches是否有数据,无返回false
        {
            foreach ($matches as $key => $value) #筛选
            {
                if(is_int($key))
                {
                    unset($matches[$key]);      #除去数字key元素,保留关联元素。与上面的preg_match一起理解
                }
            }
//            echo "<pre>";
//            echo "url中getRoute中 筛选后的$ matches 第四个输出<br/>";
//            print_r($matches);
//            echo "<pre>";
            $result=$matches;
            if($long > sizeof($result))         #URL参数超过后的处理
            {
                $i=1;
                foreach ($arguments as $k => $v)
                {
                    if($k > sizeof($result))
                    {
                        if($i==1)
                        {
                            $result[$v]='';
                            $temp=$v;
                            $i=2;
                        }
                        else
                        {
                            $result[$temp]=$v;
                            $i=1;
                        }
                    }
                }
            }
            return $result;
        }
        return false;
    }
    #使参数标准化，不能存在符号，只能是a-zA-Z0-9组合
    private function _normalize(&$param)
    {
        $param=preg_replace("/[^a-zA-Z0-9]/", '', $param);
    }
}