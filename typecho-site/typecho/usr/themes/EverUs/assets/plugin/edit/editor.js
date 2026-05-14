(function ($) {
    $.fn.extend({
        /* 按键盘实现插入内容 */
        shortcuts: function () {
            this.keydown(function (e) {
                var _this = $(this);
                e.stopPropagation();
                if (e.altKey) {
                    switch (e.keyCode) {
                        case 67:
                            _this.insertContent('[code]' + _this.selectionRange() + '[/code]');
                            break;
                    }
                }
            });
        },
        /* 插入内容 */
        insertContent: function (myValue, t) {
            var $t = $(this)[0];
            if (document.selection) {
                this.focus();
                var sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
                sel.moveStart('character', -l);
                var wee = sel.text.length;
                if (arguments.length == 2) {
                    var l = $t.value.length;
                    sel.moveEnd('character', wee + t);
                    t <= 0 ? sel.moveStart('character', wee - 2 * t - myValue.length) : sel.moveStart('character', wee - t - myValue.length);
                    sel.select();
                }
            } else if ($t.selectionStart || $t.selectionStart == '0') {
                var startPos = $t.selectionStart;
                var endPos = $t.selectionEnd;
                var scrollTop = $t.scrollTop;
                $t.value = $t.value.substring(0, startPos) + myValue + $t.value.substring(endPos, $t.value.length);
                this.focus();
                $t.selectionStart = startPos + myValue.length;
                $t.selectionEnd = startPos + myValue.length;
                $t.scrollTop = scrollTop;
                if (arguments.length == 2) {
                    $t.setSelectionRange(startPos - t, $t.selectionEnd + t);
                    this.focus();
                }
            } else {
                this.value += myValue;
                this.focus();
            }
        },
        /* 选择 */
        selectionRange: function (start, end) {
            var str = '';
            var thisSrc = this[0];
            if (start === undefined) {
                if (/input|textarea/i.test(thisSrc.tagName) && /firefox/i.test(navigator.userAgent)) str = thisSrc.value.substring(thisSrc.selectionStart, thisSrc.selectionEnd);
                else if (document.selection) str = document.selection.createRange().text;
                else str = document.getSelection().toString();
            } else {
                if (!/input|textarea/.test(thisSrc.tagName.toLowerCase())) return false;
                end === undefined && (end = start);
                if (thisSrc.setSelectionRange) {
                    thisSrc.setSelectionRange(start, end);
                    this.focus();
                } else {
                    var range = thisSrc.createTextRange();
                    range.move('character', start);
                    range.moveEnd('character', end - start);
                    range.select();
                }
            }
            if (start === undefined) return str;
            else return this;
        }
    });
})(jQuery);

/* 新按钮 */
$(function() {
    const items = [
        {
            title: '短代码',
            id: 'wmd-short-code',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="none" d="M0 0h24v24H0z"/><path d="M24 12l-5.657 5.657-1.414-1.414L21.172 12l-4.243-4.243 1.414-1.414L24 12zM2.828 12l4.243 4.243-1.414 1.414L0 12l5.657-5.657L7.07 7.757 2.828 12zm6.96 9H7.66l6.552-18h2.128L9.788 21z" fill="rgba(153,153,153,1)"/></svg>',
            type: 'wmd-button',
            text: '\`短代码\`'
        },
        {
            title: '长代码',
            id: 'wmd-long-code',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="none" d="M0 0h24v24H0z"/><path d="M11 12l-7.071 7.071-1.414-1.414L8.172 12 2.515 6.343 3.929 4.93 11 12zm0 7h10v2H11v-2z" fill="rgba(153,153,153,1)"/></svg>',
            type: 'wmd-button',
            text: '\n\`\`\`html\n    代码主体\n\`\`\`\n'
        },
        {
            title: '插入表格',
            id: 'wmd-table-button',
            svg: '<svg class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="5129"><path d="M400.756383 521.126868c-17.171078 0-31.092136 13.922081-31.092136 31.092136l0 30.332842c0 17.171078 13.922081 31.092136 31.092136 31.092136 17.170055 0 31.092136-13.922081 31.092136-31.092136l0-30.332842C431.848519 535.047926 417.926438 521.126868 400.756383 521.126868z" fill="rgba(153,153,153,1)" p-id="5130"></path><path d="M622.698195 521.126868c-17.171078 0-31.092136 13.922081-31.092136 31.092136l0 30.332842c0 17.171078 13.922081 31.092136 31.092136 31.092136 17.170055 0 31.092136-13.922081 31.092136-31.092136l0-30.332842C653.791353 535.047926 639.869273 521.126868 622.698195 521.126868z" fill="rgba(153,153,153,1)" p-id="5131"></path><path d="M588.406181 661.936871c-14.20042-9.351995-33.420157-5.374404-42.964534 8.714476-0.132006 0.197498-13.552667 19.669992-34.021861 19.669992-19.903306 0-32.281217-18.025539-33.121352-19.286252-9.134031-14.362102-28.157293-18.724457-42.635029-9.716292-14.589276 9.063423-19.068288 28.233018-10.004865 42.817178 11.158131 17.965164 41.785685 48.368614 85.762269 48.368614 43.763736 0 74.764797-30.176276 86.186941-48.004317C606.78169 690.169889 602.62195 671.299099 588.406181 661.936871z" fill="rgba(153,153,153,1)" p-id="5132"></path><path d="M366.630145 220.097814c17.171078 0 31.092136-13.922081 31.092136-31.092136l0-10.364045 228.010017 0 0 10.364045c0 17.171078 13.922081 31.092136 31.092136 31.092136 17.170055 0 31.092136-13.922081 31.092136-31.092136L687.916569 95.728248c0-17.171078-13.922081-31.092136-31.092136-31.092136-17.171078 0-31.092136 13.922081-31.092136 31.092136l0 20.72809L397.72228 116.456339 397.72228 95.728248c0-17.171078-13.922081-31.092136-31.092136-31.092136s-31.092136 13.922081-31.092136 31.092136l0 93.276407C335.538009 206.175733 349.46009 220.097814 366.630145 220.097814z" fill="rgba(153,153,153,1)" p-id="5133"></path><path d="M779.675412 100.035344c-17.170055 0-31.092136 13.922081-31.092136 31.092136s13.922081 31.092136 31.092136 31.092136c52.063773 0 94.415346 43.191708 94.415346 96.28288l0 121.04382L149.363819 379.546316 149.363819 258.430864c0-53.090149 42.351574-96.28288 94.415346-96.28288 17.171078 0 31.092136-13.922081 31.092136-31.092136s-13.922081-31.092136-31.092136-31.092136c-86.348624 0-156.599617 71.086012-156.599617 158.467151l0 543.03176c0 87.38114 70.250994 158.472268 156.594501 158.472268l535.901363 0.070608c86.348624 0 156.599617-71.091128 156.599617-158.472268L936.27503 258.502495C936.27503 171.121356 866.024036 100.035344 779.675412 100.035344zM779.680529 897.821229l-535.901363-0.070608c-52.063773 0-94.415346-43.191708-94.415346-96.287997L149.363819 441.730587l724.726939 0 0 359.802646C874.090758 854.629521 831.739185 897.821229 779.680529 897.821229z" fill="rgba(153,153,153,1)" p-id="5134"></path></svg>',
            type: 'origin_btn',
            text: '\n| 表头 | 表头 | 表头 |\n| :--: | :--: | :--: |\n| 表格 | 表格 | 表格 |\n| 表格 | 表格 | 表格 |\n| 表格 | 表格 | 表格 |\n'
        },
        {
            title: 'Emoji表情',
            id: 'wmd-emoji',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="none" d="M0 0h24v24H0z"/><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zm-5-7h2a3 3 0 0 0 6 0h2a5 5 0 0 1-10 0zm1-2a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm8 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z" fill="rgba(153,153,153,1)"/></svg>',
            type: 'origin_btn',
            text: '\nEmoji表情\n'
        },
        {
            title: '音频',
            id: 'wmd-music-button',
            svg: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="none" d="M0 0h24v24H0z"/><path d="M20 3v14a4 4 0 1 1-2-3.465V5H9v12a4 4 0 1 1-2-3.465V3h13zM5 19a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm11 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" fill="rgba(153,153,153,1)"/></svg>',
            type: 'origin_btn'
        },
        {
            title: '视频',
            id: 'wmd-video-button',
            svg: '<svg class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="3364"><path d="M959.12034 585.664743 959.12034 244.581438c0-24.282033-10.600429-43.955094-29.079292-53.971216-20.12434-10.9197-45.3867-8.249895-69.572542 7.493671l-92.620467 61.647035c-14.346753 9.544377-18.226107 28.912493-8.68173 43.249013 9.534144 14.33652 28.907377 18.231223 43.243897 8.686846l92.356454-61.480236c0.690732-0.447185 1.350764-0.848321 1.969864-1.198292l0 338.900593c-0.984421-0.695848-2.000564-1.335415-3.056616-1.913583l-93.575212-51.214429c-15.118326-8.275477-34.06484-2.710738-42.340317 12.387122-8.265244 15.113209-2.720971 34.06484 12.387122 42.340317l92.164073 50.442856c12.549827 8.452509 26.236548 12.762675 39.528272 12.762675 9.402137 0 18.60166-2.158153 27.059286-6.523577 19.443841-10.051937 31.049157-29.989011 31.049157-53.336766C959.953312 590.381162 959.668833 587.975369 959.12034 585.664743z" fill="rgba(153,153,153,1)" p-id="3365"></path><path d="M270.587742 276.915867c-17.23043 0-31.191396 13.966083-31.191396 31.191396l0 30.430056c0 17.225313 13.960966 31.191396 31.191396 31.191396 17.23043 0 31.191396-13.966083 31.191396-31.191396l0-30.430056C301.779138 290.88195 287.818171 276.915867 270.587742 276.915867z" fill="rgba(153,153,153,1)" p-id="3366"></path><path d="M482.848053 308.107263l0 30.430056c0 17.225313 13.960966 31.191396 31.191396 31.191396 17.23043 0 31.191396-13.966083 31.191396-31.191396l0-30.430056c0-17.225313-13.960966-31.191396-31.191396-31.191396C496.809019 276.915867 482.848053 290.88195 482.848053 308.107263z" fill="rgba(153,153,153,1)" p-id="3367"></path><path d="M388.896264 509.036505c43.893696 0 74.994018-30.272467 86.457094-48.157813 9.290597-14.504342 5.066389-33.795711-9.44307-43.086307-14.509459-9.311063-33.790594-5.071506-43.091424 9.437953-0.121773 0.193405-13.362332 19.423375-33.9226 19.423375-19.9821 0-32.410154-18.10331-33.243125-19.34765-9.148357-14.397918-28.247344-18.783809-42.767036-9.746992-14.631232 9.092076-19.129686 28.328185-10.031471 42.959418C314.054719 478.534817 344.779488 509.036505 388.896264 509.036505z" fill="rgba(153,153,153,1)" p-id="3368"></path><path d="M602.577948 65.224514 182.048219 65.224514c-64.683185 0-117.303636 59.702754-117.303636 133.083018l0 445.497377c0 73.379241 52.620451 133.083018 117.303636 133.083018l43.776016 0-69.845765 137.179312c-7.818059 15.352663-1.710968 34.131355 13.641695 41.949414 4.533246 2.309602 9.366322 3.40147 14.128789 3.40147 11.361769 0 22.312168-6.223748 27.820626-17.043165l84.269265-165.488055 192.939267 0 84.264149 165.488055c5.513574 10.818393 16.46909 17.043165 27.820626 17.043165 4.762467 0 9.595542-1.091868 14.133905-3.40147 15.352663-7.818059 21.454638-26.596751 13.646811-41.949414L558.792723 776.886903 602.577948 776.886903c64.678068 0 117.303636-59.702754 117.303636-133.083018L719.881584 557.971008c0-17.225313-13.960966-31.191396-31.191396-31.191396-17.23043 0-31.191396 13.966083-31.191396 31.191396l0 85.832877c0 38.324864-25.15082 70.699202-54.920844 70.699202L182.048219 714.503087c-29.770024 0-54.920844-32.374338-54.920844-70.699202L127.127376 198.307531c0-38.324864 25.15082-70.699202 54.920844-70.699202L602.577948 127.60833c29.770024 0 54.920844 32.374338 54.920844 70.699202l0 90.168626c0 17.225313 13.960966 31.191396 31.191396 31.191396 17.23043 0 31.191396-13.966083 31.191396-31.191396l0-90.168626C719.882608 124.927267 667.256016 65.224514 602.577948 65.224514z" fill="rgba(153,153,153,1)" p-id="3369"></path></svg>',
            type: 'origin_btn',
        }
    ];
    items.forEach(_ => {
        let item = $(`<li class="${_.type}" id="${_.id}" title="${_.title}">${_.svg}</li>`);
        item.on('click', function () {
            if(_.type == 'wmd-button'){
                $('#text').insertContent(_.text);
            }
        });
        $('#wmd-button-row').append(item);
    });
});

/* =====  生成 Emoji 面板（放在按钮列最后） ===== */
$(function () {
    const emojiAll = "😀 😁 😂 🤣 😃 😄 😅 😆 😉 😊 😋 😎 😍 😘 😗 😙 😚 😇 🙂 🤗 🤩 🤔 🤨 😐 😑 😶 🙄 😏 😣 😥 😮 🤐 😯 😪 😫 😴 😌 😛 😜 😝 🤤 😒 😓 😔 😕 🙃 🤑 😲 ☹️ 🙁 😖 😞 😟 😤 😢 😭 😦 😧 😨 😩 🤯 😬 😰 😱 😳 🤪 😵 😡 😠 🤬 😷 🤒 🤕 🤢 🤮 🤧 😇 🤠 🤡 🤥 🤫 🤭 🧐 🤓 😈 👿 👹 👺 💀 👻 👽 🤖 💩 😺 😸 😹 😻 😼 😽 🙀 😿 😾";
    const emojiArr = emojiAll.split(" ");
    let html = '<li id="emojistart" style="display:none;"></li>'; // 占位符
    html += '<div class="emojiblock" style="display:none;">';
    emojiArr.forEach(e => html += `<span class="editor_emoji">${e}</span>`);
    html += '</div>';
    $('#wmd-button-row').append(html);          // 把面板塞进按钮行
});

/* ===== 按钮点击事件 ===== */
$(document).on('click', '#wmd-emoji', function () {
    $('.emojiblock').slideToggle(200);        // 展开/收起
});

/* ===== 点表情插入文本 ===== */
$(document).on('click', '.editor_emoji', function () {
    const emoji = $(this).text();
    $('#text').insertContent(emoji);          // 用你的公共方法
    $('#wmd-editarea textarea').focus();      // 焦点回到编辑区
    $('.emojiblock').slideUp(200);            // 自动收起
});

window.onload = function () {
    /* 样式栏 */
    $(document).ready(function(){
        if ($("#custom-field").length >0){
            /* 插入表格 */
            $(document).on('click', '#wmd-table-button', function() {
                $('body').append(
                    '<div id="postPanel">'+
                    '<div class="wmd-prompt-background" style="position: fixed; top: 0px; z-index: 1000; opacity: 0.5; height: 100%; left: 0px; width: 100%;"></div>'+
                    '<div class="wmd-prompt-dialog">'+
                    '<div>'+
                    '<h3><label class="typecho-label">插入表格</label></h3>'+
                        '<label>表格行</label><input type="number" class="layui-input" value="3" autocomplete="off" name="A">'+
                        '<label>表格列</label><input type="number" class="layui-input" value="3" autocomplete="off" name="B">'+
                    '</div>'+
                    '<form>'+
                    '<button type="button" class="btn btn-s layui-btn" id="wmd-table-button_ok">确定</button>'+
                    '<button type="button" class="btn btn-s layui-btn layui-btn-danger" id="post_cancel">取消</button>'+
                    '</form>'+
                    '</div>'+
                    '</div>');
            });
            $(document).on('click', '#wmd-table-button_ok',function () {
				let row = $(".wmd-prompt-dialog input[name='A']").val();
				let column = $(".wmd-prompt-dialog input[name='B']").val();
				if (isNaN(row)) row = 3;
				if (isNaN(column)) column = 3;
				let rowStr = '';
				let rangeStr = '';
				let columnlStr = '';
				for (let i = 0; i < column; i++) {
					rowStr += '| 表头 ';
					rangeStr += '| :--: ';
				}
				for (let i = 0; i < row; i++) {
					for (let j = 0; j < column; j++) columnlStr += '| 表格 ';
					columnlStr += '|\n';
				}
				const textContent = `${rowStr}|\n${rangeStr}|\n${columnlStr}\n`;
                $('#text').insertContent(textContent);
                $('#postPanel').remove();
                $('#wmd-editarea textarea').focus();
            });
            /* 取消 */
            $(document).on('click','#post_cancel',function() {
                $('#postPanel').remove();
                $('#wmd-editarea textarea').focus();
            });
            /* 插入音乐 */
            $(document).on('click', '#wmd-music-button', function () {
                $('body').append(
                    '<div id="musicPanel">' +
                    '<div class="wmd-prompt-background" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1000; opacity: 0.5;"></div>' +
                    '<div class="wmd-prompt-dialog">' +
                    '<div>' +
                    '<h3><label class="typecho-label">插入音乐</label></h3>' +
                    '<label>平台</label>' +
                    '<select id="music-platform" class="layui-input layui-unselect">' +
                    '<option value="netease">网易云音乐</option>' +
                    '<option value="tencent">QQ音乐</option>' +
                    '<option value="kugou">酷狗音乐</option>' +
                    '</select>' +
                    '<label>歌曲ID</label>' +
                    '<input type="number" id="music-id" class="layui-input" autocomplete="off">' +
                    '</div>' +
                    '<form>' +
                    '<button type="button" class="btn btn-s layui-btn" id="wmd-music-ok">确定</button>' +
                    '<button type="button" class="btn btn-s layui-btn layui-btn-danger" id="music-cancel">取消</button>' +
                    '</form>' +
                    '</div>' +
                    '</div>'
                );
            });
            // 确定
            $(document).on('click', '#wmd-music-ok', function () {
                const platform = $('#music-platform').val();
                const id = $('#music-id').val().trim();
                if (!id) return;
                const textContent = `{meting-js server="${platform}" type="song" id="${id}"}{/meting-js}\n`;
                $('#text').insertContent(textContent);
                $('#musicPanel').remove();
                $('#wmd-editarea textarea').focus();
            });
            // 取消
            $(document).on('click', '#music-cancel', function () {
                $('#musicPanel').remove();
                $('#wmd-editarea textarea').focus();
            });
            // 插入视频
            /* ==================  视频插入按钮  ================== */
            $(document).on('click', '#wmd-video-button', function () {
                $('body').append(
                    '<div id="videoPanel">' +
                      '<div class="wmd-prompt-background" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1000; opacity: 0.5;"></div>' +
                      '<div class="wmd-prompt-dialog">' +
                        '<div>' +
                          '<h3><label class="typecho-label">插入视频</label></h3>' +
                          '<label>来源</label>' +
                          '<select id="video-platform" class="layui-input layui-unselect">' +
                            '<option value="bilibili">bilibili</option>' +
                            '<option value="local">本地视频</option>' +
                          '</select>' +
                          '<label id="video-label">AV 号</label>' +
                          '<input type="text" id="video-id" class="layui-input" autocomplete="off" placeholder="例如：BV1GJ411x7h7">' +
                        '</div>' +
                        '<form>' +
                          '<button type="button" class="btn btn-s layui-btn" id="wmd-video-ok">确定</button>' +
                          '<button type="button" class="btn btn-s layui-btn layui-btn-danger" id="video-cancel">取消</button>' +
                        '</form>' +
                      '</div>' +
                    '</div>'
                );
            
                // 切换 placeholder 与 label
                $('#video-platform').on('change', function () {
                    const type = $(this).val();
                    if (type === 'bilibili') {
                        $('#video-label').text('AV 号');
                        $('#video-id').attr('placeholder', '例如：BV1GJ411x7h7');
                    } else {
                        $('#video-label').text('视频地址');
                        $('#video-id').attr('placeholder', '例如：/usr/uploads/my.mp4');
                    }
                });
            });

            /* ==================  确定  ================== */
            $(document).on('click', '#wmd-video-ok', function () {
                const platform = $('#video-platform').val();
                const idOrUrl  = $('#video-id').val().trim();
                if (!idOrUrl) return;
            
                let textContent = '';
                if (platform === 'bilibili') {
                    // 同时支持 BV 号 和 aid 数字
                    const bv   = idOrUrl.startsWith('BV') ? idOrUrl : '';
                    const avid = /^\d+$/.test(idOrUrl) ? idOrUrl : '';
            
                    if (!bv && !avid) { alert('请输入正确的 BV 号 或 avid 数字'); return; }
            
                    textContent = `{bvideo bv="${bv}" avid="${avid}"}{/bvideo}\n`;
                } else {
                    // 本地视频
                    textContent = `{video data-src="${idOrUrl}"}{/video}\n`;
                }
            
                $('#text').insertContent(textContent);
                $('#videoPanel').remove();
                $('#wmd-editarea textarea').focus();
            });
            
            /* ==================  取消  ================== */
            $(document).on('click', '#video-cancel', function () {
                $('#videoPanel').remove();
                $('#wmd-editarea textarea').focus();
            });
        }
    });
};
