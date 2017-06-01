<?php
/*****************登录处理开始***************/
//数据校验
if (!isset($_POST['no']) || empty($_POST['no']) || !isset($_POST['password']) || empty($_POST['password']) || !isset($_POST['act']) || empty($_POST['act'])) {
    $eArr = array('info' => "账号或密码错误!");
    echo json_encode($eArr);
    return;
}
$loginUrl = "http://jw.lzzy.net/st/login.aspx";

$loginData = get_all_hidden(get_content($loginUrl));

$loginData["txt_卡学号"] = $_POST['no'];
$loginData["txt_密码"] = $_POST['password'];
$loginData["Button_登陆.x"] = "0";
$loginData["Button_登陆.y"] = "0";
$loginData["Rad_角色"] = "学生";
$shareCurl = curl_share_init();
curl_share_setopt($shareCurl, CURLSHOPT_SHARE, CURL_LOCK_DATA_COOKIE);
curl_share_setopt($shareCurl, CURLSHOPT_SHARE, CURL_LOCK_DATA_SSL_SESSION);
curl_share_setopt($shareCurl, CURLSHOPT_SHARE, CURL_LOCK_DATA_DNS);
$loginCurl = login_post($loginUrl, $loginData, $shareCurl);
if ($loginCurl == 404) {
    $eArr = array('info' => "服务器异常!");
    echo json_encode($eArr);
    return;
}
if ($loginCurl == false) {
    $eArr = array('info' => "账号或密码错误!");
    echo json_encode($eArr);
    return;
}
if ($_POST['act'] == "getTimeTable") {

    if (isset($_POST['term'])) {
        $jsonData = get_timeTable($shareCurl, intval($_POST['term']));
    } else {
        $jsonData = get_timeTable($shareCurl);
    }
    echo json_encode($jsonData);
    return;

}
$userArr = [
    'userName' => get_userName($shareCurl),
    'userImg' => get_usrImg($shareCurl)
];
$jsonData = array_merge($userArr, get_learn($shareCurl), get_teacher($shareCurl), get_schoolroom(), get_evaluate($shareCurl), get_achievement($shareCurl));
curl_close($loginCurl);
echo json_encode($jsonData);
return;


/*****************登录处理结束***************/

/**
 * 获取用户头像
 * @param $shareCurl 共享cookie的curl_share句柄(登录成功的)
 * @return string  用户头像的网络路径
 */

function get_usrImg($shareCurl) {
    $_url = "http://jw.lzzy.net/st/student/left.aspx";
    $_html = get_content($_url, $shareCurl);
    preg_match('/(?<=src=\").*?(\.jpg|png|bmp|pcx|tiff|gif|jpeg|tga|exif|fpx|svg|psd|cdr|pcd|dxf|ufo|eps|ai|hdri|raw)/is', $_html, $_arr);;
    return "http://jw.lzzy.net/st/student/" . $_arr[0];
}

/**
 * 获取用户名字
 * @param $shareCurl 共享cookie的curl_share句柄(登录成功的)
 * @return string 用户名字
 */
function get_userName($shareCurl) {
    $_url = "http://jw.lzzy.net/st/student/main.aspx";
    $_html = get_content($_url, $shareCurl);
    preg_match('/(?<=lbl_姓名\">).*(?=<\/span>&)/s', $_html, $_arr);
    return $_arr[0];
}

/**
 * 获取我的学习(学科成绩)
 * @param $shareCurl  共享cookie的curl_share句柄(登录成功的)
 * @return array  成绩数组
 */
function get_learn($shareCurl) {
    $_url = "http://jw.lzzy.net/st/student/st_g.aspx";
    $_html = get_content($_url, $shareCurl);
    preg_match_all('/(?<=color:\s#FF0000\">)[0-9\.]+(?=<\/a>)/', $_html, $_allNumberArr);
    $_learnData = array();
    preg_match_all('/<option.*?<\/option>/s', $_html, $_pageNumberArr);
    $_pageNumer = count($_pageNumberArr[0]);
    preg_match_all('/(?<=<td><font\scolor=\"#\w{6}\"\ssize=\"2\">).*?(?=<\/font>)/s', $_html, $_arr);
    for ($j = 0; $j < count($_arr[0]); $j += 5) {
        $_learnData[] = [
            "term" => $_arr[0][$j],
            "causes" => $_arr[0][$j + 1],
            "type" => $_arr[0][$j + 2],
            "score" => $_arr[0][$j + 3],
            "remarks" => $_arr[0][$j + 4],
        ];
    }
    if ($_pageNumer > 1) {
        for ($i = 1; $i < $_pageNumer; $i++) {
            $_postData = get_all_hidden($_html);
            $_postData['__EVENTTARGET'] = "Drl_b_转到某页";
            $_postData['Drl_b_转到某页'] = $i + 1;
            $_pageHtml = post_content($_url, $_postData, $shareCurl);
            preg_match_all('/(?<=<td><font\scolor=\"#\w{6}\"\ssize=\"2\">).*?(?=<\/font>)/s', $_pageHtml, $_pageArr);
            for ($j = 0; $j < count($_pageArr[0]); $j += 5) {
                $_learnData[] = [
                    "term" => $_pageArr[0][$j],
                    "causes" => $_pageArr[0][$j + 1],
                    "type" => $_pageArr[0][$j + 2],
                    "score" => $_pageArr[0][$j + 3],
                    "remarks" => $_pageArr[0][$j + 4],
                ];
            }
        }
    }

    return [
        'courseNumber' => $_allNumberArr[0][0],
        'learnNumber' => $_allNumberArr[0][1],
        'noPassNumber' => $_allNumberArr[0][2],
        'learnData' => $_learnData,
    ];
}


/**
 * 获取老师
 * @param $shareCurl  共享cookie的curl_share句柄(登录成功的)
 * @return array  全部老师数组
 */

function get_teacher($shareCurl) {
    $_url = "http://jw.lzzy.net/st/student/st_myteacher.aspx";
    $_html = get_content($_url, $shareCurl);
    preg_match_all('/<option.*?<\/option>/s', $_html, $_pageNumberArr);
    $_pageNumber = count($_pageNumberArr[0]);
    $_data = [];
    preg_match_all('/(?<=学年学期\">).*?(?=<\/)|(?<=课程名称\"\starget=\"_blank\">).*?(?=<\/)|(?<=_HyperLink1\"\starget=\"_blank\">).*?(?=<\/)|(?<=性别\"\starget=\"_blank\">).*?<\/|(?<=移动电话\">).*?<\/|(?<=邮箱\">).*?<\//s', $_html, $_arr);
    for ($j = 0; $j < count($_arr[0]); $j += 6) {
        $_data[] = [
            "term" => $_arr[0][$j],
            "course" => $_arr[0][$j + 1],
            "name" => $_arr[0][$j + 2],
            "sex" => substr($_arr[0][$j + 3], 0, -2) ? substr($_arr[0][$j + 3], 0, -2) : "",
            "phone" => substr($_arr[0][$j + 4], 0, -2) ? substr($_arr[0][$j + 4], 0, -2) : "",
            "email" => substr($_arr[0][$j + 5], 0, -2) ? substr($_arr[0][$j + 5], 0, -2) : "",
        ];
    }
    if ($_pageNumber > 1) {
        for ($i = 1; $i < $_pageNumber; $i++) {
            $_postData = get_all_hidden($_html);
            $_postData['__EVENTTARGET'] = "Drl_b_转到某页";
            $_postData['Drl_b_转到某页'] = $i + 1;
            $_pageHtml = post_content($_url, $_postData, $shareCurl);

            preg_match_all('/(?<=学年学期\">).*?(?=<\/)|(?<=课程名称\"\starget=\"_blank\">).*?(?=<\/)|(?<=_HyperLink1\"\starget=\"_blank\">).*?(?=<\/)|(?<=性别\"\starget=\"_blank\">).*?<\/|(?<=移动电话\">).*?<\/|(?<=邮箱\">).*?<\//s', $_pageHtml, $_pageArr);
            for ($j = 0; $j < count($_pageArr[0]); $j += 6) {
                $_data[] = [
                    "term" => $_pageArr[0][$j],
                    "course" => $_pageArr[0][$j + 1],
                    "name" => $_pageArr[0][$j + 2],
                    "sex" => substr($_pageArr[0][$j + 3], 0, -2) ? substr($_pageArr[0][$j + 3], 0, -2) : "",
                    "phone" => substr($_pageArr[0][$j + 4], 0, -2) ? substr($_pageArr[0][$j + 4], 0, -2) : "",
                    "email" => substr($_pageArr[0][$j + 5], 0, -2) ? substr($_pageArr[0][$j + 5], 0, -2) : "",
                ];
            }

        }
    }
    return ['teacherData' => $_data];
}

/**
 * 获取第二课堂(活动分)保证登录的数据无错才运行
 * @return array 活动分数组
 */

function get_schoolroom() {

    $_loginUrl = "http://crp.lzzy.net/class2/login.aspx";
    $_loginData = get_all_hidden(get_content($_loginUrl));

    $_loginData["txt_卡号"] = $_POST['no'];
    $_loginData["txt_密码"] = $_POST['password'];
    $_loginData["Button_登陆.x"] = "0";
    $_loginData["Button_登陆.y"] = "0";
    $_loginData["Rad"] = "Rad_学生";
    $_shareCurl = curl_share_init();
    curl_share_setopt($_shareCurl, CURLSHOPT_SHARE, CURL_LOCK_DATA_COOKIE);
    $_loginCurl = curl_init();
    curl_setopt($_loginCurl, CURLOPT_URL, $_loginUrl);//登录提交的地址
    curl_setopt($_loginCurl, CURLOPT_SHARE, $_shareCurl);
    curl_setopt($_loginCurl, CURLOPT_HEADER, 0);//是否显示头信息
    curl_setopt($_loginCurl, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息
    curl_setopt($_loginCurl, CURLOPT_POST, 1);//post方式提交
    curl_setopt($_loginCurl, CURLOPT_POSTFIELDS, http_build_query($_loginData));//要提交的信息
    $_getScoreUrl = "http://crp.lzzy.net/class2/Class2_0101.aspx";
    $_getScoreCurl = curl_init();
    curl_setopt($_getScoreCurl, CURLOPT_SHARE, $_shareCurl);
    curl_setopt($_getScoreCurl, CURLOPT_URL, $_getScoreUrl);
    curl_setopt($_getScoreCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_exec($_loginCurl);
    $_html = curl_exec($_getScoreCurl);
    curl_close($_getScoreCurl);
    curl_close($_loginCurl);
    curl_share_close($_shareCurl);

    preg_match('/(?<=<span\sid=\"lbl_记录数\">).*?(\w*)(?=<\/)/s', $_html, $_totalArr);
    preg_match('/(?<=<font\scolor=\"Red\">).*?(?=<\/)/s', $_html, $_totalScoreArr);
    preg_match_all('/(?<=Lab序号\">).*?(?=<\/)|(?<=Lab活动名称\">).*?(?=<\/)|(?<=Lab活动类别\">).*?(?=<\/)|(?<=Lab活动内容\">).*?(?=<\/)|(?<=Lab开始日期\">).*?(?=<\/)|(?<=Lab结束日期\">).*?(?=<\/)|(?<=Lab指导教师\">).*?<\/|(?<=Lab学分\">).*?(?=<\/)/s', $_html, $_arr);
    $_data = [];
    for ($i = 0; $i < count($_arr[0]); $i += 8) {
        $_data[] = [
            'schoolroomNo' => $_arr[0][$i],
            'schoolroomActivityName' => $_arr[0][$i + 1],
            'schoolroomActivityType' => $_arr[0][$i + 2],
            'schoolroomActivityContent' => $_arr[0][$i + 3],
            'schoolroomStartTime' => $_arr[0][$i + 4],
            'schoolroomEndTime' => $_arr[0][$i + 5],
            'schoolroomTeacher' => substr($_arr[0][$i + 6], 0, -2) ? substr($_arr[0][$i + 6], 0, -2) : "",
            'schoolroomScore' => $_arr[0][$i + 7]
        ];
    }
    return [
        'schoolroomTotalNumber' => $_totalArr[0],
        'schoolroomTotalScore' => $_totalScoreArr,
        'schoolroomData' => $_data
    ];
}

/**
 * 获取综合测评(诚信分)
 * @param $shareCurl  共享cookie的curl_share句柄(登录成功的)
 * @return array 诚信分数组
 */

function get_evaluate($shareCurl) {

    $_url = "http://jw.lzzy.net/st/student/st_c.aspx";
    $_html = get_content($_url, $shareCurl);
    preg_match_all('/<option.*?<\/option>/s', $_html, $_pageNumberArr);
    preg_match('/(?<=lbl_总积分\"\sstyle=\"display:inline-block;\"><b><font\scolor=\"Blue\">).*?(?=<\/)/s', $_html, $_totalScoreArr);
    preg_match('/(?<=<span\sid=\"lbl_b_记录数\">)\w+(?=<\/)/', $_html, $_totalNumberArr);

    $_pageNumber = count($_pageNumberArr[0]);
    $_data = [];
    preg_match_all('/(?<=<td><font\scolor=\"#\d{6}\"\ssize=\"2\">).*?(?=<\/font>)/s', $_html, $_arr);
    for ($j = 0; $j < count($_arr[0]); $j += 5) {
        $_data[] = [
            "time" => $_arr[0][$j],
            "causes" => $_arr[0][$j + 1],
            "type" => $_arr[0][$j + 2],
            "score" => $_arr[0][$j + 3],
            "remarks" => $_arr[0][$j + 4] == '&nbsp;' ? '' : $_arr[0][$j + 4]
        ];
    }

    if ($_pageNumber > 1) {
        for ($i = 1; $i < $_pageNumber; $i++) {
            $_postData = get_all_hidden($_html);
            $_postData['__EVENTTARGET'] = "Drl_b_转到某页";
            $_postData['Drl_b_转到某页'] = $i + 1;
            $_pageHtml = post_content($_url, $_postData, $shareCurl);

            preg_match_all('/(?<=<td><font\scolor=\"#\d{6}\"\ssize=\"2\">).*?(?=<\/font>)/s', $_pageHtml, $_pageArr);
            for ($j = 0; $j < count($_pageArr[0]); $j += 5) {
                $_data[] = [
                    "time" => $_pageArr[0][$j],
                    "causes" => $_pageArr[0][$j + 1],
                    "type" => $_pageArr[0][$j + 2],
                    "score" => $_pageArr[0][$j + 3],
                    "remarks" => $_pageArr[0][$j + 4] == '&nbsp;' ? '' : $_pageArr[0][$j + 4]
                ];
            }

        }
    }
    return [
        'evaluateTotalScore' => $_totalScoreArr[0],
        'evaluateTotalNumber' => $_totalNumberArr[0],
        'evaluateData' => $_data
    ];
}

/**
 * 综合素质考评成绩
 * @param $shareCurl 共享cookie的curl_share句柄(登录成功的)
 * @return array 综合成绩数组
 */

function get_achievement($shareCurl) {
    $_url = "http://jw.lzzy.net/st/student/st_zhszkp.aspx";
    $_html = get_content($_url, $shareCurl);
    preg_match_all('/(?<=<font\scolor=\"\#\w{6}\"\ssize=\"2\">).*?(?=<\/)/s', $_html, $_arr);
    $_data = [];
    for ($i = 10; $i < count($_arr[0]); $i += 10) {
        $_data[] = [
            'achievementTerm' => $_arr[0][$i + 9],
            'achievementAverage' => $_arr[0][$i + 5],
            'achievementIntegral' => $_arr[0][$i + 6],
            'achievementResult' => $_arr[0][$i + 7],
            'achievementRanking' => $_arr[0][$i + 8]
        ];
    }
    return ['achievementData' => $_data];
}

/**
 * 获取课程表
 * @param $shareCurl 共享cookie的curl_share句柄(登录成功的)
 * @param null $term 要查询的学期
 * @return array 课程表数组
 */

function get_timeTable($shareCurl, $term = null) {

    $_studentTerm = intval(substr($_POST['no'], 0, 4));
    $_term = [
        $_studentTerm . '-' . ($_studentTerm + 1) . '学年第一学期',
        $_studentTerm . '-' . ($_studentTerm + 1) . '学年第二学期',
        ($_studentTerm + 1) . '-' . ($_studentTerm + 2) . '学年第一学期',
        ($_studentTerm + 1) . '-' . ($_studentTerm + 2) . '学年第二学期',
        ($_studentTerm + 2) . '-' . ($_studentTerm + 3) . '学年第一学期',
        ($_studentTerm + 2) . '-' . ($_studentTerm + 3) . '学年第二学期',

    ];
    $_m = date('m');
    switch ((intval(date('Y')) - $_studentTerm)) {
        case 0:
            $_items = 1;
            break;
        case 1:
            $_items = $_m > 8 ? 3 : 2;
            break;
        case 2:
            $_items = $_m > 8 ? 5 : 4;
            break;
        default:
            $_items = 6;
            break;
    }
    $_termData = [];
    for ($i = 0; $i < $_items; $i++) {
        $_termData[] = $_term[$i];
    }
    if ($term === null) {
        $_url = "http://jw.lzzy.net/st/student/st_p.aspx";
        $html = get_content($_url, $shareCurl);
        preg_match('/(?<=lbl_b_周次\">)\d+(?=<\/)/', $html, $_z);
        $_data = recursive_getTimeTable($_term[($_items - 1)], $shareCurl);
        $_weekData = [];
        for ($_i = 1; $_i < 21; $_i++) {
            $_weekData[] = "第 $_i 周";
        }
        return [
            'nowD' => date('w') == 0 ? 6 : date('w') - 1,
            'nowW' => intval($_z[0]),
            'weekData' => $_weekData,
            'termData' => $_termData,
            'timeTableData' => $_data];

    } else {
        $_data = recursive_getTimeTable($_termData[$term], $shareCurl);
        return [
            'timeTableData' => $_data
        ];
    }

}


/**
 * 递归获取整个学期课程表
 * @param $term          学期
 * @param $shareCurl     share_curl句柄
 * @param null $html 用于递归抓取的网页数据
 * @param array $timeTableArr 用于递归叠加的数组
 * @param int $items 用于递归次数判定
 * @return array         要获取学期的课程表
 *
 */

function recursive_getTimeTable($term, $shareCurl, $html = null, $timeTableArr = [], $items = 0) {

    $_url = "http://jw.lzzy.net/st/student/st_p.aspx";
    if ($items == 0) {
        $html = get_content($_url, $shareCurl);
        preg_match('/(?<=lbl_b_周次\">)\d+(?=<\/)/', $html, $_z);
        if (intval($_z[0]) > 1) {
            $_t = (intval(substr($term, 0, 4)) - 1) . '-' . (intval(substr($term, 5, 9)) - 1) . '学年第一学期';
            $_postData = get_all_hidden($html);
            $_postData['cbo_学年学期'] = $_t;
            $_postData['__EVENTTARGET'] = 'cbo_学年学期';
            $_html1 = post_content($_url, $_postData, $shareCurl);
            $_postData = get_all_hidden($_html1);
            $_postData['cbo_学年学期'] = $term;
            $_postData['__EVENTTARGET'] = 'cbo_学年学期';
            $_html = post_content($_url, $_postData, $shareCurl);
        } else {
            $_postData = get_all_hidden($html);
            $_postData['cbo_学年学期'] = $term;
            $_postData['__EVENTTARGET'] = 'cbo_学年学期';
            $_html = post_content($_url, $_postData, $shareCurl);
        }
        preg_match_all('/(?<=Label\w{4}\").*?(?=<\/)/s', $_html, $_arr);
    } else {
        $_postData = get_all_hidden($html);
        $_postData['cbo_学年学期'] = $term;
        $_postData['__EVENTTARGET'] = 'LinkButton_下一周';
        $_html = post_content($_url, $_postData, $shareCurl);
        preg_match_all('/(?<=Label\w{4}\").*?(?=<\/)/s', $_html, $_arr);
    }
    $_data = [];
    for ($i = 0; $i < count($_arr[0]); $i++) {
        switch ($i % 7) {
            case 1:
                $_data[1][] = substr($_arr[0][$i], 1) ? preg_replace('/<.*?\/>/s', "", substr($_arr[0][$i], 1)) : "";
                break;
            case 2:
                $_data[2][] = substr($_arr[0][$i], 1) ? preg_replace('/<.*?\/>/s', "", substr($_arr[0][$i], 1)) : "";
                break;
            case 3:
                $_data[3][] = substr($_arr[0][$i], 1) ? preg_replace('/<.*?\/>/s', "", substr($_arr[0][$i], 1)) : "";
                break;
            case 4:
                $_data[4][] = substr($_arr[0][$i], 1) ? preg_replace('/<.*?\/>/s', "", substr($_arr[0][$i], 1)) : "";
                break;
            case 5:
                $_data[5][] = substr($_arr[0][$i], 1) ? preg_replace('/<.*?\/>/s', "", substr($_arr[0][$i], 1)) : "";
                break;
            case 6:
                $_data[6][] = substr($_arr[0][$i], 1) ? preg_replace('/<.*?\/>/s', "", substr($_arr[0][$i], 1)) : "";
                break;
            case 0:
                $_data[0][] = substr($_arr[0][$i], 1) ? preg_replace('/<.*?\/>/s', "", substr($_arr[0][$i], 1)) : "";
                break;
        }

    }
    $timeTableArr[] = $_data;

    if ($items < 19) {
        $items++;
        return recursive_getTimeTable($term, $shareCurl, $_html, $timeTableArr, $items);
    }
    return $timeTableArr;

}


/**
 * 模拟登录
 * @param $url 登录地址
 * @param $loginData 登录数据
 * @param null $shareCurl 是否共享资源，共享请传入curl_share_init()句柄
 * @return int|false|curl 如果是404则连接失败，是false是账号或密码错误(针对crp)，否则返回登录成功的curl句柄用于操作完成后关闭登录的网络请求句柄，释放系统资源
 */
function login_post($url, $loginData, $shareCurl = null) {
    $_curl = curl_init();//初始化curl模块
    curl_setopt($_curl, CURLOPT_URL, $url);//登录提交的地址
    if ($shareCurl !== null) {
        curl_setopt($_curl, CURLOPT_SHARE, $shareCurl);
    }
    curl_setopt($_curl, CURLOPT_HEADER, 0);//是否显示头信息
    curl_setopt($_curl, CURLOPT_RETURNTRANSFER, 1);//是否自动显示返回的信息
    curl_setopt($_curl, CURLOPT_POST, 1);//post方式提交
    curl_setopt($_curl, CURLOPT_POSTFIELDS, http_build_query($loginData));//要提交的信息
    $result = curl_exec($_curl);//执行cURL
    preg_match('/alert\(.*?\)/s', $result, $resultArr);//判断crp是否登录成功
    if ($result == false) {
        curl_close($_curl);
        return 404;
    }
    if (empty($resultArr)) {
        return $_curl;
    }
    return false;
}

/**
 * 使用get方式获取网页
 * @param $url 访问的网址
 * @param $shareCurl 共享curl_share句柄，使用共享cookie访问网页
 * @return Html 网页html内容
 */
function get_content($url, $shareCurl = null) {
    $_curl = curl_init();
    curl_setopt($_curl, CURLOPT_URL, $url);
    if ($shareCurl !== null) {
        curl_setopt($_curl, CURLOPT_SHARE, $shareCurl);
    }
    curl_setopt($_curl, CURLOPT_RETURNTRANSFER, 1);
    $_html = curl_exec($_curl); //执行cURL抓取页面内容
    curl_close($_curl);
    return $_html;
}

/**
 * 通过post方式获取网页
 * @param $url  访问的网页
 * @param $postData post提交的数据
 * @param $shareCurl curl_share句柄
 * @return html 返回请求网页内容
 */
function post_content($url, $postData, $shareCurl = null) {
    $_curl = curl_init();
    curl_setopt($_curl, CURLOPT_URL, $url);
    if ($shareCurl !== null) {
        curl_setopt($_curl, CURLOPT_SHARE, $shareCurl);
    }
    curl_setopt($_curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($_curl, CURLOPT_POST, 1);//post方式提交
    curl_setopt($_curl, CURLOPT_POSTFIELDS, http_build_query($postData));//要提交的信息
    $_html = curl_exec($_curl); //执行cURL抓取页面内容
    curl_close($_curl);
    return $_html;
}

/**
 * 获取网页中所有<input type="hidden">标签的内容形成对应name=>value的数组
 * @param $html 要获取的网页
 * @return array 匹配出来的数组
 */
function get_all_hidden($html) {
    preg_match_all('/type=\"hidden\".*?value.*?\/>/s', $html, $_allHiddenArr);
    $_hiddenArr = array();
    if (is_array($_allHiddenArr[0])) {
        foreach ($_allHiddenArr[0] as $item) {
            preg_match('/(?<=name=\").*?(?=\")/s', $item, $_nameArr);
            preg_match('/(?<=value=\").*?(?=\"\s\/>)/s', $item, $_valueArr);
            $_name = $_nameArr[0];
            $_value = $_valueArr[0];
            $_hiddenArr[$_name] = $_value;
        }
    };
    return $_hiddenArr;
}

?>