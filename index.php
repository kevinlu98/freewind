<?php
/**
 * Freewind一一自由之风
 *
 * @package 功能齐全，加入说说、时间轴、动态统计图表等功能，支持很多个性化操作，更多信息请移步至官网查看
 * @author 冷文博客
 * @version 1.0
 * @link https://kevinlu98.cn
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('common/header.php');
?>

<?php
if ($_GET['act'] == 'statistics') {
    $this->need('common/statistics.php');
}
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
<div class="blog-list">
    <?php if ($this->is('index')) : ?>
        <?php if ($this->options->freeRecomTitle): ?>
            <div class="recommend pos-rlt border-circular bottom-shadow"
                 style="background-image:url(<?php echo $this->options->freeRecomBg ?>);">
                <div class="recommend-info pos-abs">
                    <h3 class="no-marge pos-rlt">
                        <span>站长推荐</span>
                        <a href="<?php echo $this->options->freeRecomLink ?>"> <?php echo $this->options->freeRecomTitle ?></a>
                    </h3>
                    <p class="no-marge hidden-xs"><?php echo content_summery($this->options->freeRecom) ?></p>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php while ($this->next()): ?>
        <?php if ($this->fields->kind == 2): ?>
            <div class="blog-item">
                <div class="shuo-title pos-rlt">
                    <div class="shuo-avatar pos-abs">
                        <?php echo $this->author->gravatar(50); ?>
                    </div>
                    <div class="shuo-info">
                        <p class="author-name"><?php echo $this->title(); ?></p>
                        <p class="time"><?php $this->date('Y-m-d H:i:s'); ?></p>
                    </div>
                </div>
                <div class="shuo-content">
                    <?php echo $this->content ?>
                </div>
                <p class="shuo-footer">
                    <i class="iconfont icon-eye"> <?php get_post_view($this) ?></i>
                    <i class="iconfont icon-comment1"> <a
                                href="<?php $this->permalink() ?>"><?php echo $this->commentsNum ?> 回复</a> </i>
                    <?php $suport = get_post_support($this->cid) ?>
                    <i class="iconfont <?php echo $suport['icon'] ?>">
                        <a class="post-suport"
                           data-cid="<?php echo $this->cid ?>"
                           href="javascript:void (0)">
                            <?php echo '('.$suport['count'] .')'. $suport['text'] ?>
                        </a>
                    </i>
                </p>
            </div>
            <?php $comments = get_comment_by_cid($this->cid) ?>
            <?php if ($comments): ?>
                <div class="index-comments">
                    <ul>
                        <?php foreach ($comments as $comment): ?>
                            <li class="pos-rlt">
                                <div class="comment-avatar pos-abs">
                                    <?php if ($comment['authorId'] == $comment['ownerId']): ?>
                                        <?php echo $this->author->gravatar(32); ?>
                                    <?php else: ?>
                                        <img src="<?php get_avatar($comment['mail']) ?>" alt="">
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
        <?php else: ?>
            <div class="blog-item bottom-shadow">
                <?php if ($this->fields->show == 3): ?>
                    <div class="big-img">
                        <img src="<?php $this->fields->showImg() ?>" alt="">
                    </div>
                <?php endif; ?>
                <h3 class="no-marge">
                    <?php $categories = $this->categories ?>
                    <?php foreach ($categories as $category): ?>
                        <a class="hide-md hide-sm hidden-xs badge-green"
                           href="<?php echo $category['permalink'] ?>"><?php echo $category['name'] ?></a>
                    <?php endforeach; ?>
                    <a href="<?php $this->permalink() ?>">
                        <?php $this->title() ?>
                        <?php if ($this->password): ?>
                            <i class="iconfont icon-mimasuolock"></i>
                        <?php endif; ?>
                    </a>
                </h3>
                <p class="item-desc"><?php echo content_summery($this->content) ?>    </p>
                <p class="item-footer no-marge">
                    <?php $suport = get_post_support($this->cid) ?>
                    <i class="iconfont <?php echo $suport['icon'] ?>">
                        <a class="post-suport"
                           data-cid="<?php echo $this->cid ?>"
                           href="javascript:void (0)">
                            <?php echo '('.$suport['count'] .')'. $suport['text'] ?>
                        </a>
                    </i>
                    <i class="iconfont icon-user1"> <?php $this->author(); ?></i>
                    <i class="iconfont icon-clock1"> <?php $this->date('Y-m-d'); ?></i>
                    <i class="iconfont icon-eye"> <?php get_post_view($this) ?></i>
                    <i class="iconfont icon-comment1"> <?php $this->commentsNum('%d条评论'); ?>    </i>
                    <i class="iconfont icon-tags-o"><?php $this->tags(""); ?></i>
                </p>
            </div>
        <?php endif; ?>
    <?php endwhile; ?>
</div>
<?php $this->pageNav('<i class="iconfont icon-angle-left"></i>', '<i class="iconfont icon-angle-right"></i>', '5', '...'); ?>

<?php $this->need('common/footer.php'); ?>
