<?php
/**
 * 时间轴
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('common/header.php');
?>
<link rel="stylesheet" href="<?php $this->options->themeUrl('static/css/timeline.css'); ?>">
<div class="bg-white main-header">
    <h1 class="no-marge">
        <?php if ($this->is('index')) : ?>
            <?php $this->options->title() ?>
        <?php else: ?>
            <?php $this->archiveTitle(array(
                'category' => _t('分类 %s 下的文章'),
                'search' => _t('包含关键字 %s 的文章'),
                'tag' => _t('标签 %s 下的文章'),
                'author' => _t('%s 发布的文章')
            ), '', ''); ?>
        <?php endif; ?>
    </h1>
    <p class="no-marge"><?php $this->options->description() ?></p>
</div>
<div class="blog-content">
    <div class="crumbs">
        <a href="<?php $this->options->siteUrl(); ?>"><i class="iconfont icon-home"></i> 首页</a> <i class="split">/</i>
        <strong><?php $this->archiveTitle(array(
                'category' => _t('分类 %s 下的文章'),
                'search' => _t('包含关键字 %s 的文章'),
                'tag' => _t('标签 %s 下的文章'),
                'author' => _t('%s 发布的文章')
            ), '', ''); ?></strong>
    </div>
    <div class="page-bg bg-whitefff" style="padding: 30px 10px 10px;box-sizing: border-box">
        <section id="cd-timeline" class="cd-container">
            <?php $this->widget('Widget_Contents_Post_Recent', 'pageSize=10000')->to($article); ?>
            <?php $colors = ['red', 'blue', 'green', 'black']; ?>
            <?php $index = 0 ?>
            <?php while ($article->next()):
                $ci = $index++ % 4;
                $color = $colors[$ci];
                ?>
                <?php $colors = ['red', 'blue', 'green', 'black']; ?>
                <div class="cd-timeline-block">
                    <div class="cd-timeline-img cd-picture <?php echo $color ?>">
                        <img src="<?php $this->options->themeUrl('static/image/time/' . $color . '.svg'); ?>"
                             alt="Picture">
                    </div>
                    <div class="cd-timeline-content <?php echo $color ?>">
                        <h2><a href="<?php echo $article->permalink ?>"><?php echo $article->title ?></a></h2>
                        <p><?php echo content_summery($article->content, 40) ?></p>
                        <span class="cd-date"><?php echo date('M d, Y', $article->created) ?></span>
                    </div>
                </div>
            <?php endwhile; ?>
        </section>
    </div>
</div>
<script src="<?php $this->options->themeUrl('static/js/modernizr.js'); ?>"></script>
<script>
    $(function () {
        var $timeline_block = $('.cd-timeline-block');
        //hide timeline blocks which are outside the viewport
        $timeline_block.each(function () {
            if ($(this).offset().top > $(window).scrollTop() + $(window).height() * 0.75) {
                $(this).find('.cd-timeline-img, .cd-timeline-content').addClass('is-hidden');
            }
        });
        //on scolling, show/animate timeline blocks when enter the viewport
        $(window).on('scroll', function () {
            $timeline_block.each(function () {
                if ($(this).offset().top <= $(window).scrollTop() + $(window).height() * 0.75 && $(this).find('.cd-timeline-img').hasClass('is-hidden')) {
                    $(this).find('.cd-timeline-img, .cd-timeline-content').removeClass('is-hidden').addClass('bounce-in');
                }
            });
        });
    });
</script>
<?php $this->need('common/footer.php'); ?>
