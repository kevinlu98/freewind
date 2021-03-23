<?php
/**
 * 说说中心
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
    <div class="blog-content" style="min-height: 100vh">
        <div class="crumbs">
            <a href="<?php $this->options->siteUrl(); ?>"><i class="iconfont icon-home"></i> 首页</a> <i
                    class="split">/</i>
            <strong><?php $this->archiveTitle(array(
                    'category' => _t('分类 %s 下的文章'),
                    'search' => _t('包含关键字 %s 的文章'),
                    'tag' => _t('标签 %s 下的文章'),
                    'author' => _t('%s 发布的文章')
                ), '', ''); ?></strong>
        </div>
        <div class="blog-list">
            <?php $this->widget('Widget_Post_Shuo@shuo')->to($shuo); ?>

            <?php while ($shuo->next()): ?>
                <div class="blog-item">
                    <div class="shuo-title pos-rlt">
                        <div class="shuo-avatar pos-abs">
                            <?php echo $shuo->author->gravatar(32); ?>
                        </div>
                        <div class="shuo-info">
                            <p class="author-name"><?php echo $shuo->title(); ?></p>
                            <p class="time"><?php $shuo->date('Y-m-d H:i:s'); ?></p>
                        </div>
                    </div>
                    <div class="shuo-content">
                        <?php echo $shuo->content ?>
                    </div>
                    <p class="shuo-footer">
                        <i class="iconfont icon-eye"> <?php get_post_view($shuo) ?></i>
                        <i class="iconfont icon-comment1"> <a
                                    href="<?php $shuo->permalink() ?>"><?php echo $shuo->commentsNum ?> 回复</a> </i>
                    </p>
                </div>
                <?php $comments = get_comment_by_cid($shuo->cid) ?>
                <?php if ($comments): ?>
                    <div class="index-comments">
                        <ul>
                            <?php foreach ($comments as $comment): ?>
                                <li class="pos-rlt">
                                    <div class="comment-avatar pos-abs">
                                        <?php if ($comment['authorId'] == $comment['ownerId']): ?>
                                            <?php echo $shuo->author->gravatar(32); ?>
                                        <?php else: ?>
                                            <img src="<?php  get_avatar($comment['mail']) ?>" alt="">
                                        <?php endif; ?>
                                    </div>
                                    <div class="comment-body">
                                        <div class="comment-head"><?php echo $comment['author'] ?>
                                            <?php if ($comment['authorId'] == $comment['ownerId']): ?>
                                                <strong class='admin'>管理员</strong>
                                            <?php endif; ?>
                                            <span><?php echo date('Y-m-d H:i:s', $comment['created']) ?></span>
                                        </div>
                                        <div class="comment-content">
                                            <?php echo preg_replace("/<br>|<p>|<\/p>/", ' ', $comment['text']) ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
        <?php $shuo->pageNav('<i class="iconfont icon-angle-left"></i>', '<i class="iconfont icon-angle-right"></i>', '5', '...'); ?>
    </div>
<?php $this->need('common/footer.php'); ?>