<?php
/**
 * User: jifei
 * Date: 2013-07-30
 * Time: 23:12
*/
/**
 * 双向队列
 * 双端队列（deque，全名double-ended queue）是一种具有队列和栈性质的数据结构。
 * 双端队列中的元素可以从两端弹出，插入和删除操作限定在队列的两边进行。
 */
class Deque
{
    public $queue=array();

    /**
     * 构造函数初始化队列
     */
    public function __construct($queue=array())
    {
        if(is_array($queue))
        {
            $this->queue=$queue;
        }
    }

    /**
     * 获取第一个元素
     */
    public function front()
    {
        return reset($this->queue);
    }

    /**
     * 获取最后一个元素
     */
    public function back()
    {
        return end($this->queue);
    }

    /**
     * 判断是否为空
     */
    public function is_empty()
    {
       return empty($this->queue);
    }

    /**
     * 队列大小
     */
    public function size()
    {
       return count($this->queue);
    }

    /**
     * 插入到尾
     */
    public function push_back($val)
    {
        array_push($this->queue,$val);
    }

    /**
     * 插入到头
     */
    public function push_front($val)
    {
       array_unshift($this->queue,$val);
    }

    /**
     * 移除最后一个元素
     */
    public function pop_back()
    {
       return array_pop($this->queue);
    }

    /**
     * 移除第一个元素
     */
    public function pop_front()
    {
        return array_shift($this->queue);
    }

    /**
     * 清空队列
     */
    public function clear()
    {
        $this->queue=array();
    }
}

//初始化一个双向队列
$deque=new Deque(array(1,2,3,4,5));
echo $deque->size().PHP_EOL;
echo $deque->is_empty().PHP_EOL;
echo $deque->front().PHP_EOL;
echo $deque->back().PHP_EOL;
echo PHP_EOL;
//弹出元素测试
echo $deque->pop_back().PHP_EOL;
echo $deque->pop_front().PHP_EOL;
echo $deque->size().PHP_EOL;
echo PHP_EOL;
$deque->push_back('a').PHP_EOL;
$deque->push_front(0).PHP_EOL;
echo PHP_EOL;
//插入测试
echo $deque->front().PHP_EOL;
echo $deque->back().PHP_EOL;
echo $deque->size().PHP_EOL;
echo PHP_EOL;
//清空测试
$deque->clear();
echo $deque->is_empty();