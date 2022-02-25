<?php
namespace app\index\controller;

use think\Controller;
use think\Db;
class Cart extends Controller
{
public function index(){
        if(empty(session('email'))){
            $this->error('请先登录!','login/login');
        }
        $data=Db::table('vcart')->where('email',session('email'))->select();

        $this->assign('result',$data);
        return $this->fetch();
}
    
    
    public function addCart(){
        $param = input('get.');
        if(empty(session('email'))){
            $this->error('请先登录!','login/login');
        }
        if(empty(input('get.bookID'))){
            $this->error('请选择商品!','index/index');
        }
        $data = Db::table('cart')->where('email',session('email'))->where('bookID',$param['bookID'])->find();
        if(empty($data)){
            $result = Db::execute("insert into cart(cartID,email,bookID,num) values(null,'" . session('email') . "','" .$param['bookID']. "',1)");
            //dump($result);
        }else{
         	$result=Db::execute("update cart set num=num+1 where email='" .session('email'). "' and bookID='" . $param['bookID'] . "'");
         			//dump($result);
        }
        $this->redirect(url('cart/index'));
    }
    public function clearCart(){
        
        $result=Db::execute("delete from cart where email='" .session('email'). "'");
        $this->redirect(url('cart/index'));
    }
    public function deleteCart(){
        $param = input('get.');
        $result=Db::execute("delete from cart where cartID=".$param['cartID']);
        $this->redirect(url('cart/index'));
        
    }
    public function updateCart(){
        $param = input('get.');
        $result=Db::execute("update cart set num=".$param['num']." where cartID=".$param['cartID']);
        $this->redirect(url('cart/index'));
    }
}


