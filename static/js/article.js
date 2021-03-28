$(function () {
    $("#write pre").before(`
<div class="mac-bar pos-rlt">
<i></i><i></i><i></i>
<span class="copy-tips pos-abs">复制</span>
<a href="javascript:void(0);" class="copy-btn pos-abs iconfont icon-clipboardxieziban"></a>

</div>`)
    let comText = $('#comment-text')
    let comName = $('#com-name')
    let comMail = $('#com-mail')
    let comUrl = $('#com-url')
    let comPid = $("#comPid");
    let themeUrl = $("#theme-url").data('theme')
    $.fn.serializeJson = function () {
        let serializeObj = {};
        $(this.serializeArray()).each(function () {
            serializeObj[this.name] = this.value;
        });
        return serializeObj;
    };

    createId("#write")
    let topEle = $("#blog-tree").parent()
    let headHight = $("#app-header").height() + 20;
    let offsetTop = topEle.offset().top;
    let showPos = offsetTop + 90;


    let commentSubmit = function () {
        if (!comText.val()) {
            layer.msg("请输入评论内容", {icon: 2})
            return false;
        }
        if (!comName.val()) {
            layer.msg("请输入用户昵称，可通过填写QQ号快速获取信息", {icon: 2})
            return false;
        }
        if (!comMail.val()) {
            layer.msg("请输入邮箱，可通过填写QQ号快速获取信息", {icon: 2})
            return false;
        } else {
            let mailReg = /^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/
            if (!mailReg.test(comMail.val())) {
                layer.msg("邮箱格式不正确，可通过填写QQ号快速获取信息", {icon: 2})
                return false;
            }
        }

        let url = $(this).attr('action');
        let method = $(this).attr('method');
        let data = $(this).serializeJson();
        let pname = $("#comPname");
        console.log(pname.val());
        if (pname.val())
            data['text'] = `<p><span class="parent-name">@${pname.val()}</span></p>` + data['text'];
        let loadIndex = layer.load(0, {shade: false});
        $.ajax({
            url: url,
            type: method,
            data: data,
            dataType: 'json',
            success: res => {
                layer.close(loadIndex)
                layer.msg(res.msg, {icon: res.success ? 1 : 2}, function () {
                    if (res.success) {
                        location.reload();
                    }
                })
            },
            error: res => {
                layer.close(loadIndex)
            }
        })
        return false;
    }
    let getQQInfn = function () {
        let qq = $(this).val();
        if (qq) {
            let loadIndex = layer.load(0, {shade: false});
            $.ajax({
                url: "https://api.usuuu.com/qq/" + qq,
                dataType: "json",
                success: res => {
                    layer.close(loadIndex)
                    if (res.code === 200) {
                        let mail = qq + "@qq.com";
                        comMail.val(mail)
                        let name = res.data.name;
                        comName.val(name)
                        let url = `https://user.qzone.qq.com/${qq}/main`
                        comUrl.val(url)
                    } else {
                        layer.msg(res.msg, {icon: 2})
                    }
                },
                error: res => {
                    layer.close(loadIndex)
                }
            })
        }
    }

    $("#write .mac-bar .copy-btn").mouseenter(function () {
        $(this).parent().children('.copy-tips').stop().fadeIn()
    }).mouseleave(function () {
        $(this).parent().children('.copy-tips').stop().fadeOut().text('复制')
    })


    let clipboard = new Clipboard("#write .mac-bar .copy-btn", {
        text: function (trigger) {
            let copytext = $(trigger).parent().next().text();
            return copytext.trim();
        }
    });
    clipboard.on("success", function (e) {
        $(e.trigger).parent().children('.copy-tips').text('已复制!')
    });


    function createId(parent = "") {
        for (let i = 1; i < 7; i++) {
            let selector = `${parent} h${i}`;
            let elems = $(selector)
            for (let j = 0; j < elems.length; j++) {
                let id = randomStr(16);
                let elem = $(elems[j]);
                elem.attr('id', `h${i + id}`)
                elem.attr('data-level', i)
            }
        }
    }

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

    let calcToc = function () {
        if (topEle.length !== 0) {
            let bodyWidth = $("body").width();
            let scrollHieght = $(document).scrollTop();
            let windownHeight = $(window).height();
            let h = windownHeight - (offsetTop - scrollHieght);
            let tagEle = $(".tag-cloud");
            let tagBottom = tagEle.offset().top + tagEle.height();
            // console.log("tag" + tagBottom);
            // console.log("scrollHieght" + scrollHieght);
            let rightBar = $("#app-content #app-main");
            let loginRight = bodyWidth - rightBar.offset().left - rightBar.outerWidth();
            if (h > showPos && scrollHieght > tagBottom) {
                topEle.css({position: 'fixed', top: headHight, right: loginRight})
            } else {
                topEle.css({position: 'relative', top: 0, right: 0})
            }
        }
    }

    // $("#write a").attr('target','_blank')

    calcToc()


    // $(document).on("mousewheel DOMMouseScroll", calcToc);

    $(window).scroll(calcToc).resize(function () {
        calcToc()
    })


    tocbot.init({
        tocSelector: '#blog-tree',
        contentSelector: '#write',
        headingSelector: 'h1, h2, h3, h4',
        hasInnerContainers: true,
    });

    const editor = initEdit()
    console.log(editor);


    $(".comments-list .comm-title .replay-btn").on('click', function () {
        let comEle = $(".blog-content .comment-box")
        let commentbox = document.getElementById('comment-box');
        comEle.remove()
        let pid = $(this).data('parent');
        let pname = $(this).data('pname');

        $("#comPname").val(pname)
        comPid.val(pid)
        $(this).after(`<a class="no-replay" href="javascript:void (0);">取消回复</a>`)
        this.parentNode.parentNode.appendChild(commentbox)
        $("#comment-form").submit(commentSubmit)
        $('#com-qq').blur(getQQInfn)
        $(".comments-list .comm-title .no-replay").on('click', function () {
            console.log($(this));
            comEle.remove()
            $(this).remove()
            $(".blog-content .comments-list").before(commentbox)
            comPid.val(0)
            $("#comPname").val('')
            $("#comment-form").submit(commentSubmit)
            $('#com-qq').blur(getQQInfn)
        })
    })


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
        const editor = new E("#common-edit")
        editor.config.height = 150
        editor.config.placeholder = '你的评论一针见血'
        editor.config.menus = [
            'bold',
            'italic',
            'underline',
            'strikeThrough',
            'foreColor',
            'link',
            'emoticon',
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

        editor.config.onchange = function (html) {
            comText.val(html)
        }
        editor.config.onchangeTimeout = 500
        editor.config.showFullScreen = false
        editor.config.zIndex = 1010
        editor.create()
        comText.val(editor.txt.html())
        return editor;
    }

    $('#com-qq').blur(getQQInfn)
    // comQQ.blur(function () {
    //
    //
    // })

    $("#comment-form").submit(commentSubmit)


})