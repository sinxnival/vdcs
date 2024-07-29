<style>
.tblLatestList td {
    cursor:pointer;
}
/* #tblLatestList tbody tr:hover {
    background-color:#FFE5CC;
} */
.rowActive {
    background-color:#FFE5CC;
}
.downloadImg{
    cursor:pointer;
}
.resultFinal {
    color:blue;
}
.resultNull {
    font-style: italic;
}
.beforeToday {
    color: red;
}
</style>
<script>
var vm = new Vue({
    el: '#app',
    data: {
        icon: '<i class="fa-solid fa-caret-up"></i>',
        collapse : false,
        latestList : [],
        docHistory : [],
        jno : sessionStorage.getItem("jno"),
        jobName : sessionStorage.getItem("jobName"),
        selectDoc : null,
        showHistory : false,
        pageNo : 1,
        totalCnt : null,
        totalCntString : null, 
        totalPage : null,
        naviOffset : 15,
        pageList : null,
        isRebrowsing : false,
        researchOption : 'so_all',
        researchText : '',
        searchList : {},
        selectHistory : null,
        searchHtml : '',
        selectList : [],
        checkList : [''],
        sd_start_date : '',
        sd_end_date : '',
        sdOption : '0',
        so_dc : '',
        so_rc : '',
        noData : false,
        researchSave : '',
        historyDateList: {},
        maxSeq: 0,
        isDownError: false,
        isStaff: sessionStorage.getItem("isStaff"),
        externalRight: 'Y',
        filterDownloadList: [],
        isSearch: false
    },
    created() {
        // 최신문서 데이터 불러오기
        this.getLatestData();

        // 날짜 min/max값 넣기
        dateMinMaxAppend();

        // 권한 체크
        if(this.jno && this.isStaff == "N") {
            this.isStaffCheck();
        }
    },
    computed: {
        allSelected: {
            get: function() {
                return this.checkList.length === this.selectList.length;
            },
            set: function(e) {
                this.selectList = e ? this.checkList : [];
            }
        }
    },
    methods: {
        //Tools 열고 닫기
        collapseChange() {
            if(this.collapse === false) {
                this.collapse = true;
                this.icon = '<i class="fa-solid fa-caret-down"></i>';
                $("#tools").collapse("hide");
            } else {
                this.collapse = false;
                this.icon = '<i class="fa-solid fa-caret-up"></i>';
                $("#tools").collapse("show");
            }
        },
        // 데이터 가져오기
        getLatestData() {
            var data = this;
            var jno = data.jno;
            var searchCondition = '';
            
            if(jno) {
                $("#modalLoading").modal('show');

                if(this.isRebrowsing) {
                    $.each(this.searchList, function(i, condition) {
                        searchCondition += "&" + i + "=" + condition;
                    });
                } else {
                    searchCondition = "&" + this.researchOption + "=" + this.researchSave;
                }
                searchCondition += "&sd_type=" + this.sdOption + "&sd_start_date=" + this.sd_start_date + "&sd_end_date=" + this.sd_end_date;
                searchCondition += "&so_dc=" + this.so_dc + "&so_rc=" + this.so_rc;

                var isSearch = searchCondition.replace(/&sd_type=\d/g, '');
                isSearch = isSearch.replace(/&\w+=/g, '');
                
                if(!isSearch) {
                    this.isSearch = false;
                } else {
                    this.isSearch = true;
                }

                var url = '/api/vdcs/?api_key=d6c814548eeb6e41722806a0b057da30&api_pass=BQRUQAMXBVY=&mode=latest&jno='+ jno + '&navi_page='+ this.pageNo +'&navi_offset=' + this.naviOffset + searchCondition;
                axios.get(url).then(
                    function(response) {
                        var latest = response["data"];
                        if(latest["Message"] == "Success") {
                            var latestList = latest["Value"];
                            data.latestList = latestList;
                            
                            data.checkList = [];
                            // 체크박스 리스트
                            $.each(latestList, function(i, info) {
                                data.checkList.push(info["doc_no"]);
                            });
                            
                            // 페이지 요소 data에 저장
                            data.totalPage = latest["Navigator"]["TotalPage"];
                            data.totalCnt = latest["Navigator"]["TotalRow"];
                            data.totalCntString = numberToCommas(latest["Navigator"]["TotalRow"].toString());
    
                            // 페이지 정보
                            $("#pageInfo").show();
    
                            data.noData = false;
                        } else if(latest["Value"] == null) {
                            data.latestList = [];
    
                            data.pageNo = 1;
                            data.totalPage = 1;
                            data.totalCnt = 0;
                            data.totalCntString = 0;
    
                            // history 목록, pdf 숨기기
                            data.showHistory = false;
                            data.selectDoc = null;
                            data.noData = true;
                        }
                        // 페이징 바 가져오기
                        data.getPagination();
                        // 선택 박스 초기화
                        data.selectList = [];
                })
                .finally(function () {
                    $("#modalLoading").modal("hide");
                    $("#modalLoading").hide();
                    $(".modal-backdrop").hide();

                    // 배경 초기화
                    // $(".tblLatestList td").each(function() {
                    //     var removeTag = $(this).html().replace("<span style=\"background-color:pink\">", "");
                    //     var removeTag = removeTag.replace("</span>", "");
                    //     $(this).html(removeTag);
                    // });
                    //  검색 배경
                    // if(data.isRebrowsing == false) {
                    //     if(data.researchOption == "so_all" && data.researchSave) {
                    //         $(".tblLatestList td").each(function() {
                    //             var tdText = $(this).text().toUpperCase();
                    //             var upperSearch = data.researchSave.toUpperCase();
                    //             if(tdText.match(upperSearch)) {
                    //                 var indexOri = tdText.match(upperSearch).index;
                    //                 var lengthOri = upperSearch.length;
                    //                 var oriText = $(this).text().substr(indexOri, lengthOri);
                    //                 const redRegExp = new RegExp(data.researchSave, 'gi');
        
                    //                 var ans = $(this).html().replace(redRegExp, "<span style=\"background-color:pink\">" + oriText + "</span>")
                    //                 // 문서번호 일경우
                    //                 if($(this).hasClass("doc_num")) {
                    //                     $(this).find(".so_doc_no").html(ans);
                    //                 } else {
                    //                     $(this).html(ans);
                    //                 }
                    //             }
                    //         });
    
                    //     } else if (data.researchOption != "so_all" && data.researchSave) {
                    //         $(".tblLatestList ." + data.researchOption).each(function() {
                    //             var tdText = $(this).text().toUpperCase();
                    //             var upperSearch = data.researchSave.toUpperCase();
                    //             if(tdText.match(upperSearch)) {
                    //                 var indexOri = tdText.match(upperSearch).index;
                    //                 var lengthOri = upperSearch.length;
                    //                 var oriText = $(this).text().substr(indexOri, lengthOri);
                    //                 const redRegExp = new RegExp(data.researchSave, 'gi');
        
                    //                 var ans = $(this).text().replace(redRegExp, "<span style='background-color:pink'>" + oriText + "</span>")
                    //                 // 문서번호 일경우
                    //                 if($(this).hasClass("doc_num")) {
                    //                     $(this).find(".so_doc_no").html(ans);
                    //                 } else {
                    //                     $(this).html(ans);
                    //                 }
                    //             }
                    //         });
                    //     }
                    // } 
                    // else {
                    //     if(Object.keys(data.searchList).length > 0) {
                    //         for (var key in data.searchList) {
                    //             var wordList = data.searchList[key].split("♡");
                    //             $(wordList).each(function(i, word) {
                    //                 if(key == "so_all") {
                    //                     $(".tblLatestList td").each(function() {
                    //                         var tdText = $(this).text().toUpperCase();
                    //                         var upperSearch = word.toUpperCase();
                    //                         if(tdText.match(upperSearch)) {
                    //                             var indexOri = tdText.match(upperSearch).index;
                    //                             var lengthOri = upperSearch.length;
                    //                             var oriText = $(this).text().substr(indexOri, lengthOri);
                    //                             const redRegExp = new RegExp(word, 'gi');
                    
                    //                             var ans = $(this).html().replace(redRegExp, "<span style=\"background-color:pink\">" + oriText + "</span>")
                    //                             // 문서번호 일경우
                    //                             if($(this).hasClass("doc_num")) {
                    //                                 $(this).find(".so_doc_no").html(ans);
                    //                             } else {
                    //                                 $(this).html(ans);
                    //                             }
                    //                         }
                    //                     });
                    //                 } else {
                    //                     $(".tblLatestList ." + key).each(function() {
                    //                         var tdText = $(this).text().toUpperCase();
                    //                         var upperSearch = word.toUpperCase();
                    //                         if(tdText.match(upperSearch)) {
                    //                             var indexOri = tdText.match(upperSearch).index;
                    //                             var lengthOri = upperSearch.length;
                    //                             var oriText = $(this).text().substr(indexOri, lengthOri);
                    //                             const redRegExp = new RegExp(word, 'gi');
                    
                    //                             var ans = $(this).text().replace(redRegExp, "<span style='background-color:pink'>" + oriText + "</span>")
                    //                             // 문서번호 일경우
                    //                             if($(this).hasClass("doc_num")) {
                    //                                 $(this).find(".so_doc_no").html(ans);
                    //                             } else {
                    //                                 $(this).html(ans);
                    //                             }
                    //                         }
                    //                     });
                    //                 }
                    //             });
                    //         }
                    //     }
                    // }
                });
            }
        },
        // 행 클릭
        docRowClick(docNum) {
            // Latest 행 active
            this.selectDoc = docNum;
            // docHistory Table 활성화
            this.showHistory = true;

            var data = this;
            var jno = data.jno;

            axios.get('/api/vdcs/?api_key=d6c814548eeb6e41722806a0b057da30&api_pass=BQRUQAMXBVY=&mode=doc_history&jno='+ jno + '&ms_no=' + docNum ).then(
                function(response) {
                    var docHistory = response["data"];
                    if(docHistory["Message"] == "Success") {
                        var docHistory = docHistory["Value"];
                        data.docHistory = docHistory
                        data.selectHistory = docHistory[0]["doc_no"];
                        data.historyRowClick(data.selectHistory);
                    };
                }
            );
        },
        // 페이징바 가져오기
        getPagination() {
            var property = this;

            var url = '../../../common/pagination.php';
            var data = {
                pageNo : this.pageNo,
                totalCnt : this.totalCnt,
                customPageUnit : this.naviOffset
            }
            axios.post(url, data)
            .then(function(response) {
                property.pageList = response["data"]["pageList"];
            })
            .catch(function(error){
                console.log(error);
            });
        },
        // 페이지 이동하기
        onPageNoClick(event) {
            var obj = event.target.tagName;
            
            if(obj == "A") {
                var pageId = event.target.id;
                if(pageId) {
                    pageNo = pageId.split('_');
                    if(this.pageNo != pageNo[1]) {
                        this.pageNo = pageNo[1];
                        this.getLatestData();
                        this.showHistory = false;
                        this.selectDoc = null;
                        this.selectList = [];
                    }
                }
            } else if (obj == "I") {
                pageId = $(event.target).closest("a").attr("id");
                pageNo = pageId.split('_');
                if(this.pageNo != pageNo[1]) {
                    this.pageNo = pageNo[1];
                    this.getLatestData();
                    this.showHistory = false;
                    this.selectDoc = null;
                    this.selectList = [];
                }
            }
        },
        // 검색 버튼
        btnSearchClick() {
            // 결과 내 재검색 on
            if(this.isRebrowsing == true) {
                var extOption = Object.keys(this.searchList).includes(this.researchOption);

                if(extOption) {
                    var element = this.searchList[this.researchOption].split("♡");

                    var overlap = 0
                    var data = this;
                    $.each(element, function(i, text) {
                        if(text == data.researchText) {
                            overlap++;
                        }
                    });

                    if(overlap == 0) {
                        if(this.researchText) {
                            this.searchList[this.researchOption] = this.searchList[this.researchOption] + '♡' + this.researchText
                        }
                    }
                } else {
                    if(this.researchText) {
                        this.searchList[this.researchOption] = this.researchText;
                    }
                }
                this.updateWord();

                this.pageNo = 1;
                this.getLatestData();

                // 검색창 초기화
                this.researchText = '';
            } else {
                this.pageNo = 1;
                this.researchSave = this.researchText;
                this.getLatestData();
            }
        },
        // Distribute File 다운로드
        distributeDownload(doc_no) {
            location.href = '/api/vdcs/?api_key=d6c814548eeb6e41722806a0b057da30&api_pass=BQRUQAMXBVY=&model=DOC_DE_DOWNLOAD&jno='+ this.jno +'&doc_no='+ doc_no;
        },
        // Issue File 다운로드
        IssueDownload(doc_no) {
            location.href = '/api/vdcs/?api_key=d6c814548eeb6e41722806a0b057da30&api_pass=BQRUQAMXBVY=&model=DOC_RE_DOWNLOAD&jno='+ this.jno +'&doc_no='+ doc_no;
        },
        // History 행 클릭
        historyRowClick(doc_no) {
            // Histroy 행 active
            this.selectHistory = doc_no;

            // Pdf 미리보기
            $("#previewPdf").load('../../../pdfjs-3.0.279-dist/web/pdfSmViewer.php?jno=' + this.jno + '&doc_no=' + this.selectHistory);
        },
        // 검색어 삭제
        deleteWord(event) {
            var obj = event.target;
            // 검색옵션 삭제
            if($(obj).hasClass("deleteOption")) {
                var optionNm = $(obj).siblings("span").attr("class");
                delete this.searchList[optionNm];
                this.updateWord();
            }
            // 검색어 삭제 
            else if($(obj).hasClass("deleteWord")) {
                var optionNm = $(obj).siblings("span").attr("class");
                var wordNm = $(obj).siblings("span").text();

                var searchElement = this.searchList[optionNm].split("♡");

                searchElement = searchElement.filter(function(data) {
                    return data != wordNm
                });

                if(searchElement.length == 0) {
                    delete this.searchList[optionNm];
                } else {
                    this.searchList[optionNm] = searchElement.join('♡');
                }

                this.updateWord();
            }

            this.btnSearchClick();
        },
        // 검색어 업데이트
        updateWord() {
            var data = this;
            data.searchHtml = '';
            $.each(data.searchList, function(i, text) {
                var optionNm = $('option[value="'+ i +'"]').text();
                data.searchHtml += '<span class="badge badge-info mr-2" style="font-size:small">';
                data.searchHtml += '<span class="'+ i +'">' + optionNm + '</span>';
                data.searchHtml += ' <span class="deleteOption" style="color:black;cursor:pointer">X</span></span>';

                var searchElement = text.split("♡");
                $.each(searchElement, function(j, element) {
                    data.searchHtml += '<span class="mr-2" style="background-color:#CCE5FF">';
                    data.searchHtml += '<span class="'+ i +'">' + element + '</span>';
                    data.searchHtml += ' ' + '<span class="deleteWord" style="color:black;cursor:pointer;">X</span></span>';
                });
            });
            if(data.so_dc != '') {
                data.searchHtml += '<span class="badge badge-info mr-2" style="font-size:small">';
                data.searchHtml += '<span class="공종(Disc.)">공종(Disc.)</span>';
                data.searchHtml += '</span>';
                data.searchHtml += '<span class="mr-2" style="background-color:#CCE5FF">';
                data.searchHtml += '<span class="'+ data.so_dc +'">' + data.so_dc + '</span>';
                data.searchHtml += '</span>';
            }
            if(data.so_rc != '') {
                data.searchHtml += '<span class="badge badge-info mr-2" style="font-size:small">';
                data.searchHtml += '<span class="Result#">Result#</span>';
                data.searchHtml += '</span>';
                data.searchHtml += '<span class="mr-2" style="background-color:#CCE5FF">';
                data.searchHtml += '<span class="'+ data.so_rc +'">' + data.so_rc + '</span>';
                data.searchHtml += '</span>';
            }
        },
        // 결과 내 검색 사용여부
        isUseRebrowsing() {
            if(this.isRebrowsing) {
                this.searchList = {}
                this.searchHtml = ''
                this.getLatestData();
            } else {
                if(this.researchText) {
                    this.searchList[this.researchOption] = this.researchText;
                    this.researchText = '';
                }
                this.updateWord();
            }
        },
        // 선택 다운로드
        selDocDownload() {
            var selDoc = this.selectList.join(",")

            location.href = '/api/vdcs/?api_key=d6c814548eeb6e41722806a0b057da30&api_pass=BQRUQAMXBVY=&model=DOC_LE_DOWNLOAD&jno='+ this.jno +'&doc_no=' + selDoc;
        },
        // 최신목록 내보내기
        exportLatestExcel() {
            $("#modalLoading").modal("show");
            var data = this;
            axios.get('/api/vdcs/?api_key=d6c814548eeb6e41722806a0b057da30&api_pass=BQRUQAMXBVY=&mode=doc_history&jno='+ this.jno).then(
                function(response) {
                    var historyList = response["data"];
                    if(historyList["Message"] == "Success") {
                        var historyList = historyList["Value"];
                        let msList = [];
                        var hisCnt = 0;
                        $.each(historyList, function(i, info) {
                            msList.push(info["ms_no"]);

                            // 중복 제거
                            msList = msList.filter((element, index) => {
                                return msList.indexOf(element) === index;
                            });
                        });

                        let tempArray = [];
                        let maxSeq = 0;

                        $.each(msList, function(j, ms_no) {
                            tempArray = [];
                            $.each(historyList, function(i, info) {
                                if(ms_no == info["ms_no"]) {
                                    tempArray.push({
                                        hist_distribute_date_str: info["hist_distribute_date_str"],
                                        hist_reply_date_str: info["hist_reply_date_str"],
                                        doc_no: info["doc_no"],
                                        doc_status_nick: info["doc_status_nick"]
                                    });
                                }
                            });
                            // tempArray = tempArray.reverse();
                            data.historyDateList[ms_no] = tempArray;
                            // 최대 차수
                            var seq = data.historyDateList[ms_no].length;
                            if(maxSeq < seq) {
                                maxSeq = seq;
                            }
                        });
                        data.maxSeq = maxSeq;
                        // 다운로드 시작
                        var url = '/vdcs/document/latest/latest_list_download_excel.php';
                        data.axiosDownload("excelDown" ,url, "POST");
                    };
                }
            ).catch(function(error) {
                console.log(error);
                $("#modalLoading").modal("hide");
            });
        },
        // 전체 다운로드
        allDocDownload() {
            var data = this;
            // 다운로드 파일 정보 가져오기
            var url = "/api/vdcs/?api_key=d6c814548eeb6e41722806a0b057da30&api_pass=BQRUQAMXBVY=&model=LATEST_ZIP_INFO&jno=" + data.jno;
            axios.post(url)
            .then(function(response) {
                if((response["data"]["Message"]) == "Success") {
                    var value = response["data"]["Value"];
                    $("#confirmModal .modal-body").html(`전체 다운로드는 대용량인 관계로 전일 22시를 기준으로 생성됩니다.<br />
                                                        이점 감안하시기 바랍니다.<br /><br />
                                                        - 파일 생성일자 : ${value["file_date_str"]}<br />
                                                        - 파일 크기 : ${value["file_size_str"]}`);
                    $("#confirmModal").modal("show");
                }
            })
            .catch(function(error){
                console.log(error);
            });

            var data = this;
            // 확인 버튼 클릭
            $("#btnConfirm").on("click", function() {
                var url = "/api/vdcs/?api_key=d6c814548eeb6e41722806a0b057da30&api_pass=BQRUQAMXBVY=&model=LATEST_ZIP_DOWNLOAD&jno=" + data.jno;

                // 다운 시작
                // data.ajaxDownload(url);
                location.href = url;
            });
        },
        // 쿠키 삭제
        deleteCookie(name) {
            document.cookie = name + '=; expires=Thu, 01 Jan 1999 00:00:10 GMT;';
        },
        // axios 다운로드
        axiosDownload(type, url, method) {
            if(type == "excelDown") {
                var data = {
                    jno: this.jno,
                    jobName: this.jobName,
                    historyDateList: this.historyDateList,
                    maxSeq: this.maxSeq
                }
            } else if(type == "listDownload") {
                var data = {
                    doc_no : this.filterDownloadList.join(",")
                }
            } else {
                var data = '';
            }
            axios({
                url: url,
                data: data,
                method: method,
                responseType: "blob" // 응답 데이터 타입 정의
            })
            .then(function(response) {
                // 다운로드 파일 이름을 추출하는 함수
                const extractDownloadFilename = (response) => {
                    const disposition = response.headers["content-disposition"];
                    const fileName = decodeURI(
                    disposition
                        .match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/)[1]
                        .replace(/['"]/g, "")
                    );
                    return fileName;
                };
                const blob = new Blob([response.data]);
                const fileObjectUrl = window.URL.createObjectURL(blob);

                const link = document.createElement("a");
                link.href = fileObjectUrl;
                link.style.display = "none";
                link.download = extractDownloadFilename(response);

                // 다운로드 파일의 이름은 직접 지정 할 수 있습니다.
                // link.download = "sample-file.xlsx";

                // 링크를 body에 추가하고 강제로 click 이벤트를 발생시켜 파일 다운로드를 실행시킵니다.
                document.body.appendChild(link);
                link.click();
                link.remove();

                // 다운로드가 끝난 리소스(객체 URL)를 해제합니다.
                window.URL.revokeObjectURL(fileObjectUrl);
            })
            .catch(function(error){
                console.log(error);
            })
            .finally(function() {
                $("#modalLoading").modal("hide");
            });
        },
        // percenatage
        showPer(per) {
            $("#percent").text(per + "%");
            $("#percent").show();
        },
        // 파일 다운로드
        ajaxDownload(url) {
            var data = this;
            var downInfo = $.ajax({
                url: url,
                type : 'GET',
                xhrFields: {  //response 데이터를 바이너리로 처리한다.
                    responseType: 'blob'
                },
                beforeSend: function() {
                    $("#modalLoading").modal("show");
                    data.showPer(0);
                },
                xhr: function() {  //XMLHttpRequest 재정의 가능
                    var xhr = $.ajaxSettings.xhr();
                    xhr.onprogress = function(e) {
                        data.showPer(Math.floor(e.loaded / e.total * 100));
                    };
                    return xhr;
                },  
                success : function(response) {
                    // 다운로드 파일 이름을 추출하는 함수
                    const extractDownloadFilename = (response) => {
                        const disposition = downInfo.getResponseHeader('Content-Disposition');
                        const fileName = decodeURI(
                        disposition
                            .match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/)[1]
                            .replace(/['"]/g, "")
                        );
                        return fileName;
                    };
                    const blob = new Blob([response]);
                    const fileObjectUrl = window.URL.createObjectURL(blob);

                    const link = document.createElement("a");
                    link.href = fileObjectUrl;
                    link.style.display = "none";
                    link.download = extractDownloadFilename(response);

                    // 다운로드 파일의 이름은 직접 지정 할 수 있습니다.
                    // link.download = "sample-file.xlsx";

                    // 링크를 body에 추가하고 강제로 click 이벤트를 발생시켜 파일 다운로드를 실행시킵니다.
                    document.body.appendChild(link);
                    link.click();
                    link.remove();

                    // 다운로드가 끝난 리소스(객체 URL)를 해제합니다.
                    window.URL.revokeObjectURL(fileObjectUrl);
                },
                complete: function() {
                    if(data.isDownError) {
                        data.isDownError = false;
                    } else {
                        $("#modalLoading").modal("hide");
                        $("#percent").hide();
                    }
                },
                error:function(request,status,error){
                    data.isDownError = true;
                    data.ajaxDownload(url);
                }
            });
        },
        // 권한 체크
        isStaffCheck () {
            url = "api/common/job/authority.php";
            data = {
                jno: this.jno
            }
            axios.post(url, data)
            .then(function(response) {
                var externalRight = response["data"]["externalRight"];
                sessionStorage.setItem("externalRight", externalRight);
            })
            .catch(function(error){
                console.log(error);
            });
        },
        // 리스트 다운로드
        listDocDownload() {
            var jno = this.jno;
            var searchCondition = '';
            $("#modalLoading").modal('show');

            if(this.isRebrowsing) {
                $.each(this.searchList, function(i, condition) {
                    searchCondition += "&" + i + "=" + condition;
                });
            } else {
                searchCondition = "&" + this.researchOption + "=" + this.researchSave;
            }
            searchCondition += "&sd_type=" + this.sdOption + "&sd_start_date=" + this.sd_start_date + "&sd_end_date=" + this.sd_end_date;
            searchCondition += "&so_dc=" + this.so_dc + "&so_rc=" + this.so_rc;

            var data = this;
            var url = '/api/vdcs/?api_key=d6c814548eeb6e41722806a0b057da30&api_pass=BQRUQAMXBVY=&mode=latest&jno='+ jno + searchCondition;
            axios.get(url)
            .then(function(response) {
                var latest = response["data"];
                if(latest["Message"] == "Success") {
                    var filterDownloadList = latest["Value"];
                    
                    // 체크박스 리스트
                    $.each(filterDownloadList, function(i, info) {
                        data.filterDownloadList.push(info["doc_no"]);
                    });

                    var totalCntString = data.totalCntString;
                    var url = '/api/vdcs/?api_key=d6c814548eeb6e41722806a0b057da30&api_pass=BQRUQAMXBVY=&model=DOC_LE_DOWNLOAD&is_search=Y&jno='+ data.jno
                    // if(url.length <= 2048) {
                    if(totalCntString < 1000) {
                        data.axiosDownload('listDownload', url, "POST");
                    } else {
                        $("#modalLoading").modal('hide');
                        $("#alertModal .modal-body").html("다운받고자 하는 문서의 양이 많습니다.<br/>1000건 미만으로 검색해주세요.");

                        $("#alertModal").modal('show');
                    }
                }
            })
            .finally(function () {
                data.filterDownloadList = [];
            });
        }
    }
})
</script>
<div id="app" style="margin-bottom:30px">
<form id="mainForm" name="mainForm">
<div class="row mb-1">
    <div class="col-md-1">
        <i class="fa-solid fa-magnifying-glass"></i> <b style="font-size:large">Search</b>
    </div>
    <div class="col-md text-right">
        <span v-show="jno">
            <button type="button" class="btn btn-outline-primary btn-sm text-left mr-2 text-center" style="width:150px;" @click="selDocDownload" :disabled="selectList.length == 0" title="선택 다운로드" v-show="externalRight == 'Y'">
                <i class="fa-solid fa-check" style="font-size:large"></i> 선택 다운로드
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm text-left mr-2 text-center" style="width:150px;" @click="allDocDownload" :disabled="latestList.length == 0" title="전체 다운로드" v-show="externalRight == 'Y'">
                <i class="fa-solid fa-floppy-disk" style="font-size:large"></i> 전체 다운로드
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm text-left mr-2 text-center" style="width:150px;" @click="listDocDownload" :disabled="latestList.length == 0 || !isSearch" title="검색 결과의 전체페이지의 파일을 다운로드합니다." v-show="externalRight == 'Y'">
                <i class="fa-solid fa-list-ul" style="font-size:large"></i> 검색 결과 다운로드
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm text-left mr-2 text-center" style="width:150px;" @click="exportLatestExcel" :disabled="latestList.length == 0" title="목록 내보내기" v-show="externalRight == 'Y'">
                <i class="fa-solid fa-file-export" style="font-size:large"></i> 목록 내보내기
            </button>
        </span>
        <button type="button" class="btn btn-outline-dark btn-sm" v-html="icon" @click="collapseChange"></button>
    </div>
</div>
<div class="row">
    <div class="container-fluid mb-1 p-1 mx-3 border collapse show" style="border-radius:10px;" id="tools">
        <div class="row mt-2">
            <div class="col-md row">
                <div class="col-md-1">
                    <label class="ml-2" style="padding-top:0.2rem;color:#A0A0A0">검색조건</label>
                </div>
                <div class="col-md-4">
                    <div class="input-group-prepend">
                        <label for="" style="padding-top:0.2rem">기간</label>
                        <select class="form-control ml-2" style="width:min-content" v-model="sdOption">
                            <option value="0">선택</option>
                            <option value="distribute">배포일</option>
                            <option value="reply">회신일</option>
                        </select>
                        <input type="date" class="form-control mx-2 text-center" v-model="sd_start_date"/>~ 
                        <input type="date" class="form-control ml-2 text-center" v-model="sd_end_date"/>
                    </div>
                </div>
                <div class="col-md-2" style="padding-left:0 !important">
                    <div class="row" style="float:right">
                        <select class="form-control mr-2" style="width:min-content" v-model="so_dc" @change="btnSearchClick">
                            <option value="">공종(Disc.)</option>
                            <option value="PROC">PROC(공정)</option>
                            <option value="STAT">STAT(고정기기)</option>
                            <option value="ROTA">ROTA(회전기계)</option>
                            <option value="PIPE">PIPE(배관)</option>
                            <option value="INST">INST(계기)</option>
                            <option value="ELEC">ELEC(전기)</option>
                            <option value="CAS">CAS(토건)</option>
                            <option value="PERM">PERM(인허가)</option>
                            <option value="PM">PM(사업관리)</option>
                        </select>
                        <select class="form-control" style="width:min-content" v-model="so_rc" @change="btnSearchClick">
                            <option value="">Result#</option>
                            <option value="A">A</option>
                            <option value="N">N</option>
                            <option value="RFC">RFC</option>
                            <option value="R">R</option>
                            <option value="NN">NN</option>
                            <option value="F">F</option>
                            <option value="UN">UN</option>
                            <option value="NULL">(NULL)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <select class="form-control ml-2" v-model="researchOption">
                                <option value="so_all">전체</option>
                                <option value="so_doc_no">문서번호</option>
                                <option value="so_doc_ti">문서제목</option>
                                <option value="so_vn">Vendor</option>
                                <option value="so_tr">TR No.</option>
                                <option value="so_rt">RFQ. No. / Title</option>
                                <option value="so_ti">Item / Tag No.</option>
                                <!-- <option value="so_dc">공종</option> -->
                                <!-- <option value="so_rc">Result #</option> -->
                            </select>
                        </div>
                        <input type="text" class="form-control" v-model="researchText" @keydown.enter.prevent="btnSearchClick"/>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-info" id="btnSearch" @click="btnSearchClick" :disabled="!jno" title="검색"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </div>
                    </div>
                </div>
                <div class="col-md-1 text-right" style="padding:0 !important; padding-top:0.2rem !important">
                    <div class="custom-control custom-switch" style="padding:0 !important" title="결과 내 재검색">
                        <input type="checkbox" class="custom-control-input" id="rebrowsing" v-model="isRebrowsing" @click="isUseRebrowsing">
                        <label class="custom-control-label" for="rebrowsing">결과 내 재검색</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="ml-3" v-html="searchHtml" v-show="isRebrowsing" @click="deleteWord($event)"></div>
    </div>
</div>
<div v-show="jno">
    <!-- pc화면 -->
    <div class="d-none d-xl-block">
        <div class="row">
            <div class="col-3" style="padding-right:0 !important">
                <table class="table table-bordered table-sm tblLatestList" style="height:min-content;">
                    <thead class="thead-light">
                        <tr>
                            <th v-show="externalRight == 'Y'"><input type="checkbox" v-model="allSelected"/></th>
                            <th>공종</th>
                            <th title="Doc. No.">문서번호</th>
                            <th>Rev.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr :key="doc.ms_no" v-for="doc in latestList" :class="{'rowActive' : (doc.ms_no == selectDoc), 'resultFinal' : (doc.doc_status_nick == 'F'), 'resultNull' : (doc.doc_status_nick == '')}">
                            <td class="text-center"><input type="checkbox" v-model="selectList" :value="doc.doc_no" v-show="externalRight == 'Y'"/></td>
                            <td class="text-center" @click="docRowClick(doc.ms_no)">{{ doc.tr_func_cd }}</td>
                            <td @click="docRowClick(doc.ms_no)" style="width:230px" :title="doc.doc_num" class="doc_num"><div class="text-ellipsis so_doc_no">{{ doc.doc_num }}</div></td>
                            <td class="text-center" @click="docRowClick(doc.ms_no)">{{ doc.doc_rev_num }}</td>
                        </tr>
                    </tbody>
                </table>
                <div id="pageInfo" style="display:none;padding-left:25px" v-show="!noData">Pages : <span style="color:red">{{ pageNo }}</span> - {{ totalPage }} / Rows : {{ totalCntString }}</div>
            </div>
            <div class="col-8" style="padding:0 !important;">
                <div class="table-responsive tblLatestList">
                    <table class="table table-bordered table-sm">
                        <thead class="thead-light">
                            <tr>
                                <th class="responsiveTblRow" title="Doc. Title">문서제목</th>
                                <th class="responsiveTblRow" title="Vendor Name">Vendor</th>
                                <th class="responsiveTblRow">TR No.</th>
                                <th class="responsiveTblRow" title="Distribute Date">배포일</th>
                                <th class="responsiveTblRow" title="Reply Date">회신일</th>
                                <th class="responsiveTblRow" title="Next Schedule">차기 접수일</th>
                                <th class="responsiveTblRow">RFQ. No.</th>
                                <th class="responsiveTblRow">RFQ. Title</th>
                                <th class="responsiveTblRow">Item / Tag No.</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr :key="doc.ms_no" v-for="doc in latestList" :class="{'rowActive' : (doc.ms_no == selectDoc), 'resultFinal' : (doc.doc_status_nick == 'F'), 'resultNull' : (doc.doc_status_nick == '')}">
                                <td class="responsiveTblRow so_doc_ti" @click="docRowClick(doc.ms_no)">{{ doc.doc_title }}</td>
                                <td class="text-center responsiveTblRow so_vn" @click="docRowClick(doc.ms_no)">{{ doc.tr_from_comp_name }}</td>
                                <td class="responsiveTblRow so_tr" @click="docRowClick(doc.ms_no)">{{ doc.tr_doc_num }}</td>
                                <td class="text-center responsiveTblRow" @click="docRowClick(doc.ms_no)">{{ doc.doc_distribute_date_str }}</td>
                                <td class="text-center responsiveTblRow" @click="docRowClick(doc.ms_no)">{{ doc.doc_reply_date_str }}</td>
                                <td :class="['text-center', 'responsiveTblRow', {'beforeToday' : new Date(doc.doc_return_date_str) < new Date(doc.to_day_date_str)}]" @click="docRowClick(doc.ms_no)"><div v-show="doc.doc_status_nick != 'A' && doc.doc_status_nick != 'F'">{{ doc.doc_return_date_str }}</div></td>
                                <td class="responsiveTblRow so_rt" @click="docRowClick(doc.ms_no)">{{ doc.doc_rfq_num }}</td>
                                <td class="responsiveTblRow so_rt" @click="docRowClick(doc.ms_no)">{{ doc.doc_rfq_title }}</td>
                                <td class="responsiveTblRow so_ti" @click="docRowClick(doc.ms_no)">{{ doc.doc_tag_item }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col" style="padding-left:0 !important">
                <table class="table table-bordered table-sm tblLatestList" style="height:min-content">
                    <thead class="thead-light">
                        <tr>
                            <th>Cnt</th>
                            <th title="Result Code">
                                Rslt #
                                <div class="tooltipBox"><i class="fa-solid fa-circle-question"></i>
                                    <span class="tooltiptext"><img class="img-thumbnail" src="../../../images/result_code_tooltip.png" /></span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr :key="doc.ms_no" v-for="doc in latestList" :class="{'rowActive' : (doc.ms_no == selectDoc), 'resultFinal' : (doc.doc_status_nick == 'F'), 'resultNull' : (doc.doc_status_nick == '')}">
                            <td class="text-center" @click="docRowClick(doc.ms_no)">{{ doc.doc_cnt }}</td>
                            <td class="text-center" @click="docRowClick(doc.ms_no)">{{ doc.doc_status_nick }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- 모바일 화면 -->
    <div class="table-responsive d-xl-none d-lg-block">
        <table class="table table-bordered table-sm tblLatestList">
            <thead class="thead-light">
                <tr> 
                    <th style="white-space: nowrap;min-width: 1rem" v-show="externalRight == 'Y'"><input type="checkbox" v-model="allSelected"/></th>
                    <th class="responsiveTblRow" title="VP Discipline Code">공종</th>
                    <th class="responsiveTblRow">문서번호</th>
                    <th class="responsiveTblRow">Rev.</th>
                    <th class="responsiveTblRow">문서제목</th>
                    <th class="responsiveTblRow" title="Vendor Name">Vendor</th>
                    <th class="responsiveTblRow">TR No.</th>
                    <th class="responsiveTblRow">배포일</th>
                    <th class="responsiveTblRow">회신일</th>
                    <th class="responsiveTblRow" title="Next Schedule">차기 접수일</th>
                    <th class="responsiveTblRow">RFQ. No.</th>
                    <th class="responsiveTblRow">RFQ. Title</th>
                    <th class="responsiveTblRow">Item / Tag No.</th>
                    <th class="responsiveTblRow">Cnt</th>
                    <th class="responsiveTblRow" title="Result Code">
                        Rslt #
                        <div class="tooltipBox"><i class="fa-solid fa-circle-question"></i>
                            <span class="tooltiptext"><img class="img-thumbnail" src="../../../images/result_code_tooltip.png" /></span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr :key="doc.ms_no" v-for="doc in latestList" :class="{'rowActive' : (doc.ms_no == selectDoc), 'resultFinal' : (doc.doc_status_nick == 'F'), 'resultNull' : (doc.doc_status_nick == '')}">
                    <td class="text-center" style="white-space: nowrap;min-width: 1rem" v-show="externalRight == 'Y'"><input type="checkbox" v-model="selectList" :value="doc.doc_no"/></td>
                    <td class="responsiveTblRow text-center" @click="docRowClick(doc.ms_no)">{{ doc.tr_func_cd }}</td>
                    <td class="responsiveTblRow" @click="docRowClick(doc.ms_no)">{{ doc.doc_num }}</td>
                    <td class="responsiveTblRow text-center" @click="docRowClick(doc.ms_no)">{{ doc.doc_rev_num }}</td>
                    <td class="responsiveTblRow" @click="docRowClick(doc.ms_no)">{{ doc.doc_title }}</td>
                    <td class="responsiveTblRow text-center" @click="docRowClick(doc.ms_no)">{{ doc.tr_from_comp_name }}</td>
                    <td class="responsiveTblRow" @click="docRowClick(doc.ms_no)">{{ doc.tr_doc_num }}</td>
                    <td class="responsiveTblRow text-center" @click="docRowClick(doc.ms_no)">{{ doc.doc_distribute_date_str }}</td>
                    <td class="responsiveTblRow text-center" @click="docRowClick(doc.ms_no)">{{ doc.doc_reply_date_str }}</td>
                    <td :class="['text-center', 'responsiveTblRow', {'beforeToday' : new Date(doc.doc_return_date_str) < new Date(doc.to_day_date_str)}]" @click="docRowClick(doc.ms_no)"><div v-show="doc.doc_status_nick != 'A' && doc.doc_status_nick != 'F'">{{ doc.doc_return_date_str }}</div></td>
                    <td class="responsiveTblRow" @click="docRowClick(doc.ms_no)">{{ doc.doc_rfq_num }}</td>
                    <td class="responsiveTblRow" @click="docRowClick(doc.ms_no)">{{ doc.doc_rfq_title }}</td>
                    <td class="responsiveTblRow" @click="docRowClick(doc.ms_no)">{{ doc.doc_tag_item }}</td>
                    <td class="responsiveTblRow text-center" @click="docRowClick(doc.ms_no)">{{ doc.doc_cnt }}</td>
                    <td class="responsiveTblRow text-center" @click="docRowClick(doc.ms_no)">{{ doc.doc_status_nick }}</td>
                </tr>
            </tbody>
        </table>
    </div>
        <div class="alert alert-warning" v-show="noData">
        <strong>조건에 맞는 결과가 없습니다.</strong>
    </div>
    <ul class="pagination pagination-sm justify-content-center" v-html="pageList" @click="onPageNoClick($event)"></ul>
    <div class="row">
        <div class="col-md-8">
            <table class="table table-bordered table-sm" id="tblDocHistory" v-show="showHistory">
                <thead class="thead-light">
                    <tr>
                        <!-- <th><input type="checkbox" /></th> -->
                        <th>No</th>
                        <th>Rev.</th>
                        <th title="VP Discipline Code">공종</th>
                        <th>문서번호</th>
                        <th>TR No.</th>
                        <th>접수일</th>
                        <th>배포일</th>
                        <th>회신일</th>
                        <th title="Result Code">Rslt #</th>
                        <th title="Distribute">Dist.</th>
                        <th>Reply</th>
                    </tr>
                </thead>
                <tbody>
                    <tr :key="history.doc_no" v-for="history in docHistory" :class="{'rowActive' : (history.doc_no == selectHistory), 'resultFinal' : (history.doc_status_nick == 'F'), 'resultNull' : (history.doc_status_nick == '')}">
                        <!-- <td><input type="checkbox" /></td> -->
                        <td class="text-center" @click="historyRowClick(history.doc_no)">{{ history.rowno }}</td>
                        <td class="text-center" @click="historyRowClick(history.doc_no)">{{ history.doc_rev_num }}</td>
                        <td class="text-center" @click="historyRowClick(history.doc_no)">{{ history.func_cd }}</td>
                        <td @click="historyRowClick(history.doc_no)">{{ history.doc_num }}</td>
                        <td @click="historyRowClick(history.doc_no)">{{ history.tr_doc_num }}</td>
                        <td class="text-center" @click="historyRowClick(history.doc_no)">{{ history.hist_receive_date_str }}</td>
                        <td class="text-center" @click="historyRowClick(history.doc_no)">{{ history.hist_distribute_date_str }}</td>
                        <td class="text-center" @click="historyRowClick(history.doc_no)">{{ history.hist_reply_date_str }}</td>
                        <td class="text-center" @click="historyRowClick(history.doc_no)">{{ history.doc_status_nick }}</td>
                        <td class="text-center"><span class="downloadImg" @click="distributeDownload(history.doc_no)" title="배포문서 다운로드"><img src="../../../images/preview.png" v-show="externalRight == 'Y'"/></span></td>
                        <td class="text-center"><span class="downloadImg" v-show="history.doc_status && history.doc_status_nick != 'F' && externalRight == 'Y'" @click="IssueDownload(history.doc_no)" title="회신문서 다운로드"><img src="../../../images/outlook.png"/></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-md-4 text-center" v-show="showHistory">
            <div class="container-fluid p-1 mb-1 border" id="previewPdf"></div>
        </div>
    </div>
</div>
<div class="alert alert-success text-center" v-show="!jno">
  <strong>PROJECT를 선택하세요.</strong>
</div>
<div class="alert border mt-4" v-show="!jno">
    <div class="mb-4 p-2 text-center"><h5 style="font-weight:bold;">VDCS</h5></div>
    <div class="chargeInfo">
        <div class="my-2"><span><h5 style="font-weight:bold;">프로젝트 정보 및 조직도 관련 문의 : 프로젝트 PM / PE</h5></span></div>
        <div class="my-2"><span><h5 style="font-weight:bold;">TR / VP 관련 문의 : 프로젝트 공종 별 LE</h5></span></div>
        <div class="my-2"><span><h5 style="font-weight:bold;">시스템 운영 및 에러 관련 문의 : 기술연구소 담당(☎1074)</h5></span></div>
    </div>
</div>
</form>
</div>
