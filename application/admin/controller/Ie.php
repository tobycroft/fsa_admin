<?php


namespace app\admin\controller;

use app\common\controller\Common;

/**
 * ie提示页面控制器
 * @package app\admin\controller
 */
class Ie extends Common
{
    /**
     * 显示ie提示
     * @return mixed
     * @author 蔡伟明 <314013107@qq.com>
     */
    public function index()
    {
        // ie浏览器判断
        if (get_browser_type() == 'ie') {
            return $this->fetch();
        } else {
            $this->redirect('admin/index/index');
        }
    }
}