<?php
namespace app\index\controller;


use think\Controller;
use think\Db;
class Index extends Controller
{
    public function index()
    {
        $bclass=Db::table('book')->distinct('true')->field('bclass')->select();
        $this->assign('bclasses',$bclass);
        
        $bname=input('post.bname');
        $bcls=input('post.bclass');
        $minprice = input('post.minprice');
        $maxprice = input('post.maxprice');
        $sellnum = input('post.sellnum');
        $price = input('post.price');
        $order = '';
        if (!empty($sellnum)){
            $order .= 'SelledNum '.$sellnum;
        }
        if (!empty($price)){
            $order .= 'yourprice '.$price;
            if (!empty($sellnum)){
                $order .= ',SelledNum '.$sellnum;
            }
        }
        
        if(empty($maxprice)){
            $maxprice=100000;
        }
        if(empty($minprice)){
            $minprice=0;
        }
        
        $searchstr = 'yourprice between '. $minprice.' and '.$maxprice;
        
        if(!empty($bcls)){
            $searchstr.=" and bclass='".$bcls ."'";
        }
        if(!empty($bname)){
            $searchstr.=" and bname like '%" . $bname . "%'";
        }
//         if(input('post.byor')=="sellnum")
        
        
       $data=Db::table('book')->where($searchstr)->order($order)->select();
        $this->assign('books',$data);
        return  $this->fetch();
        
        //return '<style type="text/css">*{ padding: 0; margin: 0; } .think_default_text{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:)</h1><p> ThinkPHP V5<br/><span style="font-size:30px">鍗佸勾纾ㄤ竴鍓� - 涓篈PI寮�鍙戣璁＄殑楂樻�ц兘妗嗘灦</span></p><span style="font-size:22px;">[ V5.0 鐗堟湰鐢� <a href="http://www.qiniu.com" target="qiniu">涓冪墰浜�</a> 鐙璧炲姪鍙戝竷 ]</span></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="ad_bd568ce7058a1091"></think>';
       // return view();
    }
    public function showbook(){
        $data=Db::table('book')->paginate(7);
//         ->order('selledNum','desc')
        $this->assign('result',$data);
        $page=$data->render();
        $this->assign("page",$page);
        return $this->fetch();
    }
}
