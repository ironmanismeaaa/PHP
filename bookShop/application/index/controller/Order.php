<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\Customer;
use app\index\model\Member;
use app\index\model\Myorder;
use app\index\model\Cart;
use app\index\model\Shoplist;
use app\index\model\Showorder;
use app\index\model\Showshoplist;
use app\index\model\Book;
class Order extends Controller
{
    
    public function order(){
        if(empty(session('email'))){
            $this->error('请先登录!','login/login');
        }
        $data = Customer::where('email',session('email'))->select();
        $this->assign('Customers',$data);
   //     dump($data);
        $mem =Member::get(session('email'));
        $this->assign('member',$mem);
        
        $cartIDs=input('post.cartID/a');
        session('cartIDs', $cartIDs);
        
     //   dump($cartIDs);
        $vcart=Db::table('vcart')->where('cartID','in',$cartIDs)->select();
        $this->assign('vcart',$vcart);
        
        return $this->fetch();
    }
    public function addOrder(){
        
  
        // 事务处理开始
        Db::transaction(function () {
            // (1)添加订单信息到myorder表
            // (1.1)新建myorder对象，并将获取的表单值绑定到对应的属性。
        
            $order = new Myorder();
            $order->email = session('email');
            $order->custID = input('post.custID');
            $order->shifu = input('post.total');
        
            $order->inputtime = date("Y-m-d H:i:s");
            if(!empty(input('post.date'))){
        
                $order->peisongday = input('post.date');
            }
            if(!empty(input('post.time'))){
                $order->peisongtime = input('post.time');
            }
            if(!empty(input('post.message'))){
                $order->message = input('post.message');
            }
            if(!empty(input('post.buy_name'))){
                $order->buy_name = input('post.buy_name');
            }
            if(!empty(input('post.pay_with'))){
                $order->pay_with = input('post.pay_with');
            }
        
            $order->status = '未付款';
            $order->cltime = $order->inputtime;
        
            // (1.2)添加订单
             
            $order->save();
        
             
            // (1.3)查找新添加的订单编号
            $sch = "email='" . session('email') . "' and inputtime='" . $order->inputtime . "'";
            $orderN = Myorder::where($sch)->find();
            $orderID = $orderN->orderID;
        
        
            // （2）将购买的商品信息及数量添加到shoplist表
            // （2.1）根据选择的商品编号查看cart表
            $cartIDs = session('cartIDs');
            $carts = Cart::where('cartID', 'in', $cartIDs)->select();
            
            // (2.2)遍历carts
            foreach ($carts as $cart) {
                // (2.3)新建shoplist表对象$shoplist
                $shoplist = new Shoplist();
                // (2.4)绑定orderID、email、flowerID、num属性属性
                $shoplist->orderID = $orderID;
                $shoplist->bookID = $cart->bookID;
                $shoplist->email = session('email');
                $shoplist->num = $cart->num;
                // (2.5) 添加到shoplist表
                $shoplist->save();
                // (3)根据购物车中的flowerID查找flower表，将其销售数量+num
                $book = Book::get($cart->bookID);
                $book->save();
                // (4) 在购物车中删除对于的商品
                $cart->delete();
            }
        });
            return "success";
    }
    public function  showorder()
    {
        if(empty(session('email'))){
            $this->error('请先登录','login/index');
        }
        $orders = Showorder::where('email', session('email'))->order('orderID desc')->paginate(3);
        $page = $orders->render();
        $this->assign('showorder', $orders);
        $this->assign('page', $page);
        //var_dump($orders);
        $orderlists = array();
        foreach ($orders as $order) {
            //var_dump($order->orderID);
            $shoplistitems = array();
            foreach ($order->showshoplist as $shoplist) {
                if($order->orderID ==  $shoplist->orderID){
                    array_push($shoplistitems, $shoplist);
                }
            }
            array_push($orderlists, $shoplistitems);
        }
        //var_dump($orderlists);
        $this->assign('orderlists', $orderlists);
        return $this->fetch();
    }
    public function pay(){
        $orderID = input('get.id');
        $order = Myorder::get($orderID);
        $order->status='已付款';
        $order->cltime = date("Y-m-d H:i:s");
        $order->save();
        $this->redirect('order/showorder');
    }
    public function delete()
    {
        // 事务处理开始
        Db::transaction(function () {
            $orderID = input('post.orderID');
            $shoplists = Shoplist::where('orderID', $orderID)->select();
            foreach ($shoplists as $shoplist) {
                $bookID = $shoplist->bookID;
                $num = $shoplist->num;
                $book = Book::get($bookID);
                $book->save();
                $shoplist->delete();
            }
            $order = Myorder::get($orderID);
            $order->delete();
        });
            return "success";
    }
    public function orderUpdate(){
        $orderID=input('get.orderID/d');
        $order=Myorder::get($orderID);
        $order->status='未评价';
        $order->cltime=date('Y-m-d H:i:s');
        $order->save();
        $this->redirect('order/showorder');
    }
    public function evaluate(){
        $orderID = input('get.orderID/d');
        $data = Db::table('showshoplist')->where('orderID', $orderID)->select();
        $this->assign('results', $data);
        return $this->fetch();
    }
    public function doEvaluate(Request $request){
        $orderID = input('post.orderID/d');
        $datas = Shoplist::where('orderID', $orderID)->select();
        foreach ($datas as $shoplist) {
            $SLID=$shoplist->SLID;
            $shoplist->email = session('email');
            $shoplist->pjstar = $request->param('pjstar'.$SLID);
            $shoplist->pjcontent = $request->param('pjcontent'.$SLID);
            $shoplist->pjip = $request->ip();
            $shoplist->pjtime = date('Y-m-d H:i:s');
            $shoplist->save();
        }
        $order = Myorder::get($shoplist->orderID);
        $order->status = '已评价';
        $order->cltime = date('Y-m-d H:i:s');
        $order->save();
        $this->redirect('order/showorder');
    }
}

