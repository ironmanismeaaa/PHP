<?php
namespace app\index\controller;

use think\Controller;
use app\index\model\Book;
use app\index\model\Shoplist;
class Showbook extends Controller
{
    public function bookdetail(){
        $bookID = input('get.bookID');
        $book=Book::get($bookID);
        $this->assign('book',$book);
        $shoplists=Shoplist::where("bookID='".$bookID."' and pjstar is not null")->select();
        $this->assign('shoplists',$shoplists);
        return $this->fetch('bookdetail');
    }
    
}

