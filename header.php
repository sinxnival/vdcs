<!DOCTYPE HTML>
<html style="font-size: 13px;">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Expires" content="Mon, 06 Jan 1990 00:00:01 GMT" /> 
<meta http-equiv="Expires" content="-1" /> 
<meta http-equiv="Pragma" content="no-cache"/>
<meta http-equiv="Cache-Control" content="no-cache" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="google" content="notranslate" />
<link rel="manifest" href="manifest.json" />
<script type="text/javascript" src="js/grp.js?random=<?php echo uniqid(); ?>"></script>
<link rel="stylesheet" href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" />
<link rel="stylesheet" href="fontawesome-6.0.0-web/css/all.css" />
<link rel="stylesheet" href="jquery/jquery-ui-1.13.0/jquery-ui.min.css" />
<script type="text/javascript" src="jquery/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="jquery/jquery-ui-1.13.0/jquery-ui.min.js"></script>
<script type="text/javascript" src="jquery/jquery-ui-1.13.0/i18n/datepicker-ko.js"></script>
<script type="text/javascript" src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxcore.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxwindow.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxbuttons.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxscrollbar.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxpanel.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxtabs.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxcheckbox.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxdata.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxlistbox.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxdropdownlist.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxmenu.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxgrid.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxgrid.pager.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxgrid.sort.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxgrid.storage.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxgrid.columnsresize.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxgrid.columnsreorder.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxgrid.selection.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxtree.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxexpander.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxsplitter.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxcalendar.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxdatetimeinput.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/globalization/globalize.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxgrid.filter.js"></script>
<script type="text/javascript" src="jqwidgets-ver14.0.0-src/jqxdropdownbutton.js"></script>
<link rel="stylesheet" href="jqwidgets-ver14.0.0-src/styles/jqx.base.css" type="text/css" />
<link rel="stylesheet" href="css/style.css?random=<?php echo uniqid(); ?>" />
<script src="https://cdn.jsdelivr.net/npm/vue@2.7.13/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<link rel="stylesheet" type="text/css" href="DevExtreme/css/dx.light.css" />
<script src="DevExtreme/js/dx.all.js"></script>
<style>
    .compName {
        background-color: #DAEEF3;
        vertical-align: middle;
    }

    @media (min-width: 1300px) {
        #modalOrganization .modal-content {
            width: 1250px !important;
        }
    }
</style>
</head>
<body>
<?php
if($isLogin) {
require_once "admin/right_setting_view.php";
?>
<script>
$(document).ready(function() {
    // uno, userId
    $("#uno").val('<?php echo $_SESSION["user"]["uno"]?>');
    $("#userId").val('<?php echo $_SESSION["user"]["user_id"]?>');
        
    // 권한 설정 모달
    if($("#uno").val() == "10065") {
        $("#btnRightSetting").show();
    } else {
        $("#btnRightSetting").hide();
    }

    // 잡 여부에 따라 화면 변경
    var jno = sessionStorage.getItem("jno");
    
    // 도메인 별 권한
    var menuRight = '<?php echo $menuRight?>';
    var teamId = '<?php echo @$_SESSION["user"]["team_id"] ?>';
    $("#teamId").val(teamId);

    if(jno) {
        $("#noPjtHeader").hide();

        $.ajax({
            type: "POST",
            url: "/equipment/equip_menu_list.php",
            data: {jno: jno},
            dataType: "json",
            success: function(result) {
                var resultType = result["ResultType"];

                if(resultType == "Success") {
                    var equipMenuList = result["Value"];

                    var html = '';
                    html += '<ul>';
                    $(equipMenuList).each(function(i, menu) {
                        html += '<li>';
                        html += '<a id="equipment_'+ menu["CNO"] +'" onclick="activeSubMenu(this)">'+ menu["EQLISTTITLE"] +'</span></a>';
                        html += '</li>';
                    });
                    html += '</ul>';

                    $("#equip").append(html);
                }
            },
            complete: function() {
                // 화면 보이기
                var subMenu = '';

                if(menuRight == "vp" || menuRight == "all") {
                    if (sessionStorage.getItem("subMenu")) {
                        subMenu = sessionStorage.getItem("subMenu");
                    } else {
                        subMenu = "vpLatest";
                    }
                    activeSubMenu($("#" + subMenu));
                } else if(menuRight == "cm") {
                    var cmRight = sessionStorage.getItem("cmRight");
                    if(sessionStorage.getItem("jno")) {
                        var menuList = [];
                        $($("#welding").find("a")).each(function() {
                            menuList.push($(this).attr("id"));
                        });
                        var subMenu = sessionStorage.getItem("subMenu");
                        if (cmRight == "true" && menuList.includes(subMenu)) {
                            if(subMenu != "noRight" && $("#welding").find("a").length != 0) {
                                $("#welding").find("a").eq()
                                $("#smMenu").css("visibility", "visible");
                                activeSubMenu($("#" + subMenu));
                            } else {
                                $("#vdcsContent").load("no_right.php");
                                $("#smMenu").css("visibility", "hidden");
                                sessionStorage.setItem("subMenu", "noRight");
                            }
                        }
                        else if($("#welding").find("a").length > 0) {
                            var elementId = $("#welding").find("a").eq(0).attr("id");
                            subMenu = elementId
                            $("#smMenu").css("visibility", "visible");
                            activeSubMenu($("#" + subMenu));
                        }
                        else {
                            $("#vdcsContent").load("no_right.php");
                            $("#smMenu").css("visibility", "hidden");
                            sessionStorage.setItem("subMenu", "noRight");
                            subMenu = "noRight";
                        }
                    } else {
                        $("#smMenu").css("visibility", "hidden");
                    }
                }
            },
            error: function (request, status, error) {
                alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
    } else {
        $("#noPjtHeader").show();
        if(menuRight == "vp" || menuRight == "all") {
            $("#vpLatest").trigger("click");
        } else {
            $("#welding a").first().trigger('click');
        }
    }

    // 조직도 표시
    var organiUser = importOrganization();

    // db 권한 사용자 리스트
    var dbRightList = getDbRightList();
    var userList = Object.keys(dbRightList);

    if(menuRight == "all") {
        $("#vdcs").show();
        $("#equip").show();
        // $("#welding").show();
        $("#btnStaffOnly").show();
        $("#jobFilter").val($("input[name='jobCondition']:checked").val());
        sessionStorage.setItem("cmRight", false);
    } else if(menuRight == "vp") {
        $("#vdcs").show();
        $("#welding").remove();
        document.title = "VDCS Latest";
        $("#headerTitle").text("VDCS - Latest");
        sessionStorage.setItem("cmRight", false);
    } else if(menuRight == "cm") {
        $("#vdcs").remove();
        document.title = "공사관리시스템";
        $("#headerTitle").text("공사관리시스템");
        $("#btnStaffOnly").hide();

        // 사장님, 부사장님, 기술연구소, 관리자계정
        if(teamId == 11 || teamId == 90 || $("#uno").val() == 95 || $("#uno").val() == 10065) {
            $("#welding").show();
            $("#welding").find("ul").show();
            sessionStorage.setItem("cmRight", true);
        }
        // db change right user
        else if (userList.includes($("#uno").val()) && sessionStorage.getItem("jno")) {
            var uno = $("#uno").val();
            var codeText = dbRightList[uno]["codeText"];
            $($("#welding").find("ul").find("a")).each(function() {
                var elementId = $(this).attr("id");
                if(codeText.includes(elementId)) {
                    $(this).closest("li").show();
                } else {
                    $(this).closest("li").remove();
                }
            })

            $("#welding").show();
            sessionStorage.setItem("cmRight", true);

            organiUser = organiUser.filter((user) => {
                return user != uno;
            });
        }
        // 조직도 직원
        else if(organiUser.includes($("#uno").val()) && sessionStorage.getItem("jno")) {
            $("#welding").show();
            // welding day, welding month만 공개
            $("#welding").find("li").eq(0).show();
            $("#welding").find("li").eq(1).show();
            $("#welding").find("li").eq(2).remove();
            $("#welding").find("li").eq(2).remove();
            $("#welding").find("li").eq(2).remove();
            sessionStorage.setItem("cmRight", true);
        } 
        // 나머지 직원
        else {
            if(sessionStorage.getItem("jno")) {
                $("#welding").remove();
                $("#vdcsContent").load("no_right.php");
                sessionStorage.setItem("cmRight", false);
            }
        }
    }

    //JOB 표시
    if(sessionStorage.getItem('jobName')) {
        $("#pjtJobName").val(sessionStorage.getItem('jobName'));
        // importOrganization();
    }

    //JOB 선택 모달
    basicDemo.init();

    //jobCondition 초기값
    var IsInterStaff = '<?php echo $_SESSION["user"]["is_attend"] ?>';
    sessionStorage.setItem("isStaff", IsInterStaff);

    // 하이테크 직원 유무에 따른 잡선택
    if(IsInterStaff == "Y") {
        $("#jobFilter").val($("input[name='jobCondition']:checked").val());
        $("#btnStaffOnly").show();
    } else if(IsInterStaff == "N") {
        $("#jobFilter").val("STAFF");
        $("#selJobFilter").hide();
        $("#btnStaffOnly").show();
    } 
    // LG
    else if(IsInterStaff == "LG") {
        $("#btnStaffOnly").hide();
        $("#btnStaffOnly").closest(".selJob").removeClass("input-group");
    }

    // CM
    if(menuRight == "cm") {
        $("#vdcsOnly").hide();
        // 관리자는 ALL
        if($("#uno").val() == 10065) {
            $("#jobFilter").val("ALL");
            $("input[name='jobCondition'][value='ALL']").prop("checked", true);
        } else {
            $("#jobFilter").val("STAFF");
            $("input[name='jobCondition'][value='STAFF']").prop("checked", true);
        }
        // $("#btnStaffOnly").show();
        // $("#selJobFilter").show();
    }

    // 비밀번호 변경 버튼
    if (IsInterStaff == "N" || teamId == 90) {
        $("#btnChangePassword").show();
    } else {
        $("#btnChangePassword").hide();
    }

    //JOB LIST 불러오기
    importJobList();

    // LG 직원
    $("#jobListGrid").on("bindingcomplete", function (event) {
        if(IsInterStaff == "LG" && !sessionStorage.getItem("jno")) {
            var lgProject = $('#jobListGrid').jqxGrid('getrowdatabyid', 17624);
            var jobNm = lgProject["jobName"];
            var jno = lgProject["jno"];
            var jobNo = lgProject["jobNo"];
            onBtnJobSelectClick(jobNm, jno, jobNo);
        }
    });
    
    // 모바일 header 조정
    if($(window).width() <= 576) {
        $(".selJob").hide();
    } else {
        $(".selJob").show();
    }

    //jobCondition change
    $("input[name='jobCondition']").on('change', function() {
        $("#jobFilter").val($("input[name='jobCondition']:checked").val());
        importJobList();
    });

    //행 클릭
    $("#jobListGrid").on('rowclick', function (event) {
        var jobNm = event.args.row.bounddata["jobName"];
        var jno = event.args.row.bounddata["jno"];
        var jobNo = event.args.row.bounddata["jobNo"];
        onBtnJobSelectClick(jobNm, jno, jobNo);
    });

    // footer 데이터 추가
    // var jobName = sessionStorage.getItem("jobName");
    // var jobNo = sessionStorage.getItem("jobNo");

    // if(jobNo) {
    //     var html = "[" + jobNo + "]" + jobName + " | Latest Date : 2020-07-28 20:24:40 | History Count : 6";
    //     $("#footer").text(html);
    // }

    if(!sessionStorage.getItem("jno")) {
        $(".organization").hide();
    } else {
        $(".organization").show();
    }

    // thead 틀 고정
    var thOrganization = $('#tblOrganization').find('thead th');
    $('#tblOrganization').closest('div.tableFixHead-modal').on('scroll', function() {
        thOrganization.css('transform', 'translateY('+ this.scrollTop +'px)');
    });

    //비밀번호 변경 - 저장 버튼
    $("#btnSavePwd").on("click", onBtnSavePwdClick);
});

//로그아웃 버튼
function onLogoutClick() {
    $("#mainForm").attr({
        action:"/account/logout.php", 
        method:"post", 
        target:"_self"
    }).submit();
}

//JOB 선택 모달
var basicDemo = (function () {
    //Adding event listeners
    function _addEventListeners() {
        $('#btnPjtSelect').click(function () {
            $('#jobSelWindow').jqxWindow('open');
        });
        $('#pjtJobName').click(function () {
            var IsInterStaff = '<?php echo $_SESSION["user"]["is_attend"] ?>';
            var IsInterStaff = '<?php echo $_SESSION["user"]["is_attend"] ?>';
            if(IsInterStaff != "LG") {
                $('#jobSelWindow').jqxWindow('open');
            }
        });
        $('#mobileSelJob').click(function () {
            $('#jobSelWindow').jqxWindow('open');
        });
    };
    //Creating the demo window
    function _createWindow() {
        $('#jobSelWindow').jqxWindow({ autoOpen: false }); 
        var jqxWidget = $('#jqxWidget');

        // 화면 센터
        var width = 1500;
        var totalWidth = screen.width;
        var centerWidth = 0
        if(totalWidth >= width) {
            var centerWidth = (totalWidth - width) / 2
        }

        var height = 700;
        var totalHeight = screen.height;
        var centerHeight = 0
        if(totalHeight >= height) {
            var centerHeight = (totalHeight - height) / 2
        }

        var offset = jqxWidget.offset();
        $('#jobSelWindow').jqxWindow({
            position: { x: centerWidth, y: centerHeight - 60} ,
            showCollapseButton: true,
            maxHeight: height, 
            maxWidth: width, 
            minHeight: height, 
            minWidth: width, 
            height: height, 
            width: width,
            isModal: true,
            cancelButton: $('#btnCancelJobSel'),
            initContent: function () {
                $('#jobSelWindow').jqxWindow('focus');
            }
        });
    };
    return {
        config: {
            dragArea: null
        },
        init: function () {
            //Attaching event listeners
            _addEventListeners();
            //Adding jqxWindow
            _createWindow();
        }
    };
} ());

//서브메뉴 펼치기
function showSubMenu() {
    $('#sidebar, #vdcsContent, #divFooter').toggleClass('active');
}

// 메인화면 보이기
function showContent(subMenu) {
    $("#vdcsContent").empty();

    $.ajax({
        type: "POST",
        url: "get_content_url.php",
        data: {subMenu: subMenu},
        dataType: "json",
        success: function(result) {
            $("#vdcsContent").load(result["pageUrl"]);

            $("#bigMenu").text($(".tree .active").closest("ul").siblings("span").text());
            $("#smMenu").text($(".tree .active").text());
        },
        error: function (request, status, error) {
            alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });
}

//Job List 가져오기
function importJobList() {
    //JOB LIST
    var url = "common/job_list_data.php";
    // prepare the data
    var source =
    {
        datatype: "json",
        datafields: [
            { name: 'jno', type: 'int' },
            { name: 'jobNo', type: 'string' },
            { name: 'compName', type: 'string' },
            { name: 'orderCompName', type: 'string' },
            { name: 'jobName', type: 'string' },
            { name: 'userName', type: 'string' },
            { name: 'jobSd', type: 'string' },
            { name: 'jobEd', type: 'string' },
            { name: 'jobState', type: 'string' },
            { name: 'locName', type: 'string' },
            { name: 'jobCode', type: 'string' },
            { name: 'jobType', type: 'string' }
        ],
        id: 'jno',
        url: url,
        data : {
            jobCondition: $("#jobFilter").val()
        },
        pager: function (pagenum, pagesize, oldpagenum) {
            // callback called when a page or page size is changed.
        }
    };
    var dataAdapter = new $.jqx.dataAdapter(source);
    $("#jobListGrid").jqxGrid(
    {
        width: "100%",
        source: dataAdapter,
        // selectionmode: 'multiplerowsextended',
        sortable: true,
        pageable: true,
        autorowheight: true,
        autoheight: true,
        altrows: true,
        autoloadstate: false,
        autosavestate: false,
        columnsresize: true,
        columnsreorder: true,
        showfilterrow: true,
        filterable: true,
        pagermode: 'simple',
        pagerbuttonscount: 10,
        columns: [
            { text: 'JNO', datafield: 'jno', width: 60, cellsalign: 'center', align: 'center' },
            { text: 'JOB No.', datafield: 'jobNo', width: 160, cellsalign: 'center', align: 'center' },
            { text: 'End-User', datafield: 'compName', width: 150, cellsalign: 'center', align: 'center' },
            { text: 'Client', datafield: 'orderCompName', width: 150, cellsalign: 'center', align: 'center' },
            { text: 'PROJECT 명', datafield: 'jobName', align: 'center' },
            { text: 'PM', datafield: 'userName', width: 75, cellsalign: 'center', align: 'center' },
            { text: '시작일', datafield: 'jobSd', width: 100, cellsalign: 'center', align: 'center' },
            { text: '종료일', datafield: 'jobEd', width: 100, cellsalign: 'center', align: 'center' },
            { text: '진행현황', datafield: 'jobState', width: 75, cellsalign: 'center', align: 'center' },
            // { text: '사업소', datafield: 'locName', width: 100 },
            { text: '업무 코드', datafield: 'jobCode', width: 75, cellsalign: 'center', align: 'center' },
            // { text: 'JOB 유형', datafield: 'jobType', width: 100 },
            { text: '선택', datafield: '선택', columntype: 'button', width: 60, filterable: false, cellsalign: 'center', align: 'center' 
                , cellsrenderer: function () {
                    return "선택";
                }
                , buttonclick: function (row) {
                    var rowData = $('#jobListGrid').jqxGrid('getrowdata', row);
                    var jobNm = rowData["jobName"];
                    var jno = rowData["jno"];
                    var jobNo = rowData["jobNo"]
                    onBtnJobSelectClick(jobNm, jno, jobNo);
                }
            }
        ]
    });
}

//JOB 선택 버튼 클릭
function onBtnJobSelectClick(jobNm, jno, jobNo) {
    //세션에 저장
    sessionStorage.removeItem("jno");
    sessionStorage.setItem("jno", jno);
    sessionStorage.removeItem("jobName");
    sessionStorage.setItem("jobName", jobNm);
    sessionStorage.removeItem("jobNo");
    sessionStorage.setItem("jobNo", jobNo);
    sessionStorage.removeItem("subMenu");
    sessionStorage.removeItem("cmRight");

    $("#pjtJobName").val(jobNm);

    $('#jobSelWindow').jqxWindow('close');

    location.reload();
}

// 조직도 모달
function organizationOpen() {
    $("#modalOrganization").modal("show");
}

// 조직도 가져오기
function importOrganization() {
    var jno = sessionStorage.getItem("jno");

    var organiUserList = [];

    if(jno) {
        $.ajax({
            type: "GET",
            url: "/api/common/job/?api_key=d6c814548eeb6e41722806a0b057da30&api_pass=BQRUQAMXBVY=&model=STAFF_INFO&jno=" + jno,
            dataType: "json",
            async: false,
            success: function(result) {
                if(result["Message"] == "Success") {
                    var html = '';
                    var onceExternal = false;
                    $(result["Value"]).each(function(i, info) {
                        if(info["comp_type_str"] == "External" && onceExternal == false) {
                            html += '<tr class="compName">';
                            html += '<td colspan="10">';
                            html += info["order_comp_name"];
                            html += '</td>';
                            html += '</tr>';
    
                            onceExternal = true;
                        }
                    });
    
                    var externalNo = 1
                    $(result["Value"]).each(function(i, info) {
                        if(info["comp_type_str"] == "External") {
                            html += '<tr>';
                            html += '<td class="text-center">';
                            html += externalNo
                            html += '</td>';
                            html += '<td class="rowspanFunc" style="padding-left:0.75rem !important">';
                            html += info["func_title"];
                            html += '</td>';
                            html += '<td class="text-center">';
                            html += info["charge_cd"];
                            html += '</td>';
                            html += '<td class="text-center">';
                            html += info["charge_detail"];
                            html += '</td>';
                            html += '<td class="text-center">';
                            html += info["member_name"];
                            html += '</td>';
                            html += '<td class="text-center">';
                            html += info["grade_name"];
                            html += '</td>';
                            html += '<td class="text-center">';
                            html += info["comp_name"];
                            html += '</td>';
                            html += '<td class="text-center">';
                            html += info["cell"];
                            html += '</td>';
                            html += '<td class="text-center">';
                            html += info["tel"];
                            html += '</td>';
                            html += '<td style="padding-left:0.75rem !important">';
                            html += info["email"];
                            html += '</td>';
                            html += '</tr>';
    
                            externalNo++;
                        }
                    });
    
                    var onceInternal = false;
                    $(result["Value"]).each(function(i, info) {
                        if(info["comp_type_str"] == "Internal" && onceInternal == false) {
                            html += '<tr class="compName">';
                            html += '<td colspan="10">';
                            html += info["order_comp_name"];
                            html += '<span style="float:right;border: 1px solid #999999; padding: 1px 5px;background-color:white">'
                            html += '<span class="mr-2" style="font-weight: bold;">내부직원</span>';
                            html += '<span class="resignation mr-2">퇴사직원</span>';
                            html += '<span class="mr-2">외부직원</span>';
                            html += '<span style="background-color: #eeeedd;">협력업체직원</span>';
                            html += '</span>'
                            html += '</td>';
                            html += '</tr>';
    
                            onceInternal = true;
                        }
                    });
    
                    var InternalNo = 1
                    var IsInterStaff = '<?php echo $_SESSION["user"]["is_attend"] ?>';
                    $(result["Value"]).each(function(i, info) {
                        // 내부직원 외부직원 협력업체 구분
                        var staffClass = "";
                        if (!info["co_id"]) {
                            staffClass = "";
                        }
                        else if(info["co_id"] == 1) {
                            staffClass = "internalStaff";
                        } else if(info["co_id"] != 1) {
                            staffClass = "subconStaff";
                        }
                        // 퇴사여부
                        var resignation = "";
                        if(info["is_state"] == "N") {
                            resignation = "resignation";
                        }
    
                        if(info["comp_type_str"] == "Internal") {
                            html += '<tr>';
                            html += '<td class="text-center">';
                            html += InternalNo
                            html += '</td>';
                            html += '<td class="rowspanFunc" style="padding-left:0.75rem !important">';
                            html += info["func_title"];
                            html += '</td>';
                            html += '<td class="text-center '+ resignation +'">';
                            html += info["charge_cd"];
                            html += '</td>';
                            html += '<td class="text-center '+ resignation +'">';
                            html += info["charge_detail"];
                            html += '</td>';
                            html += '<td class="text-center '+ staffClass +' '+ resignation +'">';
                            html += info["member_name"];
                            html += '</td>';
                            html += '<td class="text-center '+ resignation +'">';
                            if(IsInterStaff == "LG") {
                                html += info["grade_name"].replace("수습사원", "사원");
                            } else {
                                html += info["grade_name"];
                            }
                            html += '</td>';
                            html += '<td class="text-center '+ resignation +'">';
                            html += info["dept_name"];
                            html += '</td>';
                            html += '<td class="text-center '+ resignation +'">';
                            html += info["cell"];
                            html += '</td>';
                            html += '<td class="text-center '+ resignation +'">';
                            html += info["tel"];
                            html += '</td>';
                            html += '<td style="padding-left:0.75rem !important">';
                            html += info["email"];
                            html += '</td>';
                            html += '</tr>';
    
                            // 내부직원 리스트
                            organiUserList.push(info["uno"]);
    
                            InternalNo++;
                        }
                    });
    
                    $("#tblOrganization tbody").empty().append(html);
    
                    // 같은 공종 행 병합
                    $(".rowspanFunc").each(function() {
                        var rows = $(".rowspanFunc:contains('" + $(this).text() + "')");
                        if(rows.length > 1) {
                            rows.eq(0).attr("rowspan", rows.length);
                            rows.not(":eq(0)").remove();
                        }
    
                        // 대문자 변환
                        $(this).text($(this).text().toUpperCase());
                    });
                }
            },
            error: function (request, status, error) {
                alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
    }
    return organiUserList;
}

// subMenu 열고 닫기
function collapseTree(obj) {
    var icon = $(obj).find("i");
    
    if(icon.hasClass("fa-minus-circle")) {
        icon.removeClass("fa-minus-circle");
        icon.addClass("fa-plus-circle");
        $(obj).siblings("ul").hide();
    } else {
        icon.removeClass("fa-plus-circle");
        icon.addClass("fa-minus-circle");
        $(obj).siblings("ul").show();
    }
}

// subMenu Click
function activeSubMenu(obj) {
    if(obj != "#noRight") {
        $(".branch").find("a").removeClass("active");
    
        $(obj).addClass("active");

        var subMenu = $(obj).attr("id");
        sessionStorage.setItem("subMenu", subMenu);
        if(subMenu.startsWith("equipment")) {
            const [prefix, index] = subMenu.split("_");

            sessionStorage.setItem("equipMenu", $(obj).text());
            sessionStorage.setItem("equipIndex", index);
        }
    
        showContent($(obj).attr("id"));
    } else {
        $("#smMenu").css("visibility", "hidden");
    }
}

//비밀번호 변경 - 저장 버튼
function onBtnSavePwdClick() {

//작업모드
$("#mode").val("SAVE_PASSWORD");
    var proceed;
    proceed = true;

    if(validatePwdInputs()) {

        //글자 수 확인
        $(".lengthCheck").each(function(){
            var pwdLength = $(this).val();

            if(pwdLength.length < 4 || pwdLength.length > 16) {
                proceed = false;
                $(this).addClass("is-invalid");
                $(this).closest(".form-group").find(".invalid-feedback").html("4 ~ 16자 의 영문 대소문자, 숫자, 특수문자를 입력하세요.");
                $(this).closest(".form-group").find(".invalid-feedback").show();
            }
        });

        if(proceed) {
            //비밀번호 확인
            if(!($("#newPwd").val() == $("#newPwdCheck").val())) {
                proceed = false;
                $("#newPwdCheck").addClass("is-invalid");
                $("#newPwdCheck").closest(".form-group").find(".invalid-feedback").html("비밀번호가 일치하지 않습니다.");
                $("#newPwdCheck").closest(".form-group").find(".invalid-feedback").show();
            }
        }

        if(proceed) {
            $.ajax({
                type: "POST",
                url: "/api/common/member/change_password/?api_key=d6c814548eeb6e41722806a0b057da30&api_pass=BQRUQAMXBVY=",
                data: $("#mainForm").serialize(),
                dataType: "json",
                success: function(result) {
                    //변경되었을 경우
                    if(result["ResultType"] == "Success") {
                        $("#resultPwdMsg").addClass("alert-primary");
                        $("#resultPwdMsg").removeClass("alert-danger");
                        $("#resultPwdMsg").empty().html(result["Message"]).fadeIn();
                        $("#resultPwdMsg").delay( 5000 ).fadeOut();

                        $("#modalChangePassword").find("input").val('');
                    } 
                    //변경이 안될경우
                    else {
                        $("#resultPwdMsg").addClass("alert-danger");
                        $("#resultPwdMsg").removeClass("alert-primary");
                        $("#resultPwdMsg").empty().html(result["Message"]).fadeIn();
                        $("#resultPwdMsg").delay( 5000 ).fadeOut();
                    }
                }
            });
        }
    }
}

//유효성 검사 (비밀번호 변경)
function validatePwdInputs() {
    var valid = true;

    // 현재 비밀번호
    valid = valid & validateElement("existPwd");

    // 새 비밀번호
    valid = valid & validateElement("newPwd");

    // 비밀번호 확인
    valid = valid & validateElement("newPwdCheck");

    return valid;
}

</script>
<nav id="navHeader" class="navbar-nav-main-menu navbar navbar-expand-sm navbar-dark fixed-top nav-color nav-main-link row">
    <div class="col-6">
        <a class="navbar-brand" href="javascript:void(0);" onclick="location.reload()" style="padding: 14.5px 0px">
            <img src="images/ft_logo.png" alt="Logo" style="margin-top: -4.2px;margin-left: 3.5px;width: 112px;" alt="HiTech Engineering"/>
            <span style="font-size:large;margin-left: 2rem;" id="headerTitle"></span>
        </a>
    </div>
    <!-- <div class="navbar-nav-w-menu">
        <ul id="mainMenu" class="navbar-nav">
        </ul>
    </div> -->
    <!-- job선택 -->
    <div class="col-1 text-right selJob" style="color:white">
        PROJECT NAME
    </div>
    <div class="col">
        <div class="input-group selJob" title="PROJECT SELECT">
            <input type="text" class="form-control" id="pjtJobName" value="PROJECT을 선택하세요" readonly />
            <div class="input-group-append" id="btnStaffOnly" style="display:none">
                <button class="btn btn-info" id="btnPjtSelect"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </div>
    </div>
    <ul class="navbar-nav ml-auto navbar-nav-n Icon">
        <li class="nav-item navbar-nav-n-menu">
            <a class="nav-link" id="mobileSelJob" href="#">
                <span class="fa-solid fa-magnifying-glass fa-2x"></span>
            </a>
            <!-- <div class="dropdown-menu dropdown-menu-right">
                <div id="mainMenuMobile" class="container-fluid"></div>
            </div> -->
        </li>
        <li class="nav-item nav-pc" style="display:none">
             <a class="nav-link" onclick="location.reload()" href="#" title="새로고침">
                <span class="fa-solid fa-arrows-rotate fa-2x"></span>
            </a>
        </li>
        <li class="nav-item organization" style="display:none" onclick="organizationOpen()">
            <a class="nav-link" href="#" title="조직도">
                <i class="fa-solid fa-sitemap fa-2x"></i>
            </a>
        </li>
        <!-- Dropdown -->
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white" href="#" id="navbardrop" data-toggle="dropdown" title="로그아웃">
                <span class="fa-stack">
                    <i class="far fa-circle fa-stack-2x"></i>
                    <i class="fas fa-user fa-stack-1x"></i>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <div class="dropdown-item-text" href="#" onclick="onUserInfoClick()"><?php echo $_SESSION["user"]["user_name"]?>(<?php echo $_SESSION["user"]["user_id"]?>)</div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalRightSetting" id="btnRightSetting" style="display:none">사용자 권한 설정</a>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalChangePassword" id="btnChangePassword">비밀번호 변경</a>
                <a class="dropdown-item" href="#" onclick="onLogoutClick()">Logout</a>
            </div>
        </li>
    </ul>
</nav>
<div id="divSubMenuContent">
<form id="mainForm" name="mainForm">
<div id="sidebar" class="vertical-nav bg-light">
<div id="leftmenuinnerinner">
<div id="closeSidebar" class="clearfix">
    <button type="button" class="close btn-close mt-1 mr-2" onclick="showSubMenu()">&times;</button>
</div>
<!-- <nav id="subMenu" class="navbar-nav-sub-menu navbar bg-light navbar-light"> -->
<!--     <ul class="navbar-nav"> -->
<!--     </ul> -->
<!-- </nav> -->
<div id="subMenu" style="padding-top: 0.5rem;font-size:14px !important">
    <ul class="tree">
        <li class="branch" id="vdcs" style="display:none">
            <span style="width:min-content" onclick="collapseTree(this)"><i class="indicator fas fa-minus-circle"></i>Document</span>
            <ul>
                <li>
                    <a id="vpLatest" onclick="activeSubMenu(this)">VDCS - Latest</span></a>
                </li>
            </ul>
        </li>
        <li class="branch" id="equip" style="display:none">
            <span style="width:min-content" onclick="collapseTree(this)"><i class="indicator fas fa-minus-circle"></i>Equipment List</span>
        </li>
        <li class="branch" id="welding" style="display:none">
            <span style="width:min-content" onclick="collapseTree(this)"><i class="indicator fas fa-minus-circle"></i>Welding</span>
            <ul>
                <li>
                    <a id="w_day" onclick="activeSubMenu(this)">WELD(DAY)</span></a>
                </li>
                <li>
                    <a id="w_month" onclick="activeSubMenu(this)">WELD(MONTH)</span></a>
                </li>
                <li>
                    <a id="n_iso" onclick="activeSubMenu(this)">NDE(By DWG)</span></a>
                </li>
                <li>
                    <a id="n_welder" onclick="activeSubMenu(this)">NDE(By WELDER)</span></a>
                </li>
                <li>
                    <a id="pkg" onclick="activeSubMenu(this)">PKG LIST</span></a>
                </li>
                <!-- <li>
                    <a id="Report4" onclick="activeSubMenu(this)">Report4</span></a>
                </li>
                <li>
                    <a id="Report5" onclick="activeSubMenu(this)">Report5</span></a>
                </li> -->
            </ul>
        </li>
    </ul>
</div>
</div>
</div>
<!-- JOB 선택 모달 -->
<div id="jqxWidget" style="display:none">
    <div id="mainDemoContainer">
        <div id="jobSelWindow">
            <div id="jobSelWindowHeader">
                <span>PROJECT 선택</span>
            </div>
            <div style="overflow: hidden;" id="jobSelWindowContent">
                <div class="container-fluid p-3 my-3 border" id="selJobFilter">
                    <div class="form-check-inline" id="vdcsOnly">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="jobCondition" value="VDCS_USE" checked>VDCS Used
                        </label>
                    </div>
                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="jobCondition" value="STAFF">조직도(STAFF)
                        </label>
                    </div>
                    <div class="form-check-inline">
                        <label class="form-check-label">
                            <input type="radio" class="form-check-input" name="jobCondition" value="ALL">전체(ALL)
                        </label>
                    </div>
                </div>
                <div id="jobListGrid"></div>
                <br />
                <div class="d-flex justify-content-around">
                    <button type="button" id="btnCancelJobSel" class="btn btn-secondary">닫기</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="divHeader"> 
    <nav>
        <ol class="breadcrumb">
            <li class="header-item-n">
                <button type="button" class="btn" onclick="showSubMenu()">
                    <span class="fas fa-bars"></span>
                </button>
            </li>
            <li class="header-item-n mobileJob"></li>
            <li class="breadcrumb-item" id="bigMenu"></li>
            <li class="breadcrumb-item" id="smMenu"></li>
        </ol>
    </nav>
</div>
<!-- <div id="divFooter">
<div class="footer" style="height:30px;">
    <div style="z-index:0;padding-left:1rem;padding-top:0.5rem" id="footer"></div>
</div>
</div> -->
</div>
<div id="vdcsContent" class="page-content p-2">
    <div class="alert alert-success text-center" style="display:none" id="noPjtHeader">
        <strong>PROJECT를 선택하세요.</strong>
    </div>
</div>

  <!-- 조직도 모달 -->
<div class="modal fade" id="modalOrganization">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
        
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">조직도</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <!-- Modal body -->
            <div class="modal-body">
                <div class="tableFixHead-modal">
                    <table class="table table-bordered table-sm" id="tblOrganization">
                        <thead class="thead-light">
                            <th>No</th>
                            <th>공종</th>
                            <th>담당</th>
                            <th>담당상세</th>
                            <th>이름</th>
                            <th>직위</th>
                            <th>소속</th>
                            <th>핸드폰</th>
                            <th>전화</th>
                            <th>이메일</th>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            
            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="container-fluid">
                    <div class="d-flex justify-content-around divBtnList">
                        <button type="button" class="btn btn-secondary mr-4" data-dismiss="modal">닫기</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal fade" id="modalChangePassword" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">비밀번호 변경</h4>
                <button type="button" class="close btn-close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="alert alert-secondary">현재 비밀번호를 입력한 후 새로 사용할 비밀번호를 입력하세요.</div>
                <div>
                    <div id="resultPwdMsg" class="alert py-1 mb-3" style="display: none;"></div>
                </div>
                <div class="row form-group">
                    <div class="col-4 colHeader">
                        <label for="existPwd">기존 비밀번호</label><span class="necessaryInput"> *</span>
                    </div>
                    <div class="col-8">
                        <input type="password" class="form-control validateElement" id="existPwd" name="existPwd" maxlength="100" required />
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-4 colHeader">
                        <label for="newPwd">변경 비밀번호</label><span class="necessaryInput"> *</span>
                    </div>
                    <div class="col-8">
                        <input type="password" class="form-control validateElement lengthCheck" id="newPwd" name="newPwd" maxlength="100" required />
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-4 colHeader">
                        <label for="newPwdCheck">비밀번호 확인</label><span class="necessaryInput"> *</span>
                    </div>
                    <div class="col-8">
                        <input type="password" class="form-control validateElement lengthCheck" id="newPwdCheck" name="newPwdCheck" maxlength="100" required />
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="alert alert-info mt-2" id="pwdLengthRule" name="pwdLengthRule">4 ~ 16자의 영문 대소문자, 숫자, 특수문자 혼용 사용할 수 있습니다.</div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <div class="container">
                    <div class="d-flex justify-content-around">
                        <button type="button" class="btn btn-primary" id="btnSavePwd" name="btnSavePwd">저장</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">닫기</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
?>

<input type="hidden" id="jobFilter" name="jobFilter" />
<input type="hidden" id="uno" name="uno" />
<input type="hidden" id="teamId" name="teamId" />
<input type="hidden" id="userId" name="userId" />

<?php
    require_once "footer.php";
?>
