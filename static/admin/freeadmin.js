$(function () {
    let turndownService = new TurndownService()
    let converter = new showdown.Converter();
    let themeUrl = $("#theme-url").data('theme')

    function initEdit() {
        let emotions;
        let expression;
        $.ajax({
            url: themeUrl + 'static/json/emotions.json',
            async: false,
            success: res => {
                emotions = res;
            }
        })
        $.ajax({
            url: themeUrl + 'static/json/expression.json',
            async: false,
            success: res => {
                expression = res;
            }
        })

        const E = window.wangEditor
        const editor = new E("#diary-edit")
        console.log(editor.config);
        editor.config.height = 150
        editor.config.placeholder = '你的评论一针见血'
        editor.config.menus = [
            'link',
            'emoticon',
            'image',
            'undo',
            'redo',
        ]

        editor.config.emotions = [{
            title: 'QQ表情',
            type: 'image',
            content: emotions
        }, {
            title: '其它表情',
            type: 'image',
            content: expression
        }
        ]
        editor.config.height = 500
        editor.config.uploadImgMaxSize = 2 * 1024 * 1024 // 2M
        editor.config.uploadImgMaxLength = 1
        editor.config.uploadFileName = 'file'
        editor.config.uploadImgServer = '/?freeAction=upload'
        editor.config.showLinkImgAlt = false
        editor.config.showLinkImgHref = false
        editor.config.uploadImgHooks = {
            customInsert: function (insertImgFn, result) {
                console.log(result);
                if (!editor.txt.html().endsWith('br>'))
                    editor.txt.html(editor.txt.html() + '<br>')
                insertImgFn('/' + result.data[0].url)
            }
        }
        editor.config.onchange = function (html) {
            html = html.replaceAll('[', '').replaceAll(']', '')
            console.log(html)
            let content = turndownService.turndown(html)
            console.log(content);
            textBox.val(content)
        }
        editor.config.onchangeTimeout = 500
        editor.config.showFullScreen = false
        editor.config.zIndex = 1010
        editor.create()
        editor.txt.html(converter.makeHtml(textBox.val()))
        textBox.val(turndownService.turndown(editor.txt.html()))
        return editor;
    }

    let btnBar = $("#wmd-button-bar")
    let textBox = $("#wmd-editarea textarea")
    let editor = null;
    textBox.before(`<div id="diary-edit"></div>`)
    $("#diary-edit").css('display', 'none')

    let textContent = textBox.val();

    $("#custom-field select[name='fields[kind]']").on('change', function () {
        console.log(111);
        let kind = $(this);
        if (kind.val() === '2') {
            btnBar.css('display', 'none')
            textBox.attr('hidden', 'textarea')
            $("#diary-edit").css('display', 'block')
            textContent = textBox.val()
            editor = initEdit();
            $("#edit-secondary").css('display', 'none')
            $("#title").parent().parent().removeClass('col-tb-9').addClass('col-tb-12')
            $(".typecho-page-title>h2").text('发表说说')
        } else {
            btnBar.css('display', 'block')
            textBox.removeAttr('hidden')
            textBox.val(textContent)
            editor.destroy();
            editor = null;
            $("#diary-edit").css('display', 'none')
            $("#edit-secondary").css('display', 'block')
            $("#title").parent().parent().removeClass('col-tb-12').addClass('col-tb-9')
            $(".typecho-page-title>h2").text('撰写新文章')
        }
    }).change()

    function randomStr(number = 4) {
        let str = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM'
        let len = str.length
        let result = ""
        for (let i = 0; i < number; i++) {
            let index = parseInt(Math.random() * len);
            result += str[index]
        }
        return result
    }

})