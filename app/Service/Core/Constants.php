<?php
/**
 * This file is part of the wangningkai/OLAINDEX-CMD.
 * (c) wangningkai <i@ningkai.wang>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Service\Core;

class Constants
{
    const DEFAULT_CALLBACK_URI = 'https://olaindex.ningkai.wang';

    const DEFAULT_RETRY = 1; // 默认重试次数

    const DEFAULT_TIMEOUT = 120; // 默认超时时间

    const DEFAULT_CONNECT_TIMEOUT = 5; // 默认连接超时时间

    const API_VERSION = 'v1.0';

    const REST_ENDPOINT = 'https://graph.microsoft.com/';

    const AUTHORITY_URL = 'https://login.microsoftonline.com/common';

    const AUTHORIZE_ENDPOINT = '/oauth2/v2.0/authorize';

    const TOKEN_ENDPOINT = '/oauth2/v2.0/token';

    // support 21vianet
    const REST_ENDPOINT_21V = 'https://microsoftgraph.chinacloudapi.cn/';

    const AUTHORITY_URL_21V = 'https://login.partner.microsoftonline.cn/common';

    const AUTHORIZE_ENDPOINT_21V = '/oauth2/authorize';

    const TOKEN_ENDPOINT_21V = '/oauth2/token';

    const SCOPES = 'offline_access user.read files.readwrite.all';

    const ACCOUNT_CN = 0; // 世纪互联版

    const ACCOUNT_COM = 1; // 国际版
}
