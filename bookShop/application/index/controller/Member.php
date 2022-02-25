<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Member;
class Member extends Controller
{
    public function editMember(){
        $email = session('email');
        if(empty($email)){
            $this->error('请先登录!','login/login');
        }
        $name = input('post.name');
        $mobile = input('post.phone');
        $member = Member::get($email);
        $member->name=$name;
        $member->mobile=$mobile;
        $member->save();
    }
    
}

