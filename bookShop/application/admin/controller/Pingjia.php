<?php
namespace app\admin\controller;

use think\Controller;
use app\index\model\Book;
use app\index\model\Shoplist;

use think\Request;
use app\index\model\Book as BookModel;
use app\index\model\Shoplist as shoplistModel;

class Pingjia extends Controller
{
    public function pingjia(){
        if(empty(session('username'))){
            $this->error('请先登录!','adminlogin/login');
        }
        
        $books=BookModel::all();
        $this->assign('books',$books);
        return $this->fetch('pingjia');
    }
    
    public function checkpingjia(){
        if(empty(session('username'))){
            $this->error('请先登录!','adminlogin/login');
        }
        $bookID = input('get.bookID');
        $book=Book::get($bookID);
        $this->assign('book',$book);
        $shoplists=Shoplist::where("bookID='".$bookID."' and pjstar is not null")->select();
        $this->assign('shoplists',$shoplists);
        return $this->fetch('checkpingjia');
    }
    
    public function deletepingjia(){
        $shoplist=shoplistModel::get(input('get.SLID'));
        $shoplist->delete();
        $books=BookModel::all();
        $this->assign('books',$books);
        return $this->fetch('pingjia');
    }
}

