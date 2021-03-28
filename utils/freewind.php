<?php
/**
 * @file: freewind.php
 * @Author: lengwen
 * @Date: 2021/3/28 1:31 下午
 * @Mail: kevinlu98@qq.com
 * @description: 自由之风工具包
 *
 */

/**
 * Class smtp 邮件工具类
 */
class smtp
{
    /* Public Variables */
    var $smtp_port; //smtp_port 端口号
    var $time_out;
    var $host_name; //服务器主机名
    var $log_file;
    var $relay_host; //服务器主机地址
    var $debug;
    var $auth; //验证
    var $user; //服务器用户名
    var $pass; //服务器密码

    /* Private Variables */
    var $sock;

    /* Constractor 构造方法*/
    function __construct($relay_host = "", $smtp_port = 25, $auth = false, $user, $pass)
    {
        $this->debug = FALSE;
        $this->smtp_port = $smtp_port;
        $this->relay_host = $relay_host;
        $this->time_out = 30; //is used in fsockopen()
        #
        $this->auth = $auth; //auth
        $this->user = $user;
        $this->pass = $pass;
        #
        $this->host_name = "localhost"; //is used in HELO command
        // $this->host_name = "smtp.163.com"; //is used in HELO command
        $this->log_file = "";

        $this->sock = FALSE;
    }

    /* Main Function */
    function sendmail($to, $from, $subject = "", $body = "", $mailtype, $cc = "", $bcc = "", $additional_headers = "")
    {
        $header = "";
        $mail_from = $this->get_address($this->strip_comment($from));
        $body = mb_ereg_replace("(^|(\r\n))(\\.)", "\\1.\\3", $body);
        $header .= "MIME-Version:1.0\r\n";
        if ($mailtype == "HTML") { //邮件发送类型
            //$header .= "Content-Type:text/html\r\n";
            $header .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        }
        $header .= "To: " . $to . "\r\n";
        if ($cc != "") {
            $header .= "Cc: " . $cc . "\r\n";
        }
        $header .= "From: " . $from . "\r\n";
        // $header .= "From: $from<".$from.">\r\n";   //这里只显示邮箱地址，不够人性化
        $header .= "Subject: " . $subject . "\r\n";
        $header .= $additional_headers;
        $header .= "Date: " . date("r") . "\r\n";
        $header .= "X-Mailer:By (PHP/" . phpversion() . ")\r\n";
        list($msec, $sec) = explode(" ", microtime());
        $header .= "Message-ID: <" . date("YmdHis", $sec) . "." . ($msec * 1000000) . "." . $mail_from . ">\r\n";
        $TO = explode(",", $this->strip_comment($to));

        if ($cc != "") {
            $TO = array_merge($TO, explode(",", $this->strip_comment($cc))); //合并一个或多个数组
        }

        if ($bcc != "") {
            $TO = array_merge($TO, explode(",", $this->strip_comment($bcc)));
        }

        $sent = TRUE;
        foreach ($TO as $rcpt_to) {
            $rcpt_to = $this->get_address($rcpt_to);
            if (!$this->smtp_sockopen($rcpt_to)) {
                $this->log_write("Error: Cannot send email to " . $rcpt_to . "\n");
                $sent = FALSE;
                continue;
            }
            if ($this->smtp_send($this->host_name, $mail_from, $rcpt_to, $header, $body)) {
                $this->log_write("E-mail has been sent to <" . $rcpt_to . ">\n");
            } else {
                $this->log_write("Error: Cannot send email to <" . $rcpt_to . ">\n");
                $sent = FALSE;
            }
            fclose($this->sock);
            $this->log_write("Disconnected from remote host\n");
        }
        echo "<br>";
        //echo $header;
        return $sent;
    }

    /* Private Functions */

    function smtp_send($helo, $from, $to, $header, $body = "")
    {
        if (!$this->smtp_putcmd("HELO", $helo)) {
            return $this->smtp_error("sending HELO command");
        }
        #auth
        if ($this->auth) {
            if (!$this->smtp_putcmd("AUTH LOGIN", base64_encode($this->user))) {
                return $this->smtp_error("sending HELO command");
            }

            if (!$this->smtp_putcmd("", base64_encode($this->pass))) {
                return $this->smtp_error("sending HELO command");
            }
        }
        #
        if (!$this->smtp_putcmd("MAIL", "FROM:<" . $from . ">")) {
            return $this->smtp_error("sending MAIL FROM command");
        }

        if (!$this->smtp_putcmd("RCPT", "TO:<" . $to . ">")) {
            return $this->smtp_error("sending RCPT TO command");
        }

        if (!$this->smtp_putcmd("DATA")) {
            return $this->smtp_error("sending DATA command");
        }

        if (!$this->smtp_message($header, $body)) {
            return $this->smtp_error("sending message");
        }

        if (!$this->smtp_eom()) {
            return $this->smtp_error("sending <CR><LF>.<CR><LF> [EOM]");
        }

        if (!$this->smtp_putcmd("QUIT")) {
            return $this->smtp_error("sending QUIT command");
        }

        return TRUE;
    }

    function smtp_sockopen($address)
    {
        if ($this->relay_host == "") {
            return $this->smtp_sockopen_mx($address);
        } else {
            return $this->smtp_sockopen_relay();
        }
    }

    function smtp_sockopen_relay()
    {
        $this->log_write("Trying to " . $this->relay_host . ":" . $this->smtp_port . "\n");
        $this->sock = @fsockopen($this->relay_host, $this->smtp_port, $errno, $errstr, $this->time_out);
        if (!($this->sock && $this->smtp_ok())) {
            $this->log_write("Error: Cannot connenct to relay host " . $this->relay_host . "\n");
            $this->log_write("Error: " . $errstr . " (" . $errno . ")\n");
            return FALSE;
        }
        $this->log_write("Connected to relay host " . $this->relay_host . "\n");
        return TRUE;;
    }

    function smtp_sockopen_mx($address)
    {
        $domain = ereg_replace("^.+@([^@]+)$", "\\1", $address);
        if (!@getmxrr($domain, $MXHOSTS)) {
            $this->log_write("Error: Cannot resolve MX \"" . $domain . "\"\n");
            return FALSE;
        }
        foreach ($MXHOSTS as $host) {
            $this->log_write("Trying to " . $host . ":" . $this->smtp_port . "\n");
            $this->sock = @fsockopen($host, $this->smtp_port, $errno, $errstr, $this->time_out);
            if (!($this->sock && $this->smtp_ok())) {
                $this->log_write("Warning: Cannot connect to mx host " . $host . "\n");
                $this->log_write("Error: " . $errstr . " (" . $errno . ")\n");
                continue;
            }
            $this->log_write("Connected to mx host " . $host . "\n");
            return TRUE;
        }
        $this->log_write("Error: Cannot connect to any mx hosts (" . implode(", ", $MXHOSTS) . ")\n");
        return FALSE;
    }

    function smtp_message($header, $body)
    {
        fputs($this->sock, $header . "\r\n" . $body);
        $this->smtp_debug("> " . str_replace("\r\n", "\n" . "> ", $header . "\n> " . $body . "\n> "));

        return TRUE;
    }

    function smtp_eom()
    {
        fputs($this->sock, "\r\n.\r\n");
        $this->smtp_debug(". [EOM]\n");

        return $this->smtp_ok();
    }

    function smtp_ok()
    {
        $response = str_replace("\r\n", "", fgets($this->sock, 512));
        $this->smtp_debug($response . "\n");

        if (!mb_ereg("^[23]", $response)) {
            fputs($this->sock, "QUIT\r\n");
            fgets($this->sock, 512);
            $this->log_write("Error: Remote host returned \"" . $response . "\"\n");
            return FALSE;
        }
        return TRUE;
    }

    function smtp_putcmd($cmd, $arg = "")
    {
        if ($arg != "") {
            if ($cmd == "")
                $cmd = $arg;
            else
                $cmd = $cmd . " " . $arg;
        }

        fputs($this->sock, $cmd . "\r\n");
        $this->smtp_debug("> " . $cmd . "\n");

        return $this->smtp_ok();
    }

    function smtp_error($string)
    {
        $this->log_write("Error: Error occurred while " . $string . ".\n");
        return FALSE;
    }

    function log_write($message)
    {
        $this->smtp_debug($message);

        if ($this->log_file == "") {
            return TRUE;
        }

        $message = date("M d H:i:s ") . get_current_user() . "[" . getmypid() . "]: " . $message;
        if (!@file_exists($this->log_file) || !($fp = @fopen($this->log_file, "a"))) {
            $this->smtp_debug("Warning: Cannot open log file \"" . $this->log_file . "\"\n");
            return FALSE;
        }
        flock($fp, LOCK_EX);
        fputs($fp, $message);
        fclose($fp);

        return TRUE;
    }

    function strip_comment($address)
    {
        $comment = "\\([^()]*\\)";
        while (mb_ereg($comment, $address)) {
            $address = mb_ereg_replace($comment, "", $address);
        }

        return $address;
    }

    function get_address($address)
    {
        $address = mb_ereg_replace("([ \t\r\n])+", "", $address);
        $address = mb_ereg_replace("^.*<(.+)>.*$", "\\1", $address);

        return $address;
    }

    function smtp_debug($message)
    {
        if ($this->debug) {
            echo $message . "<br>";
        }
    }

    function get_attach_type($image_tag) //
    {

        $filedata = array();

        $img_file_con = fopen($image_tag, "r");
        unset($image_data);
        while ($tem_buffer = AddSlashes(fread($img_file_con, filesize($image_tag))))
            $image_data .= $tem_buffer;
        fclose($img_file_con);

        $filedata['context'] = $image_data;
        $filedata['filename'] = basename($image_tag);
        $extension = substr($image_tag, strrpos($image_tag, "."), strlen($image_tag) - strrpos($image_tag, "."));
        switch ($extension) {
            case ".gif":
                $filedata['type'] = "image/gif";
                break;
            case ".gz":
                $filedata['type'] = "application/x-gzip";
                break;
            case ".htm":
                $filedata['type'] = "text/html";
                break;
            case ".html":
                $filedata['type'] = "text/html";
                break;
            case ".jpg":
                $filedata['type'] = "image/jpeg";
                break;
            case ".tar":
                $filedata['type'] = "application/x-tar";
                break;
            case ".txt":
                $filedata['type'] = "text/plain";
                break;
            case ".zip":
                $filedata['type'] = "application/zip";
                break;
            default:
                $filedata['type'] = "application/octet-stream";
                break;
        }


        return $filedata;
    }

}

/**
 * Class VerifyCode 验证码工具类
 */
class VerifyCode
{
    private $width;
    private $height;
    private $str;
    private $im;
    private $strColor;

    function __construct($session_name = 'freewind_code', $length = 4, $width = 80, $height = 20)
    {
        $vcode = 'qwertyupasdfghjkzxcvbnmQWERTYUPASDFGHJKZXCVBNM23456789';
        $this->str = "";
        for ($i = 0; $i < $length; $i++) {
            $this->str .= $vcode[rand(0, strlen($vcode))];
        }
        session_start();
        $_SESSION[$session_name] = $this->str;
        $this->width = $width;
        $this->height = $height;
        $this->createImage();
    }

    function createImage()
    {
        $this->im = imagecreate($this->width, $this->height);//创建画布
        imagecolorallocate($this->im, 200, 200, 200);//为画布添加颜色
        for ($i = 0; $i < 4; $i++) {//循环输出四个数字
            $this->strColor = imagecolorallocate($this->im, rand(0, 100), rand(0, 100), rand(0, 100));
            imagestring($this->im, rand(3, 5), $this->width / 4 * $i + rand(5, 10), rand(2, 5), $this->str[$i], $this->strColor);
        }
        for ($i = 0; $i < 200; $i++) {//循环输出200个像素点
            $this->strColor = imagecolorallocate($this->im, rand(0, 255), rand(0, 255), rand(0, 255));
            imagesetpixel($this->im, rand(0, $this->width), rand(0, $this->height), $this->strColor);
        }
    }

    function show()
    {//
        header('content-type:image/png');//定义输出为图像类型
        imagepng($this->im);//生成图像
        imagedestroy($this->im);//销毁图像释放内存
    }
}

/**
 * Ajax评论功能
 * @param $archive
 * @throws Typecho_Db_Exception
 * @throws Typecho_Exception
 */
function freewind_comment($archive)
{
    $options = Helper::options();
    $user = Typecho_Widget::widget('Widget_User');
    $db = Typecho_Db::get();
//    if ($archive->request->get('_') != Helper::security()->getToken($archive->request->getReferer())) {
//        ajax_error('非法请求');
//    }
    if (!$archive->allow('comment')) {
        ajax_error('评论功能已关闭');
    }
    if (!$user->pass('editor', true) && $archive->authorId != $user->uid &&
        $options->commentsPostIntervalEnable) {
        $last_comment = $db->fetchRow($db->select('created')->from('table.comments')
            ->where('cid = ?', $archive->cid)
            ->where('ip = ?', $archive->request->getIp())
            ->order('created', Typecho_Db::SORT_DESC)
            ->limit(1));
        if ($last_comment && ($options->gmtTime - $last_comment['created'] > 0 &&
                $options->gmtTime - $last_comment['created'] < $options->commentsPostInterval)) {
            ajax_error('对不起, 您的发言过于频繁, 请稍侯再次发布');
        }
    }
    $comment = array(
        'cid' => $archive->cid,
        'created' => $options->gmtTime,
        'agent' => $archive->request->getAgent(),
        'ip' => $archive->request->getIp(),
        'ownerId' => $archive->author->uid,
        'type' => 'comment',
        'status' => !$archive->allow('edit') && $options->commentsRequireModeration ? 'waiting' : 'approved'
    );

    /** 判断父节点 */
    if ($parentId = $archive->request->filter('int')->get('parent')) {
        if ($options->commentsThreaded && ($parent = $db->fetchRow($db->select('coid', 'cid')->from('table.comments')
                ->where('coid = ?', $parentId))) && $archive->cid == $parent['cid']) {
            $comment['parent'] = $parentId;
        } else {
            ajax_error('父级评论不存在');
        }
    }
    $feedback = Typecho_Widget::widget('Widget_Feedback');
    $validator = new Typecho_Validate();

    $validator->addRule('author', 'required', _t('必须填写用户名'));
    $validator->addRule('author', 'xssCheck', _t('请不要在用户名中使用特殊字符'));
    $validator->addRule('author', array($feedback, 'requireUserLogin'), _t('您所使用的用户名已经被注册,请登录后再次提交'));
    $validator->addRule('author', 'maxLength', _t('用户名最多包含200个字符'), 200);

    if ($options->commentsRequireMail && !$user->hasLogin()) {
        $validator->addRule('mail', 'required', _t('必须填写电子邮箱地址'));
    }

    $validator->addRule('mail', 'email', _t('邮箱地址不合法'));
    $validator->addRule('mail', 'maxLength', _t('电子邮箱最多包含200个字符'), 200);

    if ($options->commentsRequireUrl && !$user->hasLogin()) {
        $validator->addRule('url', 'required', _t('必须填写个人主页'));
    }

    $validator->addRule('url', 'url', _t('个人主页地址格式错误'));
    $validator->addRule('url', 'maxLength', _t('个人主页地址最多包含200个字符'), 200);

    $validator->addRule('text', 'required', _t('必须填写评论内容'));

    $comment['text'] = $archive->request->text;

    /** 对一般匿名访问者,将用户数据保存一个月 */
    if (!$user->hasLogin()) {
        /** Anti-XSS */
        $comment['author'] = $archive->request->filter('trim')->author;
        $comment['mail'] = $archive->request->filter('trim')->mail;
        $comment['url'] = $archive->request->filter('trim')->url;

        /** 修正用户提交的url */
        if (!empty($comment['url'])) {
            $urlParams = parse_url($comment['url']);
            if (!isset($urlParams['scheme'])) {
                $comment['url'] = 'http://' . $comment['url'];
            }
        }

        $expire = $options->gmtTime + $options->timezone + 30 * 24 * 3600;
        Typecho_Cookie::set('__typecho_remember_author', $comment['author'], $expire);
        Typecho_Cookie::set('__typecho_remember_mail', $comment['mail'], $expire);
        Typecho_Cookie::set('__typecho_remember_url', $comment['url'], $expire);
    } else {
        $comment['author'] = $user->screenName;
        $comment['mail'] = $user->mail;
        $comment['url'] = $user->url;

        /** 记录登录用户的id */
        $comment['authorId'] = $user->uid;
    }

    /** 评论者之前须有评论通过了审核 */
    if (!$options->commentsRequireModeration && $options->commentsWhitelist) {
        if ($feedback->size($feedback->select()->where('author = ? AND mail = ? AND status = ?', $comment['author'], $comment['mail'], 'approved'))) {
            $comment['status'] = 'approved';
        } else {
            $comment['status'] = 'waiting';
        }
    }

    if ($error = $validator->run($comment)) {
        //返回第一条错误信息
        validator_error($error);
    }

    /** 添加评论 */
    $comment_id = $feedback->insert($comment);
    if (!$comment_id) {
        ajax_error('评论失败,请稍后再试');
    }
    //挂载点
    $feedback->pluginHandle()->comment($comment, $archive);

    $comments = Typecho_Cookie::get('extend_contents_comments');
    if (empty($comments)) {
        $comments = array();
    } else {
        $comments = explode(',', $comments);
    }
    array_push($comments, $archive->cid);
    $comments = implode(',', $comments);
    Typecho_Cookie::set('extend_contents_comments', $comments);

    if (Helper::options()->freeMailEnable > 1) {
        $mailserver = Helper::options()->freeMailServer;
        $port = Helper::options()->freeMailPort;
        $mailuser = Helper::options()->freeMailUser;
        $mailpass = Helper::options()->freeMailPwd;
        $mailto = Helper::options()->freeMailRevice;
        $subject = '文章《' . $archive->title . '》收到了新的评论';
        $content = '<p>文章《' . $archive->title . '》最新评论内容:</p><p>'
            . $comment['text'] . '</p><p>'
            . '评论作者:' . $comment['author'] . '</p>'
            . '<p>时间:' . date('Y-m-d H:m:s', $comment['created']) . '</p>'
            . '<p><a href="' . $archive->permalink . '" target="_blank">文章详情</a></p>'
            . '<p>本邮件为<a href="' . Helper::options()->siteUrl . '">' . Helper::options()->title . '</a>自动发送，请勿直接回复</p>';
        sendto($mailserver, $port, $mailuser, $mailpass, $mailto, $subject, $content);
        if ($parentId != 0) {
            $parent = $db->fetchRow($db->select('mail')
                ->from('table.comments')
                ->where('coid = ?', $parentId));
            if ($parent) {
                $mailto = $parent['mail'];
                $subject = '文章《' . $archive->title . '》的评论收到回复';
                $content = '<p>您在文章《' . $archive->title . '》收到回复:</p><p>'
                    . $comment['text'] . '</p><p>'
                    . '回复者:' . $comment['author'] . '</p>'
                    . '<p>时间:' . date('Y-m-d H:m:s', $comment['created']) . '</p>'
                    . '<p><a href="' . $archive->permalink . '" target="_blank">文章详情</a></p>'
                    . '<p>本邮件为<a href="' . Helper::options()->siteUrl . '">' . Helper::options()->title . '</a>自动发送，请勿直接回复</p>';
                sendto($mailserver, $port, $mailuser, $mailpass, $mailto, $subject, $content);
            }
        }
    }

    Typecho_Cookie::delete('__typecho_remember_text');
    $db->fetchRow($feedback->select()->where('coid = ?', $comment_id)
        ->limit(1), array($feedback, 'push'));
    ajax_success('评论成功');

}


/**
 * 发送邮件
 * @param $mailserver 邮件服务器
 * @param $port 端口
 * @param $mailuser 发件人用户名
 * @param $mailpass 发件人密码
 * @param $mailto 收件人
 * @param $subject 标题
 * @param $content 内容
 * @return bool 是否发送成功
 */
function sendto($mailserver, $port, $mailuser, $mailpass, $mailto, $subject, $content)
{
    $smtp = new \smtp($mailserver, $port, true, $mailuser, $mailpass);
    $mailType = "HTML";
    return $smtp->sendmail($mailto, $mailuser, $subject, $content, $mailType);
}

/**
 * 上传文件
 */
function upload()
{
    $allowedExts = array("gif", "jpeg", "jpg", "png");
    $temp = explode(".", $_FILES["file"]["name"]);
    echo $_FILES["file"]["size"];
    $extension = end($temp);     // 获取文件后缀名
    if ((($_FILES["file"]["type"] == "image/gif")
            || ($_FILES["file"]["type"] == "image/jpeg")
            || ($_FILES["file"]["type"] == "image/jpg")
            || ($_FILES["file"]["type"] == "image/pjpeg")
            || ($_FILES["file"]["type"] == "image/x-png")
            || ($_FILES["file"]["type"] == "image/png"))
        && ($_FILES["file"]["size"] < 2 * 1024 * 1024)
        && in_array($extension, $allowedExts)) {
        $res = [];
        if ($_FILES["file"]["error"] > 0) {
            $res = [
                'errno' => 1,
            ];
        } else {
            $arr = explode(".", $_FILES["file"]["name"]);
            $hz = $arr[count($arr) - 1];
            $name = gmmktime() . '.' . $hz;
            $filename = __TYPECHO_ROOT_DIR__ . '/upload/' . date('Ymd') . '/' . $name;
            $dir = dirname($filename);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            move_uploaded_file($_FILES["file"]["tmp_name"], $filename);
            $res = [
                'errno' => 0,
                'data' => [
                    [
                        'url' => 'upload/' . date('Ymd') . '/' . $name,
                        'alt' => "",
                        'href' => ''
                    ]
                ]
            ];

        }
    } else {
        $res = [
            'errno' => 1,
        ];
    }

    ob_clean();
    echo json_encode($res);
    exit();
}


/**
 * 检查验证码
 * @param $code 用户输入的验证码
 * @return bool 是否成功
 */
function check_code($user_code)
{
    session_start();
    $code = strtolower($_SESSION["freewind_code"]);
    $_SESSION["freewind_code"] = Typecho_Common::randString(4);
    $user_code = strtolower($user_code);
    return $code == $user_code;
}

function support_add($cid)
{
    $db = Typecho_Db::get();
    $row = $db->fetchRow($db->select('support')->from('table.contents')->where('cid = ?', $cid));
    $support = Typecho_Cookie::get('extend_contents_support');
    if (empty($support)) {
        $support = array();
    } else {
        $support = explode(',', $support);
    }
    if (!in_array($cid, $support)) {
        $db->query($db->update('table.contents')->rows(array('support' => (int)$row['support'] + 1))->where('cid = ?', $cid));
        array_push($support, $cid);
        $support = implode(',', $support);
        Typecho_Cookie::set('extend_contents_support', $support);
        return $row['support'] + 1;
    } else {
        return false;
    }
}

/**
 * 生成验证码
 */
function get_code()
{
    ob_clean();
    $image = new VerifyCode();//将类实例化为对象
    $image->show();//调用函数
}


/**
 * Freewind主题Ajax登录
 * @param Widget_Archive $archive
 */
function freewind_login(Widget_Archive $archive)
{
    $login = Typecho_Widget::widget('Widget_Login');
    $options = Helper::options();
    $user = Typecho_Widget::widget('Widget_User');
//    $login->security->protect();
    if ($user->hasLogin()) {
        ajax_error("当前用户已登录");
    }
    $validator = new Typecho_Validate();
    $validator->addRule('name', 'required', _t('请输入用户名'));
    $validator->addRule('password', 'required', _t('请输入密码'));

    $login_info = [
        'name' => $archive->request->get('name'),
        'password' => $archive->request->get('password'),
    ];

    if ($error = $validator->run($login_info)) {
        //返回第一条错误信息
        validator_error($error);
    }

    $valid = $user->login($login_info['name'], $login_info['password'], false,
        1 == $archive->request->remember ? $options->time + $options->timezone + 30 * 24 * 3600 : 0);

    /** 比对密码 */
    if (!$valid) {
        /** 防止穷举,休眠3秒 */
        sleep(3);

        $login->pluginHandle()->loginFail($user, $login_info['name'],
            $login_info['password'], 1 == $archive->request->remember);

        Typecho_Cookie::set('__typecho_remember_name', $login_info['name']);
        ajax_error(_t('用户名或密码无效'));
    }

    $login->pluginHandle()->loginSucceed($user, $login_info['name'],
        $login_info['password'], 1 == $archive->request->remember);

    ajax_success(_t(' 登录成功'));

}

/**
 * Freewind主题Ajax注册
 * @param Widget_Archive $archive
 * @throws Typecho_Exception
 */
function freewind_regist(Widget_Archive $archive)
{
    $register = Typecho_Widget::widget('Widget_Register');
    $options = Helper::options();
    $user = Typecho_Widget::widget('Widget_User');
    $db = Typecho_Db::get();

    /** 如果已经登录 */
    if ($user->hasLogin()) {
        ajax_error(_t('当前用户已登录'));
    }
    if (!$options->allowRegister) {
        ajax_error(_t('站点已经关闭了注册功能'));
    }

    if (!check_code($archive->request->get('imgcode'))) {
        ajax_error(_t('验证码错误'));
    }


    $validator = new Typecho_Validate();
    $validator->addRule('name', 'required', _t('必须填写用户名称'));
    $validator->addRule('name', 'minLength', _t('用户名至少包含5个字符'), 5);
    $validator->addRule('name', 'maxLength', _t('用户名最多包含32个字符'), 32);
    $validator->addRule('name', 'xssCheck', _t('请不要在用户名中使用特殊字符'));
    $validator->addRule('name', array($register, 'nameExists'), _t('用户名已经存在'));
    $validator->addRule('screenName', 'required', _t('用户昵称不能为空'));
    $validator->addRule('mail', 'required', _t('必须填写电子邮箱'));
    $validator->addRule('mail', array($register, 'mailExists'), _t('电子邮箱地址已经存在'));
    $validator->addRule('mail', 'email', _t('电子邮箱格式错误'));
    $validator->addRule('mail', 'maxLength', _t('电子邮箱最多包含200个字符'), 200);
    $validator->addRule('password', 'required', _t('必须填写密码'));
    $validator->addRule('password', 'minLength', _t('为了保证账户安全, 请输入至少六位的密码'), 6);
    $validator->addRule('password', 'maxLength', _t('为了便于记忆, 密码长度请不要超过十八位'), 18);
    $validator->addRule('confirm', 'confirm', _t('两次输入的密码不一致'), 'password');
    $regist_user = $archive->request->from('name', 'screenName', 'password', 'mail', 'confirm');
    if ($error = $validator->run($regist_user)) {
        validator_error($error);
    }
    $hasher = new PasswordHash(8, true);

    $regist_user['password'] = $hasher->HashPassword($regist_user['password']);
    $regist_user['created'] = $options->time;
    $regist_user['group'] = 'subscriber';

    unset($regist_user['confirm']);

    $regist_user = $register->pluginHandle()->register($regist_user);
    $insertId = $db->query($db->insert('table.users')->rows($regist_user));
    if (!$insertId) {
        ajax_error(_t('注册失败'));
    }
    $result = $db->fetchRow($db->select()->from('table.users')->where('uid = ?', $insertId)
        ->limit(1));
    if (!$result) {
        ajax_error(_t('注册失败'));
    }
    $register->pluginHandle()->finishRegister($register);
    $user->login($regist_user['name'], $regist_user['password'], false,
        1 == $archive->request->remember ? $options->time + $options->timezone + 30 * 24 * 3600 : 0);
    Typecho_Cookie::delete('__typecho_first_run');
    Typecho_Cookie::delete('__typecho_remember_name');
    Typecho_Cookie::delete('__typecho_remember_mail');

    ajax_success(_t('注册成功，请返回登录'));
}


function validator_error($error)
{
    foreach ($error as $k => $v) {
        ajax_error($v);
    }
}

/**
 * Ajax返回
 * @param false $success 是否成功
 * @param string $msg 消息
 * @param array $data 数据
 */
function ajax_normail($success = false, $msg = '', $data = [])
{
    $result = [
        'success' => $success,
        'msg' => $msg,
        'data' => $data
    ];
    ajax_return($result);
}

/**
 * Ajax返回 错误
 * @param $msg 错误信息
 */
function ajax_error($msg = '')
{
    $result = [
        'success' => false,
        'msg' => $msg
    ];
    ajax_return($result);
}

/**
 * Ajax返回 成功
 * @param string $msg 成功信息
 * @param array $data 数据
 */
function ajax_success($msg = '', $data = [])
{
    $result = [
        'success' => true,
        'msg' => $msg,
        'data' => $data
    ];
    ajax_return($result);
}

/**
 * Ajax返回  自定义
 * @param false[] $data
 */
function ajax_return($data = ['success' => false])
{
    ob_clean();
    echo json_encode($data);
    exit();
}