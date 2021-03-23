$(function () {

    let categoryCtx = document.getElementById("category")
    let categoryChart = echarts.init(categoryCtx);
    let categoryOption = {
        title: {
            text: '分类雷达图',
            textStyle: {
                fontSize: 14,
                fontWeight: 'light'
            }
        },
        radar: {
            center: ['50%', '50%'],
            radius: 50,
            name: {
                textStyle: {
                    color: '#111',
                    borderRadius: 3,
                    padding: [3, 5]
                },
            },
            indicator: [
                {name: 'emlog教程', max: 8},
                {name: '网站相关', max: 8},
                {name: 'Linux学习', max: 8},
                {name: 'Mac小技能', max: 8},
                {name: '后端技术学习', max: 8},
                {name: '前端小知识', max: 8},
                {name: '其它', max: 8}
            ],
        },
        series: [{
            type: 'radar',
            color: '#3ECF8E',
            data: [
                {
                    value: [8, 5, 2, 3, 2, 3, 1],
                    areaStyle: {
                        opacity: 0.3,
                        color: '#3ECF8E'
                    },
                    fontStyle: {
                        fontSize: '12px',
                    }
                }
            ]
        }]
    };
    let themeUrl = $("#theme-url").data('theme')

    let labelsCtx = document.getElementById("labels")
    let labelsChart = echarts.init(labelsCtx);
    let labelsOption = {
        title: {
            text: '标签统计',
            textStyle: {
                fontSize: 14,
                fontWeight: 'light'
            }
        },
        grid: [{
            left: '10%',
            bottom: '30%',
            top: '15%',
            right: '10%'
        }],

        xAxis: {
            type: 'category',
            data: [],
            axisLabel: {
                color: '#333',
                //  让x轴文字方向为竖向
                interval: 0,
                formatter: function (params) {
                    let newParamsName = '' // 最终拼接成的字符串
                    let paramsNameNumber = params.length // 实际标签的个数
                    let provideNumber = 2 // 每行能显示的字的个数
                    let rowNumber = Math.ceil(paramsNameNumber / provideNumber) // 换行的话，需要显示几行，向上取整
                    //     /**
                    //  * 判断标签的个数是否大于规定的个数， 如果大于，则进行换行处理 如果不大于，即等于或小于，就返回原标签
                    //  */
                    // 条件等同于rowNumber>1
                    if (paramsNameNumber > provideNumber) {
                        /** 循环每一行,p表示行 */
                        for (let p = 0; p < rowNumber; p++) {
                            let tempStr = '' // 表示每一次截取的字符串
                            let start = p * provideNumber // 开始截取的位置
                            let end = start + provideNumber // 结束截取的位置
                            // 此处特殊处理最后一行的索引值
                            if (p === rowNumber - 1) {
                                // 最后一次不换行
                                tempStr = params.substring(start, paramsNameNumber)
                            } else {
                                // 每一次拼接字符串并换行
                                tempStr = params.substring(start, end) + '\n'
                            }
                            newParamsName += tempStr // 最终拼成的字符串
                        }
                    } else {
                        // 将旧标签的值赋给新标签
                        newParamsName = params
                    }
                    // 将最终的字符串返回
                    return newParamsName
                }// x轴文字换行

            }
        },
        yAxis: {
            type: 'value'
        },
        series: [{
            data: [],
            type: 'bar'
        }]
    };

    $.fn.serializeObject = function () {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
    let timeCtx = document.getElementById("time-statistic")
    let timeChart = echarts.init(timeCtx);
    let timeOption = {
        title: {
            text: '发布动态图',
            textStyle: {
                fontSize: 14,
                fontWeight: 'light'
            }
        },
        xAxis: {
            type: 'category',
            data: []
        },
        yAxis: {
            type: 'value'
        },
        series: [{
            data: [],
            type: 'line'
        }]
    }


    function shuoImg() {
        let contents = $('.blog-item .shuo-content');
        for (let i = 0; i < contents.length; i++) {
            let content = $(contents[i])
            let img = content.find("img[title='']");
            if (img.length === 1) {
                img.css({'width': '70%', 'height': '300px'})
            } else if (img.length === 2 || img.length === 4) {
                img.css({'width': '48%', 'height': '300px'})
            }
        }
    }

    let headHight = $("#app-header").height() + 20;

    $.validator.addMethod("usernameCheck", function (value, element, param) {
        let pattern = /^[a-zA-Z][0-9a-zA-Z_]{5,31}/
        return pattern.test(value);
    })
    $.validator.addMethod("pwdCheck", function (value, element, param) {
        let pattern = /^(?![0-9]+$)(?![a-z]+$)(?![A-Z]+$)(?!([^(0-9a-zA-Z)])+$).{6,20}$/
        return pattern.test(value);
    })

    $.validator.addMethod("codeCheck", function (value, element, param) {
        let success = false;
        $.ajax({
            url: '?imgcode=' + value,
            dataType: 'json',
            async: false,
            success: res => {
                success = res.success
            }
        })
        return success;
    })
    $.validator.addMethod("nameExists", function (value, element, param) {
        let success = false;
        $.ajax({
            url: '?checkName=' + value,
            dataType: 'json',
            async: false,
            success: res => {
                success = res.success
            }
        })
        return success;
    })
    $.validator.addMethod("mailExists", function (value, element, param) {
        let success = false;
        $.ajax({
            url: '?checkMail=' + value,
            dataType: 'json',
            async: false,
            success: res => {
                success = res.success
            }
        })
        return success;
    })

    function validRegister() {
        return $("#register-form").validate({
            rules: {
                name: {
                    required: true,
                    usernameCheck: true,
                    nameExists: true
                },
                screenName: {
                    required: true,
                },
                mail: {
                    required: true,
                    email: true,
                    mailExists: true
                },
                password: {
                    required: true,
                    pwdCheck: true
                },
                repwd: {
                    equalTo: "#password"
                },
                imgcode: {
                    required: true,
                    codeCheck: true
                }
            },
            messages: {
                name: {
                    required: "用户名不能为空",
                    usernameCheck: "用户名长度必须为6-32之间只包含字母数字下划线且必须字母开头",
                    nameExists: '用户名已存在'
                },
                screenName: {
                    required: "昵称不能为空"
                },
                mail: {
                    required: "电子邮箱不能为空",
                    email: "必须输入正确格式的电子邮件",
                    mailExists: '邮箱已被注册'
                },
                password: {
                    required: "密码不能为空",
                    pwdCheck: "密码包含 数字,英文,字符中的两种以上，长度6-20"
                },
                repwd: {
                    equalTo: "两次密码输入不一致"
                },
                imgcode: {
                    required: "验证码不能为空",
                    codeCheck: "验证码输入有误"
                }
            }
        })
    }

    $(validRegister());
    $("#code-img").on('click', function () {
        $(this).attr('src', themeUrl + 'utils/verfiy.php?time=' + new Date().getTime())
    })


    init();


    $(window).resize(function () {
        init();
        reChart();
    })

    function dealshuo() {
        let imgs = $('.shuo-content img');
        let shuoImg = []
        for (let i = 0; i < imgs.length; i++) {
            let img = $(imgs[i]);
            if (!img.attr('title')) {
                img.parent().children('br').remove()
            }
        }
    }


    function reChart() {
        $.ajax({
            url: '/?act=statistics',
            dataType: 'json',
            async: false,
            success: res => {
                let article = res['article']
                let category = res['category']
                let tag = res['tag']
                let timeX = [];
                let timeY = [];
                for (let i = 0; i < article.length; i++) {
                    timeX.push(article[i]['time'])
                    timeY.push(article[i]['count'])
                }
                timeOption.xAxis.data = timeX;
                timeOption.series = [
                    {
                        data: timeY,
                        type: 'line'
                    }
                ]
                let categoryS = [];
                let categoryI = [];
                for (let i = 0; i < category.length; i++) {
                    categoryI.push({
                        name: category[i]['name'],
                        max: category[0]['count']
                    })
                    categoryS.push(category[i]['count'])
                }
                categoryOption.radar.indicator = categoryI
                categoryOption.series[0].data[0].value = categoryS
                let tagX = []
                let tagY = []
                let tagColor = ['#FC625D', '#3ECF8E', '#73D8FF', '#AEA1FF', '#FE9200', '#FE9200', '#6FA8DC']
                for (let i = 0; i < tag.length; i++) {
                    tagX.push(tag[i]['name'])
                    tagY.push({
                        value: tag[i]['count'],
                        itemStyle: {
                            color: tagColor[i]
                        }
                    })
                }
                labelsOption.xAxis.data = tagX
                labelsOption.series = [{
                    data: tagY,
                    type: 'bar'
                }]
            }
        })
        timeChart.clear();
        timeChart.resize()
        timeChart.setOption(timeOption)
        labelsChart.clear();
        labelsChart.resize();
        labelsChart.setOption(labelsOption)
        categoryChart.clear();
        categoryChart.resize();
        categoryChart.setOption(categoryOption);
    }


    function init() {
        let navList = $("#app-aside .nav-list");
        let navTop = navList.offset().top
        let footTop = $("#app-aside .user-footer").offset().top
        let height = footTop - navTop;
        height -= 20
        if (height > 0) {
            navList.css({"height": height + "px"})
        }
        dealshuo()
        topbarPos()
        let rightSelector = $(".right-bar .right-tab>ul span");
        let activeItem = $(".right-bar .right-tab>ul .active");
        shuoImg()
        rightSelector.css({'width': activeItem.width(), 'left': activeItem.position().left, 'display': 'block'});
    }


    $(".right-bar .right-tab>ul li").on('click', function () {
        $(this).addClass('active').siblings().removeClass('active')
        let rightSelector = $(".right-bar .right-tab>ul span");
        let activeItem = $(".right-bar .right-tab>ul .active");
        rightSelector.css({'width': activeItem.width()}).stop().animate({'left': activeItem.position().left});
        let selectBody = $(this).data('select');
        $(".right-bar .right-tab .select-item.current").removeClass("current")
        $(`#${selectBody}`).addClass("current")
    })

    function topbarPos() {
        let rightBar = $("#app-content #app-main");
        let bodyWidth = $("body").width();

        let loginRight = bodyWidth - rightBar.offset().left - rightBar.outerWidth();
        $("#login-pain").css({"right": loginRight, "top": headHight})
        let whisperRight = $("#login-bar-btn").outerWidth() + loginRight;
        $("#whisper-pain").css("right", whisperRight)
    }

    $("#app-statistic").on('click', function () {
        $("#whisper-pain").css({'z-index': -1}).stop().css({'opacity': '0'})
        $("#login-pain").stop().css({'display': 'none'})
        let statistic = $("#statistic-pain");
        statistic.stop().fadeToggle()
        reChart();
    })

    $("#whisper-btn").on('click', function () {
        $("#statistic-pain").stop().css({'display': 'none'})
        $("#login-pain").stop().css({'display': 'none'})
        let whisper = $("#whisper-pain");
        if (whisper.css('opacity') === '0')
            whisper.css({'top': headHight + 200 + 'px', 'z-index': 1020}).stop().animate({
                "top": headHight + 'px',
                'opacity': '1'
            }, 1000)
        else
            whisper.css({'z-index': -1}).stop().animate({'opacity': '0'}, 500)
    })

    $("#app-header #login-bar-btn").on('click', function () {
        $("#whisper-pain").css({'z-index': -1}).stop().css({'opacity': '0'})
        $("#statistic-pain").stop().css({'display': 'none'})
        $("#login-pain").fadeToggle()
    })

    $("#show-left-bar").on('click', function () {
        let aside = $("#app-aside")
        console.log(aside.css("left"));
        if (aside.css("left") === '0px') {
            aside.stop().animate({'left': '-100%'})
            $("#app-content").stop().animate({'margin-left': 0})
        } else {
            aside.stop().animate({'left': 0})
            $("#app-content").stop().animate({'margin-left': aside.width()})
        }

    })


    $("#bg-cover .cover-close").on('click', function () {
        $("#bg-cover").stop().fadeOut();
    })


    $("#show-search-sm,#show-search-btn").on('click', function () {
        $("#bg-cover").stop().fadeIn();
    })


    $(".nav-list>ul>li>a").on('click', function () {
        $(".nav-list>ul>li>a").removeClass('nav-active')
        $(this).addClass('nav-active')
        let navRight = $(".nav-list .nav-right")
        navRight.removeClass('icon-angle-down').addClass('icon-angle-right')
        $(this).next('.nav-right').removeClass('icon-angle-right').addClass('icon-angle-down')
        let target = $(this).data('target')
        let targetEle = $(`#${target}`)
        if (targetEle.css('display') === 'none') {
            $(".nav-list .child-nav-list").css('display', 'none')
            targetEle.fadeIn()
        } else {
            $(".nav-list .child-nav-list").fadeOut()
            navRight.removeClass('icon-angle-down').addClass('icon-angle-right')
            $(this).removeClass('nav-active')
            $('.nav-list .child-nav-list .child-active').removeClass('child-active')
        }

    })


    $(".nav-list .child-nav-list li>a").on('click', function () {
        $(".nav-list .child-nav-list li>a").removeClass('child-active')
        $(this).addClass('child-active')
        $(".nav-list .child-nav-list .nav-right").removeClass('icon-angle-down').addClass('icon-angle-right')
        $(this).next('.nav-right').removeClass('icon-angle-right').addClass('icon-angle-down')
        let target = $(this).data('target')
        let targetEle = $(`#${target}`)
        let other = $(this).parent().parent().children('li').children('.child-nav-list');
        if (targetEle.css('display') === 'none') {
            other.css('display', 'none')
            targetEle.fadeIn()
        } else {
            other.fadeOut()
            $(this).next(".nav-right").removeClass('icon-angle-down').addClass('icon-angle-right')
            $(this).removeClass('child-active')

        }
    })

    $(".right-bar .right-item .catgory-list li").mouseenter(function () {
        $(this).children(".children").stop().fadeIn();
    }).mouseleave(function () {
        $(this).children(".children").stop().fadeOut();
    })

    $("#bg-cover .keywords-list a").on('click', function () {
        let search = $("#search-form");
        let input = $("#search-form input[name=s]");
        input.val($(this).data('key'))
        search.submit()
    })

    $("#my-email").click(function () {
        layer.tips($(this).data('email'), '#my-email')
    })


    $("#register-div #register-form").on('submit', function () {
        if (validRegister().form()) {
            let url = $(this).attr('action');
            let data = $(this).serializeObject();
            let loadIndex = layer.load(1, {
                shade: [0.1, '#fff'] //0.1透明度的白色背景
            });
            $.ajax({
                url: url,
                type: "POST",
                data: data,
                success: res => {
                    if (res.indexOf('>强烈建议更改你的默认密码</a></li>') !== -1) {
                        layer.close(loadIndex)
                        layer.msg('注册成功', {icon: 1}, function () {
                            location.reload()
                        })
                    } else {
                        layer.close(loadIndex)
                        layer.msg('注册失败', {icon: 2})
                    }
                },
                error: res => {
                    layer.close(loadIndex)
                    layer.msg('发生未知错误', {icon: 2})
                }
            })
        }
        return false;
    })

    $('#login-form').on('submit', function () {
        let url = $(this).attr('action');
        let data = $(this).serializeObject();
        let loadIndex = layer.load(1, {
            shade: [0.1, '#fff'] //0.1透明度的白色背景
        });
        $.ajax({
            url: url,
            type: "POST",
            data: data,
            success: res => {
                if (res.indexOf('// 导航菜单 tab 聚焦时展开下拉菜单') !== -1) {
                    layer.close(loadIndex)
                    layer.msg('登录成功', {icon: 1}, function () {
                        location.reload()
                    })
                } else {
                    layer.close(loadIndex)
                    layer.msg('用户名或密码错误', {icon: 2})
                }
            },
            error: res => {
                layer.close(loadIndex)
                layer.msg('发生未知错误', {icon: 2})
            }
        })
        return false;
    })

    $("#to-register").on('click', function () {
        $('#login-div').stop().fadeOut()
        $('#register-div').stop().fadeIn()
    })

    $('#return-login').on('click', function () {
        $('#login-div').stop().fadeIn()
        $('#register-div').stop().fadeOut()
    })

    $('.post-suport').on('click', function () {
        let cid = $(this).data('cid');
        $.ajax({
            url: `?freewind=${new Date().getTime()}`,
            type: 'POST',
            data: {
                suport: true,
                cid: cid
            },
            dataType: 'json',
            success: res => {
                if (res.success) {
                    $(this).parent().removeClass('icon-xin').addClass('icon-theheart-fill')
                    $(this).text('(' + res.count + ')' + '已赞')
                } else {
                    layer.msg('该文章您已点过赞啦', {icon: 2})
                }
            }
        })
    })
})