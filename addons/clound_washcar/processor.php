<?php
/**
 * 云洗车模块处理程序
 *
 * @author zhb1190
 * @url 
 */
defined('IN_IA') or exit('Access Denied');

class Clound_washcarModuleProcessor extends WeModuleProcessor {
	public function respond() {
		$content = $this->message['content'];
		//这里定义此模块进行消息处理时的具体过程, 请查看微擎文档来编写你的代码
        $news = array(
            'Title' => '洗车',
            'Description' => '洗车！',
            'PicUrl' => '',
            'Url' => $this->createMobileUrl("index"),
        );
        //return $this->respText($content);
        return $this->respNews($news);
	}
}