<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php if ($this->fields->file >= 2): ?>
    <?php if (file_view($this)): ?>
        <div class="file-down pos-rlt">
            <ul class="pos-abs">
                <li><strong>附件:</strong><?php $this->fields->fileName() ?></li>
                <li><strong>附件大小:</strong><?php $this->fields->fileSize() ?></li>
                <li><strong>更新时间:</strong><?php echo date('Y年m月d日', $this->modified); ?></li>
                <li><strong>附件下载:</strong><a target="_blank"
                                             href="<?php echo $this->permalink . '?download=' . $this->fields->fileName ?>">点此下载</a>
                </li>
            </ul>
            <div class="file-desc">
                <?php if ($this->options->downDesc): ?>
                    <?php $this->options->downDesc() ?>
                <?php else: ?>
                    <strong>下载说明</strong>: 本站大部分下载资源收集于网络，只做学习和交流使用，版权归原作者所有，若为付费资源，请在下载后24小时之内自觉删除，若作商业用途，请到原网站购买，由于未及时购买和付费发生的侵权行为，与本站无关。本站发布的内容若侵犯到您的权益，请联系本站删除，我们将及时处理！
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="file-not-view">
            <?php
            $view_type = [
                "3" => "登录可见",
                "4" => "回复可见",
                "5" => "登录回复可见"];
            ?>
            <p>
                <text>对不起，作者设置了附件<?php echo $view_type[$this->fields->file] ?></text>
            </p>
        </div>
    <?php endif ?>
<?php endif; ?>
