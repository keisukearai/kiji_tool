<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Log;

/**
 * 記事ツールクラス
 */
class KijiToolController extends Controller
{
    // 四捨五入
    const ROUND = '1';
    // 切り捨て
    const TRUNCATE = '2';

    /**
     * 初期表示
     * @return object view形式
     */
    public function index() {
        Log::debug(__CLASS__ .' ' .__FUNCTION__);

        // 出力値に設定
        $outPut = [];
        $outPut['money'] = "";
        $outPut['reward'] = "";
        $outPut['rounding'] = "1";
        $outPut['calc_list'] = [];

        return view('kiji.index')->with($outPut);
    }

    /**
     * 計算ボタン押下処理
     * @param Request $request リクエスト情報
     * @return object view形式
     */
    public function calc(Request $request) {
        Log::debug(__CLASS__ .' ' .__FUNCTION__);

        // リクエスト情報の取得
        // 記事代金
        $money = $request->input('money');
        // 特別報酬
        $reward = $request->input('reward');
        // 端数処理
        $rounding = $request->input('rounding');

        // 四捨五入
        if ($rounding == self::ROUND) {
            // 記事代金計算
            $money_tax_8 = $this->calc_tax($money, 8, $rounding);
            $money_tax_10 = $this->calc_tax($money, 10, $rounding);
            // 特別報酬計算
            $reward_tax_8 = $this->calc_tax($reward, 8, $rounding);
            $reward_tax_10 = $this->calc_tax($reward, 10, $rounding);

        // 切り捨て
        } else if($rounding == self::TRUNCATE) {
            // 記事代金計算
            $money_tax_8 = $this->calc_tax($money, 8, $rounding);
            $money_tax_10 = $this->calc_tax($money, 10, $rounding);
            // 特別報酬計算
            $reward_tax_8 = $this->calc_tax($reward, 8, $rounding);
            $reward_tax_10 = $this->calc_tax($reward, 10, $rounding);
        }

        // 小計(8%) 【記事代 + 特別報酬 税抜き】
        $subtotal_8 = ($money - $money_tax_8) + ($reward - $reward_tax_8);
        // 源泉徴収算出(8%)
        $withholding_tax_8 = $this->calc_withholding_tax($subtotal_8);

        // 小計(10%) 【記事代 + 特別報酬 税抜き】
        $subtotal_10 = ($money - $money_tax_10) + ($reward - $reward_tax_10);
        // 源泉徴収算出(8%)
        $withholding_tax_10 = $this->calc_withholding_tax($subtotal_10);

        // 小計の消費税計算
        $subtotal_tax_8 = $this->calc_subtotal_tax($subtotal_8, 8);
        $subtotal_tax_10 = $this->calc_subtotal_tax($subtotal_10, 10);
        // dd($subtotal_10);
        // dd($subtotal_tax_10);

        // 一覧定義
        $calc_list = [];
        $obj = new \stdClass();
        $obj->tax_name = '8%';
        $obj->money_tax = $money_tax_8;
        $obj->reward_tax = $reward_tax_8;
        $obj->subtotal = $subtotal_8;
        $obj->subtotal_tax = $subtotal_tax_8;
        $obj->withholding_tax = $withholding_tax_8;
        // 一覧に追加
        $calc_list[] = $obj;

        $obj = new \stdClass();
        $obj->tax_name = '10%';
        $obj->money_tax = $money_tax_10;
        $obj->reward_tax = $reward_tax_10;
        $obj->subtotal = $subtotal_10;
        $obj->subtotal_tax = $subtotal_tax_10;
        $obj->withholding_tax = $withholding_tax_10;
        // 一覧に追加
        $calc_list[] = $obj;

        // 出力値に設定
        $outPut = [];
        $outPut['money'] = $money;
        $outPut['reward'] = $reward;
        $outPut['rounding'] = $rounding;
        $outPut['calc_list'] = $calc_list;

        return view('kiji.index')->with($outPut);
    }

    private function calc_tax($num, $tax, $round) {
        // 税込み金額消費税計算
        $tax = $num / (1 + $tax / 100) * $tax / 100;
        // 四捨五入
        if ($round == self::ROUND) {
            $rounded_tax = round($tax);
        // 切り捨て
        } else if ($round == self::TRUNCATE) {
            $rounded_tax = floor($tax);
        }
        return $rounded_tax;
    }

    private function calc_withholding_tax($num) {
        // 切り捨て
        return floor($num * 20.42 / 100);
    }

    private function calc_subtotal_tax($num, $tax) {
        // 切り捨て
        return floor($num * $tax / 100);
    }
}
