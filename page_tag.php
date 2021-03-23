<?php
/**
 * 标签大全
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
    <div class="page-bg">
        <div class="page-tag-cloud">
            <?php Typecho_Widget::widget('Widget_Metas_Tag_Cloud')->to($tags); ?>
            <?php if ($tags->have()): ?>
                <?php while ($tags->next()): ?>
                    <a href="<?php $tags->permalink(); ?>" title="<?php $tags->name(); ?>"><?php $tags->name(); ?>
                        (<?php $tags->count(); ?>)</a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
        <div class="page-cloud-count row">
            <div id="cloud-pie" class="col-md-12"></div>
        </div>
    </div>
</div>
<?php $this->need('common/footer.php'); ?>
<script>
    $(function () {
        let page_color = ['#0099CC', '#CC0033', '#009966', '#9933FF',
            '#993399', '#333366', '#660066', '#99CCFF']
        let page_tags = $(".page-tag-cloud a")
        for (let i = 0; i < page_tags.length; i++) {
            let tag = $(page_tags[i])
            let color_index = parseInt(Math.random() * page_color.length)
            tag.css('backgroundColor', page_color[color_index]);
        }

        let pie = document.getElementById("cloud-pie")
        let pieChart = echarts.init(pie);
        let pieOption = {
            title: {
                text: '标签统计',
                left: 'center'
            },
            tooltip: {
                trigger: 'item'
            },
            legend: {
                orient: 'vertical',
                left: 'left',
            },
            <?php
            $tags = metas_count('tag', 20);
            $tags_pei = [];
            foreach ($tags as $tag) {
                $tags_pei[] = [
                    'value' => $tag['count'],
                    'name' => $tag['name']
                ];
            }
            ?>
            series: [
                {
                    name: '访问来源',
                    type: 'pie',
                    radius: '50%',
                    data: <?php echo json_encode($tags_pei)?>,
                    emphasis: {
                        itemStyle: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        };
        pieChart.setOption(pieOption)
    })


</script>