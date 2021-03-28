$(function () {
    let themeUrl = $("#setting-selector #check-update").data('theme');
    $.getScript('https://cdn.bootcdn.net/ajax/libs/layer/3.1.1/layer.min.js')
    console.log(themeUrl)
    $(".main .row.typecho-page-main div[role='form']")
        .removeClass('col-tb-8')
        .removeClass('col-tb-offset-2')
        .addClass('col-tb-12')
    $(".main .row.typecho-page-main div[role='form'] > form")
        .addClass('col-mb-12 col-tb-9')
        .prepend(`<div class="form-item update-info">${$('#notice').html()}</div>`)
    $('.update-info').stop().fadeIn();
    $("#setting-selector li a").on('click', function () {
        let current = $(this).data('target');
        $('.form-item').stop().fadeOut();
        $(`.${current}`).stop().fadeIn();
        $('#setting-selector li a.current').removeClass('current')
        $(this).addClass('current')
        return false;
    })
    $('input[name=freeMailRevice]').after(`<a href="javascript:void(0)" id="mail-test">发送测试邮件</a>`)

    $('#mail-test').on('click', function () {
        let loadIndex = layer.load(1, {
            shade: [0.1, '#fff'] //0.1透明度的白色背景
        });
        $.ajax({
            url: '/?freeAction=mail',
            dataType: 'json',
            success: res => {
                layer.close(loadIndex)
                layer.msg(res.msg, {icon: res.success ? 1 : 2})
            },
            error: res => {
                layer.close(loadIndex)
            }
        })
    })

    $("#check-update").on('click', function () {
        let index = layer.load(1, {
            shade: [0.1, '#fff'] //0.1透明度的白色背景
        });
        $.ajax({
            url: `${themeUrl}utils/update.php?check=update`,
            success: res => {
                layer.close(index)
                let result = JSON.parse(res)
                if (!result.updated) {
                    layer.msg(result.msg, {icon: result.success ? 1 : 2})
                } else {
                    let cIndex = layer.confirm(result.msg, {btn: ['更新', "取消"]}, function () {
                        layer.close(cIndex)
                        let upIndex = layer.load(1, {
                            shade: [0.1, '#fff'] //0.1透明度的白色背景
                        });
                        $.ajax({
                            url: themeUrl + "utils/update.php?updated=true",
                            type: 'POST',
                            dataType: "json",
                            success: res => {
                                layer.close(upIndex)
                                layer.msg(res.msg, {icon: 1})
                            },
                            error: res => {
                                layer.msg("出现未知错误", {icon: 2})
                                layer.close(upIndex)
                            }
                        })
                    });
                }
            }
        })
    })


})
