#!/usr/bin/env php
<?php

/**
 * Tool 命令行工具 (youhujun 的别名)
 *
 * 用法:
 *   php tool make:facade <路径> [描述]
 *   php tool make:service <路径> [描述]
 *   php tool call:facade <路径> [描述]
 *
 * 示例:
 *   php tool make:facade Facade/V1/Wechat/Official/WechatOfficialWebAuth
 *   php tool make:service Service/V1/Wechat/Official/WechatOfficialWebAuth
 *   php tool call:facade V1/Wechat/Official/WechatOfficialWebAuth "微信公众号网页授权服务"
 */

// 调用 youhujun 命令
require __DIR__ . '/youhujun';
