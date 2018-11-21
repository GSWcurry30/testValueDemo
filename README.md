# testValueDemo
# testValueDemo
validator是按照一定规则校验参数合法性的类，包括一个静态方法，对校验参数数组value中的元素，
依次采用校验规则数组rules中的方法名进行校验。由于普通数据类型和数组类型参数在校验方法中参数个数不同，
因为分别采用不同的校验规则。

校验的规则在类validateRules中，包括已经添加的规则以及一个接收用户自定义规则的静态数组。用户自定义规则通过匿名函数添加到validateRules::$funclist数组中，
其他规则通过一个静态方法实现，不满足条件抛出validateException异常。

接口validator::validate($value = array(),$rules = array(),$index = 0)中采用回调函数call_user_func()对传入的参数采用规则类中的静态方法进行校验。

