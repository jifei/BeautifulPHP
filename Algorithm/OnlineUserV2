<?php
/**
 * User: jifei
 * Date: 2016/1/25
 * Time: 11:24
 *
 * 每分钟百万用户,实时统计最近15分钟在线用户总数V2版本
 * 使用redis的bitmap数据结构,每分钟一个bitmap，用户ID对应bitmap的位数
 * 计算指定时间段内的用户数,先对不同时间bitmap进行或(OR)运算，再bitcount统计在线数
 * bitmap数据结构非常节省内存，运算速度非常快。改造后内存占用仅为之前的1/70，统计速度为1/50
 * 优化:当前分钟的数据有可能正在更新，并发量大的情况下，应该先复制到临时的bitmap，计算时用临时bitmap
 */
class OnlineUser
{
	public $prefix_key = "online";//key前缀
	public function __construct()
	{
		$this->redis = new Redis();
		$this->redis->connect('127.0.0.1');
	}
	/**
	 * bitmap添加新的在线用户
	 *
	 * @param $uid
	 */
	public function addUser($uid)
	{
		$this->redis->setBit($this->prefix_key . date('Hi'), $uid,1);
	}
	/**
	 * 获取在线用户数
	 *
	 * @param $start_min  统计开始分钟 Hi格式
	 * @param $end_min    统计结束的分钟
	 *
	 * @return int
	 */
	public function userNum($start_min, $end_min)
	{
		//第一个参数操作类型OR
		$params[] = 'OR';
		//第二个参数存放运算结果的临时bitmap key
		$params[] = $this->prefix_key . $start_min . '_' . $end_min;
		//遍历时间区间内所有的分钟,并放入到参数中
		for ($min = $start_min; $min < $end_min; $min++) {
			$params[] = $this->prefix_key . $min;
		}
		call_user_func_array([$this->redis, "bitOp"], $params);
		$num = $this->redis->bitCount($params[1]);
		//删除临时bitmap
		$this->redis->delete($params[1]);
		return $num;
	}
}
