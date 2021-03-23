<?php
/**
 * 文章归档
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
    <div class="page-bg bg-whitefff" style="padding: 30px 10px 10px;box-sizing: border-box">
        <?php $this->widget('Widget_Contents_Post_Recent', 'pageSize=10000')->to($archives); ?>
        <?php
        $year = 0;
        $mon = 0;
        $i = 0;
        $j = 0;
        $output = '';
        ?>
        <?php while ($archives->next()):
            $year_tmp = date('Y', $archives->created);
            $mon_tmp = date('m', $archives->created);
            $y = $year;
            $m = $mon;
            if ($mon != $mon_tmp || $year != $year_tmp) {
                $year = $year_tmp;
                $mon = $mon_tmp;
                if (!empty($output))
                    $output .= '</ul></div>';
                $output .= '<div class="archive-item"><h3><i class="iconfont icon-calculator"></i>' . date('Y年m月', $archives->created) . '</h3><ul>';
            }
            $output .= '<li>' . date('Y-m-d', $archives->created) . '<a href="' . $archives->permalink . '">' . $archives->title . '</a></li>';
        endwhile;
        $output .= '</ul></div>';
        echo $output;
        ?>
    </div>
</div>

<?php $this->need('common/footer.php'); ?>
