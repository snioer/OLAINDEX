<?php

namespace App\Http\Controllers;

use App\Helpers\Tool;
use Illuminate\Http\Request;

/**
 * 初始化安装操作
 * Class InstallController
 * @package App\Http\Controllers
 */
class InstallController extends Controller
{
    /**
     * 申请相关密钥
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function apply(Request $request)
    {
        // 感谢 donwa 提供的方法
        if (Tool::config('client_id') != '' || Tool::config('client_secret') != '' || Tool::config('redirect_uri') != '') {
            Tool::showMessage('已配置相关信息', false);
            return view('message');
        }
        $redirect_uri = $request->get('redirect_uri');
        if (!$redirect_uri) {
            Tool::showMessage('重定向地址缺失', false);
            return view('message');
        }
        $ru = "https://developer.microsoft.com/en-us/graph/quick-start?appID=_appId_&appName=_appName_&redirectUrl={$redirect_uri}&platform=option-php";
        $deepLink = "/quickstart/graphIO?publicClientSupport=false&appName=OLAINDEX&redirectUrl={$redirect_uri}&allowImplicitFlow=false&ru=" . urlencode($ru);
        $app_url = "https://apps.dev.microsoft.com/?deepLink=" . urlencode($deepLink);
        return redirect()->away($app_url);
    }

    /**
     * 首次安装
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function _1stInstall(Request $request)
    {
        // 检测是否已经配置client_id等信息
        $client_id = Tool::config('client_id');
        $client_secret = Tool::config('client_secret');
        $redirect_uri = Tool::config('redirect_uri');
        if ($client_id != '' && $client_secret != '' && $redirect_uri != '')
            return redirect()->route('bind');
        //  显示基础信息的填写、申请或提交应用信息、返回
        if ($request->isMethod('get')) return view('install.init');
        $client_id = $request->get('client_id');
        $client_secret = $request->get('client_secret');
        $redirect_uri = $request->get('redirect_uri');
        if ($client_id == '' || $client_secret == '' || $redirect_uri == '') {
            Tool::showMessage('参数请填写完整', false);
            return redirect()->back();
        }
        // 写入配置
        $data = [
            'client_id' => $client_id,
            'client_secret' => $client_secret,
            'redirect_uri' => $redirect_uri
        ];
        Tool::updateConfig($data);
        return redirect()->route('bind');
    }

    /**
     * 重置安装
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset()
    {
        if (Tool::config('access_token') != '' && Tool::config('refresh_token') != '' && Tool::config('access_token_expires') != '') {
            Tool::showMessage('您已绑定帐号，无法重置', false);
            return view('message');
        }
        $data = [
            'client_id' => '',
            'client_secret' => '',
            'redirect_uri' => ''
        ];
        Tool::updateConfig($data);
        return redirect()->route('_1stInstall');
    }

    /**
     * 绑定帐号
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bind(Request $request)
    {
        if (Tool::config('access_token') != '' && Tool::config('refresh_token') != '' && Tool::config('access_token_expires') != '') {
            Tool::showMessage('您已绑定帐号', false);
            return view('message');
        }
        if ($request->isMethod('post')) {
            if (Tool::config('access_token') != '' && Tool::config('refresh_token') != '' && Tool::config('access_token_expires') != '') {
                Tool::showMessage('您已绑定帐号，无法重置', false);
                return view('message');
            } else {
                return redirect()->route('oauth');
            }
        } else {
            return view('install.bind');
        }
    }
}
