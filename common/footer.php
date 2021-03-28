<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<footer id="app-footer">
    友情链接:
    <?php $links = split_theme_fields($this->options->freeLinks); ?>
    <?php $index = 1 ?>
    <?php foreach ($links as $link): ?>
        <?php
        if ($this->options->freeLinkNum != '0' && $index++ > (int)$this->options->freeLinkNum) {
            break;
        }
        ?>
        <a href="<?php echo $link[1] ?>" target="_blank"><?php echo $link[0] ?></a>
    <?php endforeach; ?>
    <br>
    <?php $this->options->freeFooter() ?>
</footer>
</div>
<div class="right-bar bg-white hide-md">
    <div class="right-tab">
        <ul class="pos-rlt bottom-shadow">
            <li data-select="hot-selector" class="active"><a href="javascript:void (0);"><i
                            class="iconfont icon-hot"></i></a></li>
            <li data-select="new-selector"><a href="javascript:void (0);"><i class="iconfont icon-new"></i></a>
            </li>
            <span class="pos-abs"></span>
        </ul>
        <div id="hot-selector" class="right-item select-item bottom-shadow current">
            <h3>热门文章</h3>
            <div class="right-item-body">
                <ul class="item-blog-list">
                    <?php $this->widget('Widget_Post_hot@hot', 'pageSize=4')->to($hot); ?>
                    <?php while ($hot->next()): ?>
                        <li>
                            <a href="<?php $hot->permalink() ?>"><?php $hot->title(); ?></a>
                            <p><i class="iconfont icon-eye"><?php get_post_view($hot) ?> </i><i
                                        class="iconfont icon-clock1"><?php $hot->date('Y-m-d'); ?> </i></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
        <div id="new-selector" class="right-item select-item bottom-shadow">
            <h3>最新评论</h3>
            <div class="right-item-body">
                <ul class="item-comment-list">
                    <?php $this->widget('Widget_Comments_Recent')->to($comments); ?>
                    <?php while ($comments->next()): ?>
                        <li class="pos-rlt">
                            <div class="pos-abs avatar shadow">
                                <img src="<?php  get_avatar($comments->mail) ?>" alt="">
                            </div>
                            <strong><?php $comments->author(false); ?></strong>
                            <p class="comm"><?php echo preg_replace("/<br>|<p>|<\/p>/", ' ', $comments->text) ?></p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="right-item">
        <h3>日历</h3>
        <div class="right-item-body">
            <p class="calendar-title">
                <?php echo date('Y年m月') ?>
                <?php echo print_calendar() ?>
        </div>
    </div>
    <div class="right-item">
        <h3>标签云</h3>
        <div class="right-item-body tag-cloud">
            <?php Typecho_Widget::widget('Widget_Metas_Tag_Cloud')->to($tags); ?>
            <?php if ($tags->have()): ?>
                <?php $index = 0 ?>
                <?php while ($tags->next()):
                    if ($index++ >= 30) break;
                    ?>
                    <a href="<?php $tags->permalink(); ?>" title="<?php $tags->name(); ?>">
                        <?php $tags->name(); ?></a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php if ($this->is('post')): ?>
        <div class="right-item">
            <h3>文章目录</h3>
            <div id="blog-tree" class="right-item-body"></div>
        </div>
    <?php elseif ($this->is('page')): ?>
        <div class="right-item">
            <div id="blog-tree" style="opacity: 0;height: 0;width: 0;" class="right-item-body"></div>
        </div>
    <?php endif; ?>
</div>
</div>
</div>
<div id="bg-cover" class="pos-fix">
    <div class="search-sm-form pos-rlt shadow">
        <button class="iconfont icon-close pos-abs cover-close"></button>
        <div class="input-item">
            <form method="post" id="search-form" action="">
                <input type="text" name="s" size="32" id="search-content" placeholder="请输入搜索关键字...">
                <button class="pos-abs"><i class="iconfont icon-search"></i> 搜索</button>
            </form>
        </div>
        <p class="keywords-list">
            <span>推荐关键字：</span>
            <?php Typecho_Widget::widget('Widget_Metas_Tag_Cloud')->to($tags); ?>
            <?php if ($tags->have()): ?>
                <?php $index = 0 ?>
                <?php while ($tags->next()):
                    if ($index++ >= 10) break;
                    ?>
                    <a href="javascript:void (0)" data-key="<?php $tags->name(); ?>"><?php $tags->name(); ?></a>
                <?php endwhile; ?>
            <?php endif; ?>
        </p>
    </div>
</div>
</div>
<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.2.0/jquery.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/echarts/5.0.1/echarts.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/layer/3.1.1/layer.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/jquery-validate/1.9.0/jquery.validate.min.js"></script>
<?php if ($this->is('post') || $this->is('page')): ?>
    <script src="https://cdn.bootcdn.net/ajax/libs/clipboard.js/1.7.1/clipboard.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tocbot/4.11.1/tocbot.min.js"></script>
    <script src="<?php $this->options->themeUrl('static/plugin/wangEdit/wangEditor.min.js') ?>"></script>
    <script src="<?php $this->options->themeUrl('static/plugin/prism/prism.js') ?>"></script>
    <script src="https://cdn.bootcdn.net/ajax/libs/jquery-validate/1.9.0/jquery.validate.min.js"></script>
<?php endif ?>
<script src="<?php $this->options->themeUrl('static/js/script.js') ?>"></script>
<?php if ($this->is('post') || $this->is('page')): ?>
    <script src="<?php $this->options->themeUrl('static/js/article.js') ?>"></script>
<?php endif ?>
<?php $this->options->freeCss() ?>
</body>
</html>