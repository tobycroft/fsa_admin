<?php


namespace app\cms\model;

use think\Model as ThinkModel;

/**
 * 友情链接模型
 * @package app\cms\model
 */
class Link extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'cms_link';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
}
