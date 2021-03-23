$(function () {
    let themeUrl = $("#theme-url").data('theme')
    $.getScript("https://cdn.jsdelivr.net/npm/wangeditor@latest/dist/wangEditor.min.js", function () {
        $.getScript('https://cdn.bootcdn.net/ajax/libs/turndown/7.0.0/turndown.min.js', function () {
            $.getScript('https://cdn.bootcss.com/showdown/1.3.0/showdown.min.js', function () {
                $.getScript(`${themeUrl}static/admin/freeadmin.js`)
            })
        })
    })
})