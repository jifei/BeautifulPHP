<?php
/**
 *
 * User: jifei
 * Date: 2013-07-31
 * Time: 23:19
 */

/**
 * Class Singleton
 * 单例模式，也叫单子模式，是一种常用的软件设计模式。在应用这个模式时，单例对象的类必须保证只有一个实例存在，
 * 充分体现了 DRY（Don't Repeat Yourself）的思想。
 *
 * 实现单例模式的思路是：一个类能返回对象一个引用(永远是同一个)和一个获得该实例的方法（必须是静态方法，通常使用getInstance这个名称）；
 * 当我们调用这个方法时，如果类持有的引用不为空就返回这个引用，如果类保持的引用为空就创建该类的实例并将实例的引用赋予该类保持的引用；
 * 同时我们还将该类的构造函数定义为私有方法，这样其他处的代码就无法通过调用该类的构造函数来实例化该类的对象，只有通过该类提供的静态方法来得到该类的唯一实例。
 *
 * 应用场景：适用于一个类只有一个实例的场景。数据库连接，日志记录，购物车
 * 缺点：PHP运行是页面级别的，无法直接实现跨页面的内存数据共享。
 */
class Singleton
{
    //保存类实例的私有的静态成员变量
    private static $_instance;

    //私有的构造方法
    private function __construct()
    {
        echo 'This is a Constructed method;';
    }

    //创建一个空的私有__clone方法防止对象被克隆
    private function __clone()
    {
    }

    //单例方法，用于获取唯一的实例对象
    public static function getInstance()
    {
        if (!(self::$_instance instanceof self)) {
            //instanceof用于检测对象与类的从属关系,is_subclass_of对象所属类是否类的子类
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    //测试
    public function test()
    {
        echo 123;
    }
}

$a = Singleton::getInstance();
$a->test();
echo PHP_EOL;
$b = Singleton::getInstance(); //第二次调用时不执行构造方法
$b->test();
echo PHP_EOL;
//$c=new Singleton();由于构造方法私有，这个会报错的
//$d=clone $a;克隆对象报错
