<?php
/**
 * Created by PhpStorm.
 * User: Hou-ShiShu
 * Date: 2018/1/23
 * Time: 10:11
 */

namespace app\common\controller;


class ReturnGoodsList
{
    /*
     * @stripGoodsListByResult      条状产品列表数据组装
     * $resource                    资源集
     * @return html                  返回组装完成的html
     * */
    static function stripGoodsListByResult($resource){
        $count=count($resource);
        $html = '';
        if(!empty($count)){
            foreach($resource as $value){
                if($value["price"]-$value["coupon_money"] < 0){
                    $coupon_after = 0;
                }else{
                    $coupon_after =$value["price"]-$value["coupon_money"];
                }
                $html .= '<div class="strip-goods">';
                $html .= '<div class="strip-thumbnail">';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value['goods_id'].'">';
                $html .='<img src="'.$value["banner"].'">';
                $html .= '</a>';
                $html .= '</div>';
                $html .= '<div class="strip-title">';
                $html .= '<img src="'.$value["type"].'">';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value["goods_id"].'"><span>'.mb_substr($value["name"],0,25,"utf-8").'</span></a>';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value["goods_id"].'"><p class="strip-goods-after">券后价：￥'.$coupon_after.'</p></a>';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value["goods_id"].'"><p class="strip-goods-before">原价：￥'.$value["price"].'</p></a>';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value["goods_id"].'"><p>销量：'.$value["sales"].'</p></a>';
                $html .= '</div>';
                $html .= '<div class="strip-vou">';
                $html .= '<p><span>券</span></p>';
                $html .= '<P>￥ '.$value["coupon_money"].'</P>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }


    /*
     * @stripGoodsListByResult      首页产品列表数据组装
     * $resource                    资源集
     * @return html                  返回组装完成的html
     * */
    static function indexGoodsListByResult($resource){
        $count=count($resource);
        $html = '';
        if(!empty($count)){
            foreach($resource as $value){
                if($value["price"]-$value["coupon_money"] < 0){
                    $coupon_after = 0;
                }else{
                    $coupon_after =$value["price"]-$value["coupon_money"];
                }
                $html .= '<div class="lists-goods">';
                $html .= '<div class="lists-thumbnail">';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value["goods_id"].'">';
                $html .= '<img src="'.$value["banner"].'">';
                $html .= '</a>';
                $html .= '</div>';
                $html .= '<div class="lists-goods-title">';
                $html .= '<img src="'.$value["type"].'">';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value["goods_id"].'"><span>'.mb_substr($value["name"],0,25,"utf-8").'</span></a>';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value["goods_id"].'"><p class="lists-goods-after">券后价：￥'.$coupon_after.'</p></a>';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value["goods_id"].'"><p class="lists-goods-before">原价：'.$value["price"].'</p></a>';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value["goods_id"].'"><p>销量：'.$value["sales"].'</p></a>';
                $html .= '</div>';
                $html .= '<div class="lists-vou">';
                $html .= '<p><span>券</span></p>';
                $html .= '<P>￥ '.$value["coupon_money"].'</P>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }

    /*
     * @stripGoodsListByResult      快捷专区产品列表数据组装
     * $resource                    资源集
     * @return html                  返回组装完成的html
     * */
    static function areaBlockGoodsListByResult($resource){
        $count=count($resource);
        $html = '';
        if(!empty($count)){
            foreach($resource as $value){
                if($value["price"]-$value["coupon_money"] < 0){
                    $coupon_after = 0;
                }else{
                    $coupon_after =$value["price"]-$value["coupon_money"];
                }
                $html .= '<div class="block-goods">';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value['goods_id'].'" class="goods-thumbnail"><img src="'.$value["banner"].'"></a>';
                $html .= '<div class="goods-title">';
                $html .= '<img src="'.$value["type"].'">';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value['goods_id'].'"><span>'.mb_substr($value["name"],0,25,"utf-8").'</span></a>';
                $html .= '</div>';
                $html .= '<div class="goods-price">';
                $html .= '<span class="price-before">券价：￥'.$coupon_after.'</span>';
                $html .= '<span class="price-after">￥'.$value["price"].'</span>';
                $html .= '<div class="price-vou">';
                $html .= '<div class="vou-title">';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value['goods_id'].'">领券</a>';
                $html .= '</div>';
                $html .= '<div class="vou-num">';
                $html .= '<a href="/index/goods/goodsDetails?id='.$value['goods_id'].'">￥ '.$value["coupon_money"].'</a>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';

            }
        }
        return $html;
    }


    /*
     * @stripGoodsListByResult      搜索产品列表数据组装
     * $resource                    资源集
     * @return html                  返回组装完成的html
     * */
    static function searchGoodsListByResult($resource){
        $count=count($resource);
        $html = '';
        if(!empty($count)){
            foreach($resource as $value){
                if($value->zk_final_price-$value->coupon_info < 0){
                    $coupon_after = 0;
                }else{
                    $coupon_after =$value->zk_final_price-$value->coupon_info;
                }
                $html .= '<div class="strip-goods">';
                $html .= '<div class="strip-thumbnail">';
                $html .= '<a href="'.$value->coupon_click_url.'">';
                $html .='<img src="'.$value->pict_url.'">';
                $html .= '</a>';
                $html .= '</div>';
                $html .= '<div class="strip-title">';
                $html .= '<img src="/static/index/images/t-logo-'.$value->user_type.'.png">';
                $html .= '<a href="'.$value->coupon_click_url.'"><span>'.mb_substr($value->title,0,25,"utf-8").'</span></a>';
                $html .= '<a href="'.$value->coupon_click_url.'"><p class="strip-goods-after">券后价：￥'.$coupon_after.'</p></a>';
                $html .= '<a href="'.$value->coupon_click_url.'"><p class="strip-goods-before">原价：￥'.$value->zk_final_price.'</p></a>';
                $html .= '<a href="'.$value->coupon_click_url.'"><p>销量：'.$value->volume.'</p></a>';
                $html .= '</div>';
                $html .= '<div class="strip-vou">';
                $html .= '<p><span>券</span></p>';
                $html .= '<P>￥ '.$value->coupon_info.'</P>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }

    /*
     * @favoritesGoodsListByResult      选品库产品列表数据组装
     * $resource                    资源集
     * @return html                  返回组装完成的html
     * */
    static function favoritesGoodsListByResult($resource){
        $count=count($resource);
        $html = '';
        if(!empty($count)){
            foreach($resource as $value){
                if($value->zk_final_price - $value->coupon_info < 0){
                    $coupon_after = 0;
                }else{
                    $coupon_after =$value->zk_final_price - $value->coupon_info;
                }
                $html .= '<div class="strip-goods click_get">';
                $html .= '<div class="strip-thumbnail">';
                $html .= '<a>';
                $html .='<img src="'.$value->pict_url.'" class="img_url">';
                $html .= '</a>';
                $html .= '</div>';
                $html .= '<div class="strip-title">';
                $html .= '<img src="/static/index/images/t-logo-'.$value->user_type.'.png" class="user_type">';
                $html .= '<a><span class="title">'.mb_substr($value->title,0,25,"utf-8").'</span></a>';
                $html .= '<a><p class="strip-goods-after">券后价：￥<samp class="after_price">'.$coupon_after.'</samp></p></a>';
                $html .= '<a><p class="strip-goods-before">原价：￥<samp class="before_price">'.$value->zk_final_price.'</samp></p></a>';
                $html .= '<a><p>销量：<samp class="vol">'.$value->volume.'</samp></p></a>';
                $html .= '</div>';
                $html .= '<div class="strip-vou">';
                $html .= '<a style="color:#fff"><p><span>券</span></p>';
                $html .= '<P>￥ <samp class="coupon">'.$value->coupon_info.'</samp></P></a>';
                $html .= '</div>';
                $html .= '<div style="display:none">';
                $html .= '<p class="g_id">'.$value->num_iid.'</p>';
                $html .= '<p class="tao_url">'.$value->click_url.'</p>';
                $html .= '<p class="coupon_url">'.$value->coupon_click_url.'</p>';
                $html .= '<p class="item_url">'.$value->item_url.'</p>';
                $html .= '<p class="rate">'.$value->tk_rate.'</p>';
                $html .= '<p class="category">'.$value->category.'</p>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }
        return $html;
    }

}