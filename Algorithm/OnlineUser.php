<?php

/**
 * Created by PhpStorm.
 * User: jifei
 * Date: 15/11/24
 * Time: 20:58
 *
 * 每分钟百万用户,实时统计最近15分钟在线用户总数
 * 使用redis的set数据结构,把用户ID或者唯一表示的其他属性存放在当前分钟的集合中
 * 计算指定时间段内的用户数,计算这些分钟集合的并集即可,性能在2s内
 *
 * 优化:集合设置过期时间,集合的key可以增加日期date('dHi'),查看在线用户数增加开始结束时间判断限制
 *
 */
class OnlineUser
{
    public $prefix_key = "online";//key前缀

    public function __construct()
    {
        $this->redis = new Redis();
    }

    /**
     * 往集合中添加新的在线用户
     *
     * @param $uid
     */
    public function addUser($uid)
    {
        $this->redis->sAdd($this->prefix_key . date('Hi'), $uid);
    }


    /**
     * 获取在线用户数
     *
     * @param $start_min  统计开始分钟 Hi格式
     * @param $end_min    统计结束的分钟
     *
     * @return mixed
     */
    public function userNum($start_min, $end_min)
    {
        //第一个参数,并集的key名称
        $params[] = $this->prefix_key . $start_min . '_' . $end_min;

        //遍历时间区间内所有的分钟,并放入到参数中
        for ($min = $start_min; $min < $end_min; $min++) {
            $params[] = $this->prefix_key . $min;
        }
        //求所有分钟的用户的并集并保存,性能比直接计算返回快很多,省去了数据传输
        $num = call_user_func_array([$this->redis, "sUnionStore"], $params);

        //删除临时并集
        $this->redis->delete($params[0]);

        return $num;
    }
}
