<?php
/**
 * Utils.php
 * 
 * 工具类
 * 
 * @author      熊猫小A
 * @version     2019-01-15 0.01
 */

class Utils
{
    /**
     * 输出相对首页路由，本方法会自适应伪静态
     * 
     * @return void
     */
    public static function index($path)
    {
        Helper::options()->index($path);
    }

    /**
     * 输出相对首页路径，本方法不处理伪静态，用于静态文件
     * 
     * @return void
     */
    public static function indexHome($path)
    {
        Helper::options()->siteUrl($path);
    }

    /**
     * 输出相对主题目录路径，用于静态文件
     * 
     * @return void
     */
    public static function indexTheme($path)
    {
        Helper::options()->themeUrl($path);
    }

    /**
     * 输出头像链接
     * 
     * @return void
     */
    public static function gravatar($mail, $size = 64, $d = '')
    {
        echo Typecho_Common::gravatarUrl($mail, $size, '', urlencode($d), true);
    }

    /**
     * 判断插件是否可用
     * 
     * @return bool
     */
    public static function isPluginAvailable($name) 
    {
        $plugins = Typecho_Plugin::export();
        $plugins = $plugins['activated'];
        return is_array($plugins) && array_key_exists($name, $plugins);
    }

    /**
     * PJAX判定
     * 
     * @return bool
     */
    public static function isPjax()
    {
        return array_key_exists('HTTP_X_PJAX', $_SERVER) && $_SERVER['HTTP_X_PJAX'];
    }

    /**
     * 移动端判定
     * 
     * @return bool
     */
    public static function isMobile()
    { 
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
            return TRUE;
        }
        
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array ('mobile','nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap'
                ); 
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
                return TRUE;
            }
        }
        if (isset ($_SERVER['HTTP_ACCEPT'])){
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== FALSE) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === FALSE || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))){
                return TRUE;
            }
        }
        return FALSE;
    }

    /**
     * 编辑界面添加Button
     * 
     * @return void
     */
    public static function addButton()
    {
        echo '<script src="';
        self::indexTheme('/assets/libs/owo/owo_01.js');
        echo '"></script>';

        echo '<script src="';
        self::indexTheme('/assets/editor-ee76ad425f.js');
        echo '"></script>';

        echo '<link rel="stylesheet" href="';
        self::indexTheme('/assets/libs/owo/owo.min.css');
        echo '" />';
       
        echo '<style>#custom-field textarea,#custom-field input{width:100%}
        .OwO span{background:none!important;width:unset!important;height:unset!important}
        .OwO .OwO-logo{
            z-index: unset!important;
        }
        .OwO .OwO-body .OwO-items{
            -webkit-overflow-scrolling: touch;
            overflow-x: hidden;
        }
        .OwO .OwO-body .OwO-items-image .OwO-item{
            max-width:-moz-calc(20% - 10px);
            max-width:-webkit-calc(20% - 10px);
            max-width:calc(20% - 10px)
        }
        @media screen and (max-width:767px){	
            .comment-info-input{flex-direction:column;}
            .comment-info-input input{max-width:100%;margin-top:5px}
            #comments .comment-author .avatar{
                width: 2.5rem;
                height: 2.5rem;
            }
        }
        @media screen and (max-width:760px){
            .OwO .OwO-body .OwO-items-image .OwO-item{
                max-width:-moz-calc(25% - 10px);
                max-width:-webkit-calc(25% - 10px);
                max-width:calc(25% - 10px)
            }
        }</style>';
    }

    /**
     * 判定内容是否过时
     * 
     * @return array
     */
    public static function isOutdated($archive)
    {
        date_default_timezone_set("Asia/Shanghai");
        $created = round((time()- $archive->created) / 3600 / 24);
        $updated = round((time()- $archive->modified) / 3600 / 24);

        return array("is" => $created > 90,
                    "created" => $created,
                    "updated" => $updated);
    }

    /**
     * 输出建站时间（最早一篇文章的写作时间）
     * 
     * @return array
     */
    public static function getBuildTime()
    {
        date_default_timezone_set("Asia/Shanghai");
        $db = Typecho_Db::get();
        $content = $db->fetchRow($db->select()->from('table.contents')
        ->where('table.contents.status = ?', 'publish')
        ->where('table.contents.password IS NULL')
        ->order('table.contents.created', Typecho_Db::SORT_ASC)
        ->limit(1));
        echo date('Y-m-d\TH:i', $content['created']);
    }

    /**
     * 已发布文章数量
     * 
     * @return int
     */
    public static function getPostNum()
    {
        $db = Typecho_Db::get();
        return $db->fetchObject($db->select(array('COUNT(cid)' => 'num'))
                    ->from('table.contents')
                    ->where('table.contents.type = ?', 'post')
                    ->where('table.contents.status = ?', 'publish'))->num;
    }

    /**
     * 分类数量
     * 
     * @return int
     */
    public static function getCatNum()
    {
        $db = Typecho_Db::get();
        return $db->fetchObject($db->select(array('COUNT(mid)' => 'num'))
                    ->from('table.metas')
                    ->where('table.metas.type = ?', 'category'))->num;
    }

    /**
     * 标签数量
     * 
     * @return int
     */
    public static function getTagNum()
    {
        $db = Typecho_Db::get();
        return $db->fetchObject($db->select(array('COUNT(mid)' => 'num'))
                    ->from('table.metas')
                    ->where('table.metas.type = ?', 'tag'))->num;
    }

    /**
     * 单文章字数
     * 
     * @return int
     */
    public static function wordCount($archive){
        if($archive->wordCount == 0 || ($archive->modified > $archive->wordCountTime)){
            $db = Typecho_Db::get();
            $dbname =$db->getPrefix() . 'contents';
            $row = $db->fetchRow($db->select()->from('table.contents')->where('cid = ?', $archive->cid));
            $count = mb_strlen(preg_replace("/[^\x{4e00}-\x{9fa5}]/u", "", $row['text']), 'UTF-8');
            
            $db->query($db->update('table.contents')->rows(array('wordCount' => (int)$count))->where('cid = ?', $archive->cid));
            $db->query($db->update('table.contents')->rows(array('wordCountTime' => (int)$archive->modified))->where('cid = ?', $archive->cid));

            return $count;
        }
        else{
            return $archive->wordCount;
        }
    }

    /**
     * 根据 cid 直接更新字数
     */
    public static function wordCountByCid($cid){
        $db = Typecho_Db::get();
        $dbname =$db->getPrefix() . 'contents';
        $row = $db->fetchRow($db->select()->from('table.contents')->where('cid = ?', $cid));
        $count = mb_strlen(preg_replace("/[^\x{4e00}-\x{9fa5}]/u", "", $row['text']), 'UTF-8');

        $db->query($db->update('table.contents')->rows(array('wordCount' => (int)$count))->where('cid = ?', $cid));
        $db->query($db->update('table.contents')->rows(array('wordCountTime' => (int)$row['modified']))->where('cid = ?', $cid));

        return $count;
    }

    /**
     * 超高级设置
     * 
     * @return array
     */
    public static function getVOIDSettings()
    {
        $output = array(
            // 主题设置
            'defaultBanner' => 'https://i.loli.net/2019/02/11/5c614078f2263.png',
            'enableMath' => false,
            'head' => '',
            'footer' => '',
            'serifincontent' => false,
            'pjax' => false,
            'pjaxreload' => '',
            'titleinbanner' => false,
            'lazyload' => false,
            'indexBannerTitle' => '',
            'serviceworker' => false,
            'colorScheme' => 0, // 0: 自动，1: 日间，2: 夜间
            'headerColorScheme' => 0, //0: 暗色，1: 透明
            'reward' => '',

            // 高级设置
            'nav' => '',
            'name' => '',
            'desktopBannerHeight' => '',
            'mobileBannerHeight' => '',
            'twitterId' => '',
            'weiboId' => '',
            'headerMode' => 0,
            'followSystemColorScheme' => false
        );

        $options = Helper::options();

        if(!empty($options->advance)){
            $settings = json_decode($options->advance);
            foreach ($settings as $key => $value) {
                $output[$key] = $value;
            }
        }

        $output['defaultBanner'] = $options->defaultBanner;

        if(!empty($options->enableMath)){
            if($options->enableMath == '1') $output['enableMath'] = true;
        }

        if(!empty($options->lazyload)){
            if($options->lazyload == '1') $output['lazyload'] = true;
        }

        if(!empty($options->colorScheme)){
            if($options->colorScheme == '1') $output['colorScheme'] = 1;
            if($options->colorScheme == '2') $output['colorScheme'] = 2;
        }

        if(!empty($options->headerColorScheme)){
            if($options->headerColorScheme == '1') $output['headerColorScheme'] = 1;
        }

        if(!empty($options->head)){
            $output['head'] = $options->head;
        }

        if(!empty($options->indexBannerTitle)){
            $output['indexBannerTitle'] = $options->indexBannerTitle;
        }

        if(!empty($options->footer)){
            $output['footer'] = $options->footer;
        }

        if(!empty($options->pjax)){
            if($options->pjax == '1') $output['pjax'] = true;
        }

        if(!empty($options->serifincontent)){
            if($options->serifincontent == '1') $output['serifincontent'] = true;
        }
        
        if(!empty($options->pjaxreload)){
            $output['pjaxreload'] = $options->pjaxreload;
        }

        if(!empty($options->reward)){
            $output['reward'] = $options->reward;
        }

        if(!empty($options->titleinbanner)){
            if($options->titleinbanner == '1') $output['titleinbanner'] = true;
        }

        if(!empty($options->serviceworker)){
            if($options->serviceworker == '1') $output['serviceworker'] = true;
        }

        return $output;
    }
}