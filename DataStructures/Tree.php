<?php

/**
 * Created by PhpStorm.
 * User: jifei
 * Date: 15/2/15
 * Time: 下午1:21
 */
class Tree
{
    private $original_list;//原始数据
    private $selected_keys;//选中的key
    public $pk;//pk
    public $parent_key;//上级ID
    public $children_key;//存放子孙的key名

    function __construct($pk = 'id', $parent_key = 'pid', $children_key = 'children')
    {
        if (!empty($pk) && !empty($parent_key) && !empty($children_key)) {
            $this->pk           = $pk;
            $this->parent_key   = $parent_key;
            $this->children_key = $children_key;
        }
    }

    /**
     * 装载数据
     *
     * @param       $data          完整数据
     * @param null  $allowed_data  有权限的数据
     * @param array $selected_keys 选中的key
     */
    public function load($data, $allowed_data = null, $selected_keys = array())
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $this->original_list[$v[$this->pk]] = $v;
            }
            if (is_array($allowed_data) && !empty($allowed_data)) {
                foreach ($allowed_data as $k => $v) {
                    $this->allowFromChild($v);
                }
            }
            $this->selected_keys = $selected_keys;
        }
    }

    /**
     * 建立树结构
     *
     * @param int $root
     *
     * @return array|bool
     */
    public function buildTree($root = 0)
    {
        if (!$this->original_list) {
            return false;
        }
        $original_list = $this->original_list;
        $tree          = array();//result
        $refer         = array();//主键和单元引用
        foreach ($original_list as $k => $v) {
            if (!isset($v[$this->pk]) || !isset($v[$this->parent_key]) || isset($v[$this->children_key])) {
                unset($original_list[$k]);
                continue;
            }
            //选中
            if (in_array($v[$this->pk], $this->selected_keys)) {
                $original_list[$k]['state']['selected'] = true;
            }
            $refer[$v[$this->pk]] =& $original_list[$k];
        }

        foreach ($original_list as $k => $v) {
            if ($v[$this->parent_key] == $root) {//根直接添加到树中
                $tree[] =& $original_list[$k];
            } else {
                if (isset($refer[$v[$this->parent_key]])) {
                    $parent                        =& $refer[$v[$this->parent_key]];//获取父分类的引用
                    $parent[$this->children_key][] =& $original_list[$k];//在父分类的children中再添加一个引用成员
                }
            }
        }

        return $tree;
    }


    /**
     * 授权
     *
     * @param $child
     */
    public function  allowFromChild($child)
    {
        if (empty($this->original_list[$child[$this->pk]]['is_allowed'])) {
            $this->original_list[$child[$this->pk]]['is_allowed'] = 1;
            if (!empty($this->original_list[$child[$this->parent_key]])) {
                $this->allowFromChild($this->original_list[$child[$this->parent_key]]);
            }
        }
    }

    /**
     * 输出树结构
     *
     * @param $tree
     */
    public function printTree($tree)
    {
        if (is_array($tree) && count($tree) > 0) {
            echo '<ul>';
            foreach ($tree as $node) {
                echo '<li>' . $node['name'];
                if (!empty($node['children']))
                    $this->printTree($node['children']);
                echo '</li>';
            }
            echo '</ul>';
        }
    }


    /**
     * 输出允许的树结构
     *
     * @param $tree
     */
    public function printAllowedTree($tree)
    {
        if (is_array($tree) && count($tree) > 0) {
            echo '<ul>';
            foreach ($tree as $node) {
                if (empty($node['is_allowed'])) {
                    continue;
                }
                echo '<li>' . $node['name'];
                if (!empty($node['children']))
                    $this->printAllowedTree($node['children']);
                echo '</li>';
            }
            echo '</ul>';
        }
    }


    //输出选择功能的树结构
    public function printSelectTree($tree)
    {
        if (is_array($tree) && count($tree) > 0) {
            echo '<ul>';
            foreach ($tree as $node) {
                $selected = !empty($node['state']['selected']) ? 'checked' : '';
                echo "<li><input type=\"checkbox\" $selected>" . $node['name'];
                if (!empty($node['children']))
                    $this->printSelectTree($node['children']);
                echo '</li>';
            }
            echo '</ul>';
        }
    }
}


//demo
//所有数据
$data          = [
    ['id' => 1, 'pid' => 0, 'name' => 'a'],
    ['id' => 2, 'pid' => 0, 'name' => 'b'],
    ['id' => 3, 'pid' => 0, 'name' => 'c'],
    ['id' => 4, 'pid' => 2, 'name' => 'd'],
    ['id' => 5, 'pid' => 1, 'name' => 'e'],
    ['id' => 6, 'pid' => 5, 'name' => 'f'],
    ['id' => 7, 'pid' => 5, 'name' => 'g'],
    ['id' => 8, 'pid' => 5, 'name' => 'h'],
    ['id' => 9, 'pid' => 4, 'name' => 'i'],
    ['id' => 10, 'pid' => 9, 'name' => 'j'],
    ['id' => 11, 'pid' => 0, 'name' => 'k']];

//有权限查看的数据
$allowed_data  = [
    ['id' => 1, 'pid' => 0, 'name' => 'a'],
    ['id' => 2, 'pid' => 0, 'name' => 'b'],
    ['id' => 10, 'pid' => 9, 'name' => 'j']
];

//选中的数据
$selected_keys = [1, 2, 9];
$tree          = new Tree();

$tree->load($data, $allowed_data, $selected_keys);

echo "<style>li{list-style-type:none;}</style>";
echo "完整树:<br/>";
$tree->printTree($tree->buildTree());
echo "------------------------------------------------------<br/>";
echo "有权限的树:<br/>";
$tree->printAllowedTree($tree->buildTree());
echo "------------------------------------------------------<br/>";
echo "有选择功能的树:<br/>";
$tree->printSelectTree($tree->buildTree());
