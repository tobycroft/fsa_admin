<?php


namespace app\fsa\model;

use think\Model;
use think\helper\Hash;
use think\Db;

/**
 * 后台用户模型
 * @package app\admin\model
 */
class InstructorModel extends Model
{
    // 设置当前模型对应的完整数据表名称
    protected $table = 'fra_instructor';

    // 设置当前模型对应的完整数据表名称

    // 自动写入时间戳
    protected $autoWriteTimestamp = true;

}
