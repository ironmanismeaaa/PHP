<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Customer as CustomerModel;
class Customer extends Controller
{
    public function addCustomer(){
        $email=session('email');
        $sname=input('post.addName');
        $mobile=input('post.addPhone');
        $address=input('post.address');
        
        $customer=new CustomerModel();
        $customer->email = $email;
        if(!empty($sname)){
            $customer->sname=$sname;
        }
        if(!empty($mobile)){
            $customer->mobile=$mobile;
        }
        if(!empty($address)){
            $customer->address=$address;
        }
        $data=CustomerModel::where('email',session('email'))->select();
        if(!empty($data)){
            $customer->cdefault='0';
        }else{
            $customer->cdefault='1';
        }
        $customer->save();
        $search="sname='".$sname."' and email='".$email."' and mobile='".$mobile."' and address='".$address."'";
        $customer=CustomerModel::where($search)->find();
        
        return $customer->custID.' '.$customer->cdefault;
        
    }
    
    public function setDefault(){
        $custID=input('post.custID');
        $originalID=input('post.originalID');
        $old=CustomerModel::get($originalID);
        if(!empty($old)){
            $old->cdefault='0';
            $old->save();
        }
        $new=CustomerModel::get($custID);
        if(!empty($new)){
            $new->cdefault='1';
            $new->save();
        }
        return 'success';
        
    }
    
    public function deleteCustomer(){
        $custID=input('post.custID');
        $customer=CustomerModel::get($custID);
        if(!empty($customer)){
            $customer->delete();
           
        }
         return "success";
    }
    
    public function editCustomer(){
        $custID=input('post.custID');
        $sname=input('post.addName');
        $mobile=input('post.addPhone');
        $address=input('post.address');
        
        $customer=CustomerModel::get($custID);
        if(!empty($sname)){
            $customer->sname=$sname;
        }
        if(!empty($mobile)){
            $customer->mobile=$mobile;
        }
        if(!empty($address)){
            $customer->address=$address;
        }
        $customer->save();
        return "success";
        
    }
    
}

