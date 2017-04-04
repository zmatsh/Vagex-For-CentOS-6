<?php
/**
 * Vagex Robot 重生版 之 china mode video proxy
 * 这个脚本（video_info.php）用来部署在国外，从而让VagexRobot主题可以在国内运行而不需设置代理
 * 也就是说如果你是在国外vps上面直接用VagexRobot，这个脚本你就用不着了
 * 没看这个注释就问的一律不答
 * @author: horsley
 * @version: 2014-02-16
 */
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $url = 'http://www.youtube.com/watch?v=' . trim($_GET['id']);
    if ($play_page_body = curl_get($url, $play_page_header)) {
        $result = array(
            'watcheduser'   => get_watched_userid($play_page_body),
            'pageData'      => get_page_title($play_page_body),
            'machine'       => get_visitor_id($play_page_header),
            'exactTime'     => get_video_length($play_page_body),
        );
        echo json_encode(array('error' => false, 'data' => $result));
    } else {
        echo json_encode(array('error' => true, 'info' => 'fetch error'));
    }
}
/**
 * Preg find page title from html
 * @param $html
 * @return mixed
 */
function get_page_title($html) {
    preg_match('/<title>(.*?)<\/title>/', $html, $match);
    return isset($match[1])?$match[1]:false;
}
/**
 * Preg Youtube visitor id from response cookie
 * @param $head
 * @return mixed
 */
function get_visitor_id($head) {
    preg_match('/VISITOR_INFO1_LIVE=(.*?);/', $head, $match);
    return isset($match[1])?$match[1]:false;
}
/**
 * Preg Youtube video owner id from html
 * @param $html
 * @return mixed
 */
function get_watched_userid($html) {
    preg_match('/yt-uix-sessionlink yt-user-videos.*\/user\/(.*?)\//', $html, $match);
    return isset($match[1])?$match[1]:false;
}
/**
 * Preg Youtube video duration
 * @param $html
 * @return mixed
 */
function get_video_length($html) {
    preg_match('/"length_seconds":=\s+(\d+),/', $html, $match);
    return isset($match[1])?$match[1]:false;
}
/**
 * 简单的get请求
 * @param $url
 * @param string $header 可选返回header
 * @return bool
 */
function curl_get($url, &$header = '') {
    $ch = curl_init($url);
    curl_setopt_array($ch, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
    ));
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        return false;
    }
    $response = explode("\r\n\r\n", $response, 2);
    $rsp_body = $response[1]; //返回Body
    $header = $response[0];
    curl_close($ch);
    return $rsp_body;
}
