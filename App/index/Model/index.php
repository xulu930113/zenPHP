<?php
namespace App\index\Model;

use PHPQRCode\QRcode;
use zenphp\core\Model;

/**
 *
 * @package    index
 * @author     Shaun.Xu
 * @since      2018/1/22 15:57
 */
class index extends Model
{
    private $suffix = ".png";

    function test(){
        $insertData = array(
            'content' => "测试".time(),
        );
        $this->getCache()->set('key',$insertData,60);
        $data = $this->getDB()
            ->setTable('data as tb1')
            ->setField('tb1.`id`,tb1.`content`')
            ->setLimit(5,2)
            ->select();
        return $data;
    }

    /**
     * 生成名片二维码
     * qrcode
     * @author ShaunXu
     * @date 2018/9/28
     * @param $name         //姓名
     * @param $username     //昵称
     * @param $company      //公司名称
     * @param $position     //职位
     * @param $tell         //公司电话
     * @param $phone        //手机号
     * @param $email        //邮箱
     * @param $adr          //地址
     * @return string       放回二维码图片
     */
    function qrcode($name , $phone = "" , $tell = "" , $email = "" , $username = "" , $company = "" , $position = "" , $adr = ""){
        $QRcode = new QRcode();
        $url = "BEGIN:VCARD
VERSION:3.0
N:".$name."
NICKNAME:".$username."
TEL;TYPE=CELL:".$phone."
TEL;TYPE=work:".$tell."
EMAIL;TYPE=WORK:".$email."
ORG:".$company."
TITLE:".$position."
ADR;TYPE=WORK(*):".$adr."
END:VCARD";
        $level='M';
        $size=4;
        $errorCorrectionLevel =intval($level) ;//容错级别
        $matrixPointSize = intval($size);//生成图片大小
        $path = $this->filePath();
        $QRcode->png($url, ROOT."/".$path, $errorCorrectionLevel, $matrixPointSize,2);    //vcardimg为保存目录
        return $path;
    }

    /**
     *
     * base64EncodeImage
     * @author ShaunXu
     * @date 2018/9/28
     * @param $image_file
     * @return string
     */
    function base64EncodeImage ($image_file)
    {
        $base64_image = '';
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    }

    /**
     * 文件路径
     * filePath
     * @author ShaunXu
     * @date 2018/9/21
     * @return string
     */
    function filePath(){
        $path = "data/uploads/vcardcode/".date("Y/m/d")."/";
        $this->MkFolder(ROOT."/".$path);
        $fileName = md5($this->randCode(6).time()).$this->suffix;
        return $path.$fileName;
    }

    /**
     * 创建目录
     * MkFolder
     * @author Shaun.Xu
     * @access public
     * @param $path
     * @return void
     */
    private function MkFolder($path) {
        if (!is_readable($path)) {
            $this->MkFolder(dirname($path));
            if (!is_file($path)) {
                mkdir($path, 0777);
            }
        }
    }

    /**
     * 获取某长度的随机字符编码
     * 除纯数字与纯字母选项，其他都不包括(I,i,o,O,1,0)
     * @author	Xuni
     * @since	2015-11-05
     *
     * @param	int		$len	编码长度
     * @param	string	$format	格式（ALL：大小写字母加数字，CHAR：大小写字母，NUMBER：纯数字，默认为小写字母加数字）
     * @return	array
     */
    function randCode($len, $format='')
    {
        $is_abc = $is_numer = 0;
        $password = $tmp ='';
        switch($format){
            case 'ALL':
                $chars='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjklmnpqrstuvwxyz23456789';
                break;
            case 'CHAR':
                $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
                break;
            case 'NUMBER':
                $chars='0123456789';
                break;
            default :
                $chars='abcdefghjklmnpqrstuvwxyz23456789';
                break;
        }
        //@mt_srand((double)microtime()*1000000*getmypid());
        while(strlen($password)<$len){
            $tmp =substr($chars,(mt_rand()%strlen($chars)),1);
            if(($is_numer <> 1 && is_numeric($tmp) && $tmp > 0 )|| $format == 'CHAR'){
                $is_numer = 1;
            }
            if(($is_abc <> 1 && preg_match('/[a-zA-Z]/',$tmp)) || $format == 'NUMBER'){
                $is_abc = 1;
            }
            $password.= $tmp;
        }
        if($is_numer <> 1 || $is_abc <> 1 || empty($password) ){
            $password = $this->randCode($len,$format);
        }
        return $password;
    }
}