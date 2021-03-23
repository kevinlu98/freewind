<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php $this->options->title() ?>-资源下载</title>
    <style>
        .down-info .site-info h1 {
            color: #fff !important;
            display: inline-block;
            font-weight: 100;
            font-size: 48px;
            margin: 0 20px 0 0;
        }

        .down-info .site-info h3 {
            margin: 0;
            font-weight: 100;
            font-size: 24px;
            letter-spacing: 30px;
            display: inline-block;
        }

        .down-info .site-info {
            margin-top: 100px;
            width: 550px;
            margin-left: 50%;
            transform: translateX(-50%);
            color: #fff;
        }

        .down-info .down-link {
            margin-top: 100px;
            width: 550px;
            margin-left: 50%;
            text-align: center;
            transform: translateX(-50%);
        }

        .down-link a {
            text-decoration: none;
            min-width: 250px;
            height: 50px;
            line-height: 50px;
            display: inline-block;
            font-size: 18px;
            padding-left: 50px;
            box-sizing: border-box;
            position: relative;
            margin: 10px;
        }

        .down-link .bdy {
            background-color: #fff;
            color: #161823;
        }

        .down-link .lzy {
            background-color: #FC0156;
            color: #fff;
        }

        .down-link img {
            width: 40px;
            height: 40px;
            position: absolute;
            top: 5px;
            left: 35px;
        }

        .guanfang-link {
            width: 550px;
            margin-left: 50%;
            transform: translateX(-50%);
            color: #ccc;
            position: relative;
            border-top: 1px solid #ccc;
            margin-top: 10px;
            padding-top: 10px;
            text-align: center;
        }

        .guanfang-link span {
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #161823;
        }

        .guanfang-link a {
            text-decoration: none;
            width: 250px;
            height: 50px;
            line-height: 50px;
            display: inline-block;
            font-size: 18px;
            text-align: center;
            box-sizing: border-box;
            position: relative;
            margin: 10px;
            border: 1px solid #ccc;
            color: #ccc;
        }

        .down-desc {
            margin-top: 100px;
            width: 550px;
            margin-left: 50%;
            transform: translateX(-50%);
        }

        .down-desc h3 {
            font-weight: 100;
            color: #fff;
            padding-bottom: 10px;
            border-bottom: 1px solid #fff;
            margin: 0 0 10px;
        }

        .down-desc ol {
            padding-left: 20px;
            color: #fff;
        }
    </style>
</head>
<body style="background-color:#161823;">
<div class="down-info">
    <div class="site-info">
        <h1><?php $this->options->title() ?></h1>
        <span>让崇拜从这里开始!</span>
        <br>
        <h3>技术共享</h3><span style="color: #ccc">致力创造一个高质量的技术资源分享平台</span>
    </div>
    <div class="down-link">
        <?php $baidu = baidu_url_pwd($this->fields->fileBaidu); ?>
        <a class="bdy" href="<?php echo $baidu['url'] ?>" target="_blank">
            <img src="https://imagebed-1252410096.cos.ap-nanjing.myqcloud.com/20210131/a9f20dc440774f4a9e21584783e12355.png"
                 alt="">
            百度云盘(<?php echo (!strlen($this->fields->fileBaidu)) ? '暂不支持' : ($baidu['pwd'] ? $baidu['pwd'] : '无密码') ?>)
        </a>
        <a class="lzy" href="<?php $this->fields->fileLan() ?>" target="_blank">
            <img src="https://imagebed-1252410096.cos.ap-nanjing.myqcloud.com/20210131/5b05abbe17674b58bf85a125343b1165.png"
                 alt="">
            蓝奏云<?php echo (!strlen($this->fields->fileLan)) ? '(暂不支持)' : '' ?>
        </a>
    </div>
    <div class="guanfang-link">
        <span>or</span>
        <a href="<?php $this->fields->fileGuan() ?>">
            官方下载<?php echo (!strlen($this->fields->fileGuan)) ? '(暂不支持)' : '' ?></a>
    </div>
    <div class="down-desc">
        <h3>下载说明</h3>
        <?php $this->options->downPageDesc() ?>
    </div>
</div>
</body>
</html>

