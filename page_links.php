<?php
/**
 * 友情链接
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('common/header.php');
?>

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
    <div id="write">
        <?php $this->content(); ?>
    </div>
    <div class="bg-whitefff page-link">
        <?php $links = split_theme_fields($this->options->freeLinks); ?>
        <div style="margin: 0;padding: 10px;">
            <?php foreach ($links as $link): ?>
                <a class="link-item pos-rlt" href="<?php echo $link[1] ?>" target="_blank">
                    <img class="pos-abs" src="<?php echo $link[1] ?>/favicon.ico" alt="">
                    <h2><?php echo $link[0] ?></h2>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php $this->need('common/comments.php'); ?>
</div>

<?php $this->need('common/footer.php'); ?>
<script>
    $(function () {

    })
</script>
