<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use app\index\model\Book as BookModel;

class Book extends Controller
{
    public function index(){
        if(empty(session('username')))
        {
            $this->error('请先登录','adminlogin/index');
        }
        $books=BookModel::all();
        $this->assign('books',$books);
        return $this->fetch();
    }
    
    public function bookadd(){
        return $this->fetch();
    }
    
    public function addBook(Request $request){
        $bookID=$request->param('bookID');
        if(empty($bookID)){
            $this->error('请填写书本编号');
        }
        $flower1=BookModel::get($bookID);
        if(!empty($flower1)){
            $this->error('您填写鲜花编号已存在！');
        }
        $book=new BookModel();
        $book->bookID=$bookID;
        $book->bname=$request->param('bname');
        $book->bclass=$request->param('bclass');
        $book->shuoming=$request->param('shuoming');
        $book->price=$request->param('price');
        $book->yourprice=$request->param('yourprice');
        $book->tejia=$request->param('tejia');
        $book->SelledNum=$request->param('SelledNum');
    
        $pictures=$request->file('pictures');
        if	(empty($pictures)){
            $this->error('请选择上传文件');
        }
        $info=$pictures->validate(['ext'=>'jpg,png'])->move(ROOT_PATH.'public/static'.DS.'picture','');
        $book->pictures=$info->getSaveName();
        
        $book->save();
        $this->success('添加成功！','book/index');
    }
    
    public function bookDelete(){
        $book=BookModel::get(input('get.bookID'));
        $book->delete();
        $this->redirect('book/index');
    }
    
    public function bookupdate(){
        $book=BookModel::get(input('get.bookID'));
        $this->assign('book',$book);
        return $this->fetch();
    }
    
    public function updateBook(Request $request){
        $book=BookModel::get(input('post.bookID'));
        $book->bname=$request->param('bname');
        $book->bclass=$request->param('bclass');
        $book->shuoming=$request->param('shuoming');
        $book->price=$request->param('price');
        $book->yourprice=$request->param('yourprice');
        $book->tejia=$request->param('tejia');
        $book->SelledNum=$request->param('SelledNum');
         
        $pictures=$request->file('pictures');
        if(!empty($pictures)){
            $info=$pictures->validate(['ext'=>'jpg,png'])->move(ROOT_PATH.'public/static'.DS.'picture','');
            $book->pictures=$info->getSaveName();
        }
        $book->save();
        $this->success('修改成功！','book/index');
    }
    
}

