<?php
/**
 * Created by PhpStorm.
 * 变量命名方式
 *---------------------------------------------
 * $sString | $oObject | $nNumber | $arrArray
 *  字符串   |   对象    |   数字   |    数组
 *---------------------------------------------
 * User : Armand
 * Email: yhzs15155@sina.com
 * Date : 2018/4/2
 * Time : 9:46
 */


header('Content-type:text/html;charset=utf-8');
/**
 * 将阿拉伯数字转换为汉字
 * TODO:整形数字转换不准超过PHP最大整数 如果用字符串类型则限制到载级单位 如果想在往上的话就自己加
 * Class Number
 * @package app\index\controller
 */
class Number
{
    // 数字对应的汉字
    private $arrWordNumber   = ['零', '一', '二', '三', '四', '五', '六', '七', '八', '九'];
    // 大单位 : 从末尾开始 每四位数为一大单位 写代码能赚到一载么
    private $arrLargeCompany = [1 => '', '万', '亿', '兆', '京', '垓', '秭', '穰', '沟', '涧', '正', '载'];
    // 小单位
    private $arrCompany      = ['千', '百', '十', ''];


    /**
     * 调用demo
     * 1234567   -> 一百二十三万四千五百六十七
     * 123456789 -> 一亿两千三百四十五万六千七百八十九
     */
    public function index()
    {
        $sWordNumber = self::getNumberCompany('6666600000');
        $this->prompt($sWordNumber);

    }// END index


    /**
     * 类调用入口
     * @param int $nNumber
     * @return string
     */
    public function getNumberCompany( $nNumber = 0 )
    {
        // 拆分数组
        $arrNumber = self::getSplitNumber(str_split($nNumber));
        // 大单位计数
        $nCount    = count($arrNumber);

        // 数字转换为汉字
        $sWordNumber = '';
        foreach ($arrNumber as $k => $v)
        {
            // 拼接小单位
            foreach ($v as $key => $val)
            {
                // 不为 0 的情况下才可以拼接单位
                if ($val == 0)
                    $sWordNumber .= $this->arrWordNumber[$val];
                else
                    $sWordNumber .= $this->arrWordNumber[$val].$this->arrCompany[$key];
            }
            // 拼接大单位
            if ($nCount != 0)
            {
                // 小数组全为 0 则不拼接单位 以免出现零万 零百万 零千万的现象
                if (array_sum($v) != 0)
                    $sWordNumber .= $this->arrLargeCompany[$nCount];
                $nCount --;
            }
        }
        // 去除开头跟末尾以及多余的零
        $sWordNumber = self::RemoveZero($sWordNumber);

        return $sWordNumber;

    }// END getNumberCompany


    /**
     * 拆分并返回四个数字为一组的二维数组
     * @param array $arrNumber
     * @return array
     */
    private function getSplitNumber( $arrNumber = array() )
    {
        $nNumber   = 0;                                // 数组指针
        $arrData   = array();                          // 数组容器
        $arrNumber = array_reverse($arrNumber);        // 从尾部计算 计数都是从尾部开始个十百千万这样算的 所以要反转一下数组
        // 分割成二维数组
        foreach ($arrNumber as $k => $v)
        {
            if (empty($arrData[$nNumber]))
                $arrData[$nNumber][] = $v;
            else
                $arrData[$nNumber][] = $v;

            if (count($arrData[$nNumber]) == 4)
                $nNumber ++;
        }
        // 分割完成 把数组反转回来
        $arrData = array_reverse($arrData);
        foreach ($arrData as $k => $v)
        {
            $nCount = count($v);
            if ($nCount < 4)
            {
                for ($i = 0; $i < (4 - $nCount); $i ++)
                    $arrData[$k][] = '0';
            }
            $arrData[$k] = array_reverse($arrData[$k]);
        }

        return $arrData;

    }// END getSplitNumber


    /**
     * 去除首尾的零
     * @param string $sString
     * @return string
     */
    private function RemoveZero( $sString = '')
    {
        $sString = str_replace('零零', '', $sString);         // 把所有相临的零都去掉
        $arrWordNumber = self::mbStrSplit($sString);         // 拆分字符串为数组
        $nCount = count($arrWordNumber);                     // 现存数字个数
        foreach ($this->arrLargeCompany as $key => $val)
        {
            // 开头是零不行 结尾是零不行 大单位前一位是零不行
            foreach ($arrWordNumber as $k => $v)
            {
                if ($k == 0 and $v == '零')
                    unset($arrWordNumber[$k]);
                if ($k == ($nCount - 1) and $v == '零')
                    unset($arrWordNumber[$k]);
                if ($v == $val and $arrWordNumber[$k - 1] == '零')
                    unset($arrWordNumber[$k - 1]);
            }
        }
        // 刷新key
        $arrWordNumber = array_values($arrWordNumber);
        // 一十一开头总感觉挺奇怪的 所以换成十
        if ($arrWordNumber[0].$arrWordNumber[1] == '一十')
            unset($arrWordNumber[0]);
        $sString = implode('', $arrWordNumber);
        return $sString;

    }// END RemoveZero


    /**
     * 切割中文字符
     * @param $sString
     * @param int $nLength
     * @param string $sCharset
     * @return array|bool
     */
    private function mbStrSplit($sString, $nLength = 1, $sCharset = "UTF-8")
    {
        if (func_num_args() == 1)
            return preg_split('/(?<!^)(?!$)/u', $sString);

        if ($nLength < 1)
            return false;
        $nLen    = mb_strlen($sString, $sCharset);
        $arrData = array();
        for ($i  = 0; $i < $nLen; $i += $nLength)
        {
            $s = mb_substr($sString, $i, $nLength, $sCharset);
            $arrData[] = $s;
        }
        return $arrData;

    }// END mbStrSplit


    /**
     * 提示
     * @param $sMsg
     */
    public function prompt( $sMsg )
    {
        $sHtml  = "<title>数字转汉字</title><meta charset=\"utf-8\"><meta name=\"viewport\" content=\"width=device-width, initial-scale=1, user-scalable=0\"><link rel=\"stylesheet\" type=\"text/css\" href=\"https://res.wx.qq.com/connect/zh_CN/htmledition/style/wap_err1a9853.css\">";
        $sHtml .= "<div class=\"page_msg\"><div class=\"inner\"><span class=\"msg_icon_wrp\"><i class=\"icon80_smile\"></i></span><div class=\"msg_content\"><h4>$sMsg</h4></div></div></div>";
        echo $sHtml;
        exit;

    }// END prompt


}// END CLASS