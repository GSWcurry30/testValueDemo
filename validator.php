<?php
/**
 * Created by PhpStorm.
 * User: HuQingWei
 * Date: 2018/11/20
 * Time: 下午4:29
 */

//异常信息类
class validateException extends Exception {
    private $exceptionMsg;
    public function __construct($msg)
    {
        $this->exceptionMsg = $msg;
    }

    public function getExceptioninfo(){
        return $this->exceptionMsg;
    }

}

//验证规则类
//校验方法的命名规则：对于数组类型的校验方法以'array'为前缀，其余数据类型分别以相应类型英文为前缀
class validateRules{

    public static $funclist = array();    //用户扩展规则，通过匿名函数添加

    /**
     * @param $data
     * @param array $type
     * @throws validateException
     * 验证数据类型是否合法
     */
    public static function dataType($data){
        $type = array('int','bool','float','string','array','object');
        if(!in_array(gettype($data),$type)){
            throw new validateException("参数类型不合法");
        }
    }

    /**
     * @param $data
     * @throws validateException
     * 验证数据范围
     */
    public static function dataRange($data){
        if($data < 0 || $data > 100) {
            throw new validateException("数据范围不合法");
        }
    }

    /**
     * @param $data
     * @param array $list
     * @throws validateException
     * 验证数据是否在某个列表
     */
    public static function dataInList($data){
        $list = array('green','blue','red');
        if(!in_array($data,$list)){
            throw new validateException("数据不在指定列表中");
        }
    }

    /**
     * @param $str
     * @param $min
     * @param $max
     * @throws validateException
     * 验证字符串长度是否在某个区间
     */
    public static function strlenInRange($str){
        $range = array('min','max');
        if(gettype($str) == "string" && (strlen($str)<$range['min'] || strlen($str))>$range['max']){
            throw new validateException("字符串长度不在指定区间");
        }
    }

    /**
     * @param $str
     * @param $regexprule
     * @throws validateException
     * 验证字符串是否符合正则规则
     */
    public static function strRegexp($str){
        $regexprule="\w+";
        preg_match($regexprule,$str,$res);
        if((!empty($res) && $res[0] != $str) || empty($res)) {
            throw new validateException("字符串不符合正则表达式");
        }
    }


    /**
     * @param $arrdata
     * @param int $key
     * @throws validateException
     * 验证数组元素是否都是int且大于0
     */
    public static function arrayCheck($arrdata=array(),$key=0){
        if(gettype($arrdata[$key]) != 'int' && $arrdata[$key] <= 0){
            throw new validateException("数组元素不符合校验规则");
        }elseif($key = count($arrdata)){
            return;
        }
        else{
            self::arrayCheck($arrdata,$key+1);
        }
    }

    /**
     * @param $arraydata
     * @param $index
     * @throws validateException
     * 验证数组特定元素是否符合校验规则(无递归)
     */
    public static function arrayIndexCheck($arraydata=array(),$index){
        if(array_key_exists($index,$arraydata) && (gettype($arraydata[$index]) != 'int' || $arraydata[$index] <= 0)){
            throw new validateException("数组特定元素不符合校验规则");
        }
    }

    /**
     * @param $arraydata
     * @param $index
     * @throws validateException
     * 数组可选字段校验
     */
    public static function arraySelectIndexCheck($arraydata=array(),$index){
        if(array_key_exists($index,$arraydata) && gettype($arraydata[$index]) != 'bool'){
            throw new validateException("可选字段校验错误");
        }
    }

}
//验证接口
class validator{
    /**
     * @param array $value   验证参数      exp: array(1,"abc",array(1,2,3))
     * @param array $rules   验证规则字符串 exp: array("validateRules::dataInList","validateRules::dataType","validateRules::funclist['func_name']")
     * @param int|string $index 对于数组指定元素的校验
     * @return int
     */

    public static function validate($value = array(),$rules = array(),$index = 0){
        try{
            //对参数依次按照制定规则校验
            foreach ($value as $v){
                //非数组类型数据验证
                if(!is_array($v)) {
                    foreach ($rules as $val) {
                        //数组校验规则和普通数据类型校验规则的参数不同,非数组数据只校验非数组的校验规则
                        if(substr($val,15,5) == 'array'){
                            continue;
                        }
                        call_user_func($val, $v);//(规则方法，规则参数)
                    }
                }
                //数组类型数据验证
                else{
                    foreach ($rules as $val){
                        //数组数据只校验数组的校验规则
                        if(substr($val,15,5) != 'array'){
                            continue;
                        }
                        if(empty($index))//(规则方法，数组规则参数---数组名和需要检测的$key)
                        {
                            call_user_func($val,array($v,0));      //递归校验数组所有元素
                        }
                        else{
                            call_user_func($val,array($v,$index)); //校验数组指定元素
                        }
                    }
                }
            }
            return 1;
        }catch(validateException $e){
            $e->getExceptionInfo();
        }
    }
}