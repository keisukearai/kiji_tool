<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>記事ツール</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>
    <body>
        <div class="px-3 pt-3">
            <h3>記事ツール</h3>
        </div>
        <div class="px-3 py-2">
            <form method="post" action="kiji_calc">
                @csrf
                <div class="row">
                    <div class="col-2">
                        <input type="number" name="money" class="form-control" value="{{ $money }}" placeholder="記事代" required />
                    </div>
                    <div class="col-2">
                        <input type="number" name="reward" class="form-control" value="{{ $reward }}" placeholder="特別報酬" required />
                    </div>
                    <div class="col-2">
                        <select name="rounding" class="form-select">
                            <option value="1" @if($rounding == '1') selected @endif>四捨五入</option>
                            <option value="2" @if($rounding == '2') selected @endif>切り捨て</option>
                        </select>
                    </div>
                </div>
                <div class="py-3">
                    <input type="submit" class="btn btn-primary" value="計算">
                    <a href="kiji" class="btn btn-secondary">リセット</a>
                </div>
            </form>
        </div>

        {{-- 計算結果がある場合のみ表示 --}}
        @if (count($calc_list) > 0)
        <div class="px-3">
            <table class="table table-bordered" style="width:80%;">
                <thead class="table-light">
                    <tr>
                        <th colspan="2">税率</th>
                        <th>税抜き</th>
                        <th>消費税</th>
                        <th>税込み</th>
                        <th>小計</th>
                        <th>小計の消費税</th>
                        <th>源泉税</th>
                        <th>合計金額</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($calc_list as $data)
                    @php
                        // 合計 = 小計 - 源泉徴収 + 小計の消費税
                        $total = $data->subtotal - $data->withholding_tax + $data->subtotal_tax;
                    @endphp
                    <tr>
                        <td rowspan="2" class="text-center">{{ $data->tax_name }}</td>
                        <td>記事代</td>
                        {{-- 税抜き --}}
                        <td class="text-end">{{ number_format($money - $data->money_tax) }}</td>
                        {{-- 消費税 --}}
                        <td class="text-end">{{ number_format($data->money_tax) }}</td>
                        {{-- 税込み --}}
                        <td class="text-end">{{ number_format($money) }}</td>
                        {{-- 小計 --}}
                        <td rowspan="2" class="text-end">{{ number_format($data->subtotal) }}</td>
                        {{-- 小計の消費税 --}}
                        <td rowspan="2" class="text-end">{{ number_format($data->subtotal_tax) }}</td>
                        {{-- 源泉税 --}}
                        <td rowspan="2" class="text-end">-{{ number_format($data->withholding_tax) }}</td>
                        {{-- 合計金額 --}}
                        <td rowspan="2" class="text-end">{{ number_format($total) }}</td>
                    </tr>
                    <tr>
                        <td>特別報酬</td>
                        {{-- 税抜き --}}
                        <td class="text-end">{{ number_format($reward - $data->reward_tax) }}</td>
                        {{-- 消費税 --}}
                        <td class="text-end">{{ number_format($data->reward_tax) }}</td>
                        {{-- 税込み --}}
                        <td class="text-end">{{ number_format($reward) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    </body>
</html>