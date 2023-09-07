<?php

namespace app\fsa\action;

use app\fsa\model\AssociationModel;
use app\fsa\model\HostModel;
use app\fsa\model\InstructorModel;
use app\fsa\model\LectureModel;
use app\fsa\model\TagDataunitModel;
use app\fsa\model\TagFormModel;
use app\fsa\model\TagModel;
use app\fsa\model\TagRoleModel;

class LectureAction
{
    protected $assoc;

    public function import_model(array $excel, int $aid)
    {
        $this->assoc = AssociationModel::where('id', $aid)->find();
        if (!$this->assoc) {
            throw new \Error("aid不存在");
        }
        switch ($this->assoc->import_type) {
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
        $ins_data = null;
        foreach ($excel as $value) {
            $StartDate = $value['活动开始时间'];
            $num = $value['参与人数'];
            $role_name = $value['对象标签'];
            $form_name = $value['形式标签'];
            $phone = $value['手机号码'];
            $title = $value['活动主题'];
            $type = $value['活动类别'];
            $name = $value['主讲人姓名'];
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

            $Duration = 7200;
            $iid = 0;
            if (strlen($phone) < 5) {
                throw new \Error("手机号不能为空");
            }
            if (strlen($name) < 1) {
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
            if (!$ins_data) {
                $ins_data = InstructorModel::where('phone', $phone)->find();
            }
            if (!$ins_data) {
                continue;
//                $ins_data = InstructorModel::create([
//                    "aid" => $this->assoc['id'],
//                    "name" => $name,
//                    "phone" => $phone,
//                    "status" => 1,
//                ])->find();
            }
            $host = HostModel::where("name", $HostName)->find();
            if (!$host) {
                $host = HostModel::create([
                    "name" => $HostName,
                    "aid" => $this->assoc->aid,
                ]);
            }
            $tag_ids = TagModel::whereIn("name", [$TagDataunits1, $TagDataunits2])->column("id");
            $tag_dataunit_ids = TagDataunitModel::whereIn("name", [$TagDataunits1, $TagDataunits2])->column("id");
            $tag_role_ids = TagRoleModel::where("name", $role_name)->column("id");
            if (empty($tag_role_ids)) {
                TagRoleModel::create([
                    "aid" => $this->assoc->aid,
                    "is_show" => 1,
                    "name" => $role_name,
                ]);
            }
            $tag_form_ids = TagFormModel::where("name", $form_name)->column("id");
            if (empty($tag_form_ids)) {
                TagFormModel::create([
                    'aid' => $this->assoc['aid'],
                    'is_show' => 1,
                    'name' => $form_name,
                ]);
            }
            $lec = LectureModel::where("iid", $iid)
                ->where("hid", $host->id)
                ->where("start_date", $StartDate)
                ->find();
            if ($lec) {
                LectureModel::where("id", $lec->id)
                    ->data([
                        'aid' => $this->assoc->aid,
                        'iid' => $iid,
                        'hid' => $host->id,
                        'title' => $title,
                        'tag_ids' => tag_ids,
                        'tag_dataunit_ids' => tag_dataunit_ids,
                        'trid' => trid,
                        'tfid' => tfid,
                        'start_date' => start_date,
                        'duration' => duration,
                        'type' => Type,
                        'province' => province,
                        'city' => city,
                        'district' => district,
                        'street' => street,
                        'can_gift' => can_gift,
                        'gift_ids' => gift_ids,
                        'visitor' => visitor,
                    ])
            }
        }
    }
}