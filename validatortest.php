<?php
/**
 * Created by PhpStorm.
 * User: HuQingWei
 * Date: 2018/11/21
 * Time: 下午12:53
 */
require_once 'validator.php';
require_once __DIR__ . '/vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class validatortest extends TestCase{

    //创建用户自定义规则
    public function createRules(){
        validateRules::$funclist['isEven'] = function($data){
            //规则方法 (exp:校验数字是否为偶数)
            if(gettype($data) == 'int' && $data%2 == 1){
                throw new validateException("数字非偶数");
            }
        };
    }

    //接口测试
    public function testvalidate(){

        $value = array(1,2,"abc",array(1,2,3,6));
        $rules = array("validateRules::dataType","validateRules::dataRange","validateRules::dataInList","validateRules::strlenInRange","validateRules::strRegexp","validateRules::arrayCheck");
        $rules2 = array("validateRules::arrayIndexCheck");
        $this->assertEquals(validator::validate($value,$rules),1);
        $this->assertEquals(validator::validate($value,$rules2,2),1);
    }
}








