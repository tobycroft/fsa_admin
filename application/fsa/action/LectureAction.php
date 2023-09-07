<?php

namespace app\fsa\action;

use app\fsa\model\AssociationModel;
use app\fsa\model\HostModel;
use app\fsa\model\InstructorModel;
use app\fsa\model\LectureModel;
use app\fsa\model\TagDataunitModel;
use app\fsa\model\TagFormModel;
use app\fsa\model\TagRoleModel;

class LectureAction
{
    protected $association;

    public function import_model(array $excel, int $aid)
    {
        $this->association = AssociationModel::where('id', $aid)->find();
        if (!$this->association) {
            throw new \Error("aid不存在");
        }
        switch ($this->association->import_type) {
            case "a":
                $this->a($excel);

            case "gt":
                $this->gt($excel);

            case "jinjiang":
                $this->jinjiang($excel);

            default:
                break;
        }
    }

    protected function a($excel)
    {
    }

    protected function gt($excel)
    {

    }

    protected function jinjiang($excel)
    {
        $instructor = null;

        foreach ($excel as $value) {
            $StartDate = $value['活动开始时间'];
            $Visitor = $value['参与人数'];
            $role_name = $value['对象标签'];
            $form_name = $value['形式标签'];
            $phone = $value['手机号码'];
            $title = $value['活动主题'];
            $type = $value['活动类别'];
            //search in type check contain some characters
            if (str_contains($type, '线上') && str_contains($type, '线下')) {
                $type = "线上与线下";
            } elseif (str_contains($type, '线上')) {
                $type = "线上";
            } elseif (str_contains($type, '线下')) {
                $type = "线下";
            } else {
                throw new \Error("活动类型需要填写线上或线下");
            }

            $instructor_name = $value['主讲人姓名'];
            $HostName = $value['举办方名称'];
            $TagDataunits = $value['主办单位类型'];
            $City = $value['活动地点（市）'];
            $Province = $value['活动地点（省）'];
            $District = $value['活动地点（区、县）'];
            $Street = $value['活动地点（乡、镇、街道）'];
            $TagDataunits1 = $value['是否体现“晋江市家庭教育大讲堂”'];
            $TagDataunits2 = $value['是否列入妇联讲课补贴范围'];
            if ($TagDataunits1 == "是") {
                $TagDataunits1 = "晋江市家庭教育大讲堂";
            }
            if ($TagDataunits2 == "是") {
                $TagDataunits2 = "妇联讲课补贴范围";
            }

            if (strlen($phone) < 5) {
                throw new \Error("手机号不能为空");
            }
            if (strlen($instructor_name) < 1) {
                throw new \Error("姓名不能为空");
            }
            if (strlen($Province) < 1) {
                throw new \Error("省不能为空");
            }
            if (strlen($City) < 1) {
                throw new \Error("城市不能为空");
            }
            if (strlen($District) < 1) {
                throw new \Error("乡镇区不能为空");
            }
            if (!$instructor) {
                $instructor = InstructorModel::where('phone', $phone)->find();
            }
            if (!$instructor) {
                continue;
//                $ins_data = InstructorModel::create([
//                    "aid" => $this->assoc['id'],
//                    "name" => $name,
//                    "phone" => $phone,
//                    "status" => 1,
//                ])->find();
            }
            $host = HostModel::where("name", $HostName)
                ->where('aid', $this->association->id)
                ->find();
            if (!$host) {
                $host = HostModel::create([
                    "name" => $HostName,
                    "aid" => $this->association->id,
                ]);
            }
            $tag_dataunit_ids = TagDataunitModel::whereIn("name", [$TagDataunits, $TagDataunits1, $TagDataunits2])
                ->where('aid', $this->association->id)
                ->column("id");
            $tag_role_ids = TagRoleModel::whereIn("name", [$role_name])
                ->where("aid", $this->association->id)
                ->column("id");
            if (empty($tag_role_ids)) {
                TagRoleModel::create([
                    "aid" => $this->association->id,
                    "is_show" => 1,
                    "name" => $role_name,
                ]);
            }
            $tag_form_ids = TagFormModel::whereIn("name", [$form_name])->column("id");
            if (empty($tag_form_ids)) {
                TagFormModel::create([
                    'aid' => $this->association->id,
                    'is_show' => 1,
                    'name' => $form_name,
                ]);
            }
            $lecture = LectureModel::where("iid", $instructor->id)
                ->where('aid', $this->association->id)
                ->where("title", $title)
                ->where("start_date", $StartDate)
                ->find();
            if ($lecture) {
                if (!LectureModel::where("id", $lecture->id)
                    ->data([
                        'aid' => $this->association->id,
                        'iid' => $instructor->id,
                        'hid' => $host->id,
                        'title' => $title,
                        'tag_ids' => 6,
                        'tag_dataunit_ids' => implode(",", $tag_dataunit_ids),
                        'trid' => implode(",", $tag_role_ids),
                        'tfid' => implode(",", $tag_form_ids),
                        'start_date' => $StartDate,
                        'type' => $type,
                        'province' => $Province,
                        'city' => $City,
                        'district' => $District,
                        'street' => $Street,
                        'visitor' => $Visitor,
                    ])
                    ->update()) {
                    throw new \Error("数据修改失败");
                } else {
                    throw new \Error(implode(",", $tag_dataunit_ids));
                }
            } else {

            }
        }
    }
}