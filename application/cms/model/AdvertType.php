<?php


namespace app\cms\model;

use think\Model as ThinkModel;

/**
 * 广告分类模型
 * @package app\cms\model
 */
class AdvertType extends ThinkModel
{
    // 设置当前模型对应的完整数据表名称
    protected $name = 'cms_advert_type';

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
}
