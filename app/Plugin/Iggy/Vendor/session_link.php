<?php
// @param string $url
// @return string
function session_link($url)
{
    if (strpos($url, '?') === false) {
        $url .= '?' . session_name() . '=' . session_id();
    } else {
        $url .= '&' . session_name() . '=' . session_id();
    }
    return $url;
}
