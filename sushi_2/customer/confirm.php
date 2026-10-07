<?php

declare(strict_types=1);

session_start();

// =============================================
// 1. KIỂM TRA NGÔN NGỮ
// =============================================

$supportedLanguages = [
    'ja',
    'en',
    'zh-CN',
    'zh-TW',
    'ko',
    'vi'
];

$lang = $_GET['lang'] ?? 'ja';

if (
    !is_string($lang) ||
    !in_array($lang, $supportedLanguages, true)
) {
    $lang = 'ja';
}

// =============================================
// 2. LẤY THÔNG TIN BÀN
// =============================================

$table = filter_input(INPUT_GET, 'table', FILTER_VALIDATE_INT);

if ($table === false || ($table !== null && $table < 1)) {
    $table = null;
}

if ($table !== null) {
    $_SESSION['table_id'] = $table;
}

$tableId = $_SESSION['table_id'] ?? null;

// =============================================
// 3. LẤY GIỎ HÀNG
// =============================================

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];

// =============================================
// 4. DỮ LIỆU MÓN ĂN TẠM THỜI
// Phải khớp ID và giá trong menu.php, cart.php.
// =============================================

$foods = [
    1 => [
        'name' => [
            'ja' => 'とんだえセット',
            'en' => 'Sushi Set',
            'zh-CN' => '寿司套餐',
            'zh-TW' => '壽司套餐',
            'ko' => '초밥 세트',
            'vi' => 'Set Sushi',
        ],
        'price' => 2343,
    ],
    2 => [
        'name' => [
            'ja' => 'えび',
            'en' => 'Shrimp Nigiri',
            'zh-CN' => '虾握寿司',
            'zh-TW' => '蝦握壽司',
            'ko' => '새우 초밥',
            'vi' => 'Sushi tôm',
        ],
        'price' => 407,
    ],
    3 => [
        'name' => [
            'ja' => 'たい',
            'en' => 'Sea Bream Nigiri',
            'zh-CN' => '鲷鱼握寿司',
            'zh-TW' => '鯛魚握壽司',
            'ko' => '도미 초밥',
            'vi' => 'Sushi cá tráp',
        ],
        'price' => 451,
    ],
    4 => [
        'name' => [
            'ja' => 'ちょっと一杯セット',
            'en' => 'Drinks & Sashimi Set',
            'zh-CN' => '小酌套餐',
            'zh-TW' => '小酌套餐',
            'ko' => '가볍게 한잔 세트',
            'vi' => 'Set nhâm nhi',
        ],
        'price' => 1969,
    ],
    5 => [
        'name' => [
            'ja' => 'まぐろづくし',
            'en' => 'Tuna Sushi Selection',
            'zh-CN' => '金枪鱼寿司拼盘',
            'zh-TW' => '鮪魚壽司拼盤',
            'ko' => '참치 모둠 초밥',
            'vi' => 'Sushi tổng hợp cá ngừ',
        ],
        'price' => 2376,
    ],
    6 => [
        'name' => [
            'ja' => '赤えび',
            'en' => 'Red Shrimp Nigiri',
            'zh-CN' => '赤虾握寿司',
            'zh-TW' => '赤蝦握壽司',
            'ko' => '붉은 새우 초밥',
            'vi' => 'Sushi tôm đỏ',
        ],
        'price' => 506,
    ],
    7 => [
        'name' => [
            'ja' => '焼きあなご',
            'en' => 'Grilled Conger Eel',
            'zh-CN' => '烤星鳗',
            'zh-TW' => '烤星鰻',
            'ko' => '구운 붕장어',
            'vi' => 'Lươn biển nướng',
        ],
        'price' => 451,
    ],
    8 => [
        'name' => [
            'ja' => '刺身5点盛り',
            'en' => 'Five-Kind Sashimi Platter',
            'zh-CN' => '五种刺身拼盘',
            'zh-TW' => '五種生魚片拼盤',
            'ko' => '모둠 사시미 5종',
            'vi' => 'Sashimi tổng hợp 5 loại',
        ],
        'price' => 2376,
    ],
];

// =============================================
// 5. BẢN DỊCH GIAO DIỆN
// =============================================

$texts = [
    'ja' => [
        'title' => 'ご注文内容の確認',
        'description' => 'ご注文内容をご確認ください。',
        'table' => 'テーブル番号',
        'item' => '商品',
        'price' => '単価',
        'quantity' => '数量',
        'subtotal' => '小計',
        'total' => '合計金額',
        'back' => 'カートに戻る',
        'submit' => '注文を確定する',
        'empty' => 'カートは空です。',
        'notice' => '注文確定後、スタッフに注文内容が送信されます。',
    ],
    'en' => [
        'title' => 'Review Your Order',
        'description' => 'Please check your order details.',
        'table' => 'Table Number',
        'item' => 'Item',
        'price' => 'Unit Price',
        'quantity' => 'Quantity',
        'subtotal' => 'Subtotal',
        'total' => 'Total',
        'back' => 'Back to Cart',
        'submit' => 'Place Order',
        'empty' => 'Your cart is empty.',
        'notice' => 'After confirmation, your order will be sent to staff.',
    ],
    'zh-CN' => [
        'title' => '确认订单内容',
        'description' => '请检查您的订单。',
        'table' => '桌号',
        'item' => '商品',
        'price' => '单价',
        'quantity' => '数量',
        'subtotal' => '小计',
        'total' => '总金额',
        'back' => '返回购物车',
        'submit' => '确认下单',
        'empty' => '购物车为空。',
        'notice' => '确认后，订单将发送给店员。',
    ],
    'zh-TW' => [
        'title' => '確認訂單內容',
        'description' => '請檢查您的訂單。',
        'table' => '桌號',
        'item' => '商品',
        'price' => '單價',
        'quantity' => '數量',
        'subtotal' => '小計',
        'total' => '總金額',
        'back' => '返回購物車',
        'submit' => '確認下單',
        'empty' => '購物車是空的。',
        'notice' => '確認後，訂單將傳送給店員。',
    ],
    'ko' => [
        'title' => '주문 내용 확인',
        'description' => '주문 내역을 확인해 주세요.',
        'table' => '테이블 번호',
        'item' => '상품',
        'price' => '단가',
        'quantity' => '수량',
        'subtotal' => '소계',
        'total' => '총금액',
        'back' => '장바구니로 돌아가기',
        'submit' => '주문 확정',
        'empty' => '장바구니가 비어 있습니다.',
        'notice' => '확인 후 주문이 직원에게 전달됩니다.',
    ],
    'vi' => [
        'title' => 'Xác nhận đơn hàng',
        'description' => 'Vui lòng kiểm tra lại đơn hàng.',
        'table' => 'Số bàn',
        'item' => 'Món ăn',
        'price' => 'Đơn giá',
        'quantity' => 'Số lượng',
        'subtotal' => 'Thành tiền',
        'total' => 'Tổng cộng',
        'back' => 'Quay lại giỏ hàng',
        'submit' => 'Xác nhận đặt món',
        'empty' => 'Giỏ hàng đang trống.',
        'notice' => 'Sau khi xác nhận, đơn hàng sẽ được gửi đến nhân viên.',
    ],
];

$t = $texts[$lang];

// =============================================
// 6. TÍNH LẠI TỔNG TIỀN
// Không nhận giá tiền từ trình duyệt.
// =============================================

$items = [];
$total = 0;

foreach ($cart as $foodId => $quantity) {
    $foodId = filter_var($foodId, FILTER_VALIDATE_INT);
    $quantity = filter_var($quantity, FILTER_VALIDATE_INT);

    if (
        $foodId === false ||
        $quantity === false ||
        !isset($foods[$foodId]) ||
        $quantity < 1 ||
        $quantity > 99
    ) {
        continue;
    }

    $food = $foods[$foodId];
    $subtotal = $food['price'] * $quantity;

    $items[] = [
        'id' => $foodId,
        'name' => $food['name'][$lang],
        'price' => $food['price'],
        'quantity' => $quantity,
        'subtotal' => $subtotal,
    ];

    $total += $subtotal;
}

// =============================================
// 7. TẠO URL CÓ GIỮ NGÔN NGỮ VÀ SỐ BÀN
// =============================================

$params = ['lang' => $lang];

if ($tableId !== null) {
    $params['table'] = (string) $tableId;
}

$cartUrl = 'cart.php?' . http_build_query($params);
$placeOrderUrl = 'place_order.php?' . http_build_query($params);

// Tạo token tạm thời cho form xác nhận.
// Sẽ được dùng để chống CSRF khi hoàn thiện Backend.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="theme-color" content="#b71920">

    <title>
        <?php echo htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8'); ?>
        | 寿司 博多魚がし
    </title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .confirm-main {
            width: min(100% - 32px, 900px);
            margin: 0 auto;
            padding: 35px 0 60px;
        }

        .confirm-title {
            font-family: serif;
            font-size: clamp(26px, 4vw, 36px);
        }

        .confirm-description {
            color: #756b60;
        }

        .confirm-panel {
            overflow: hidden;
            border: 1px solid #e6d9c7;
            border-radius: 12px;
            background: #fffdf8;
        }

        .confirm-table {
            width: 100%;
            border-collapse: collapse;
        }

        .confirm-table th,
        .confirm-table td {
            padding: 14px 12px;
            border-bottom: 1px solid #eee5d9;
            text-align: left;
        }

        .confirm-table th {
            background: #f4eee3;
            font-size: 13px;
        }

        .confirm-table td {
            font-size: 14px;
        }

        .confirm-price {
            white-space: nowrap;
        }

        .confirm-total {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 22px;
            font-size: 19px;
            font-weight: 700;
        }

        .confirm-total strong {
            color: #b71920;
            font-size: 26px;
        }

        .confirm-notice {
            margin: 0;
            padding: 0 22px 18px;
            color: #756b60;
            font-size: 13px;
        }

        .confirm-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            padding: 0 22px 22px;
        }

        .confirm-button {
            flex: 1;
            min-width: 180px;
            padding: 14px 18px;
            border: 1px solid #e6d9c7;
            border-radius: 8px;
            background: white;
            text-align: center;
            font: inherit;
            cursor: pointer;
        }

        .confirm-button.primary {
            border-color: #b71920;
            color: white;
            background: #b71920;
        }

        .confirm-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .confirm-empty {
            padding: 35px 20px;
            text-align: center;
        }

        .table-information {
            display: inline-block;
            margin: 8px 0 20px;
            padding: 8px 14px;
            border: 1px solid #e6d9c7;
            border-radius: 30px;
            background: #fffdf8;
        }

        @media (max-width: 520px) {
            .confirm-main {
                width: calc(100% - 20px);
                padding-top: 22px;
            }

            .confirm-table th,
            .confirm-table td {
                padding: 10px 6px;
                font-size: 12px;
            }

            .confirm-total {
                padding: 16px;
                font-size: 16px;
            }

            .confirm-total strong {
                font-size: 21px;
            }

            .confirm-actions {
                padding: 0 16px 16px;
            }
        }
    </style>
</head>

<body>

    <header class="site-header">

        <a href="../index.php" class="brand">
            <span class="brand-mark">魚</span>

            <span class="brand-text">
                <span class="brand-name">寿司 博多魚がし</span>
                <span class="brand-subtitle">HAKATA UOGASHI SUSHI</span>
            </span>
        </a>

        <?php if ($tableId !== null): ?>
            <div class="table-badge">
                <?php echo htmlspecialchars($t['table'], ENT_QUOTES, 'UTF-8'); ?>:
                <?php echo htmlspecialchars((string) $tableId, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

    </header>

    <main class="confirm-main">

        <h1 class="confirm-title">
            <?php echo htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8'); ?>
        </h1>

        <p class="confirm-description">
            <?php echo htmlspecialchars($t['description'], ENT_QUOTES, 'UTF-8'); ?>
        </p>

        <?php if ($tableId !== null): ?>
            <p class="table-information">
                <?php echo htmlspecialchars($t['table'], ENT_QUOTES, 'UTF-8'); ?>:
                <?php echo htmlspecialchars((string) $tableId, ENT_QUOTES, 'UTF-8'); ?>
            </p>
        <?php endif; ?>

        <?php if (count($items) === 0): ?>

            <section class="confirm-panel confirm-empty">

                <p>
                    <?php echo htmlspecialchars($t['empty'], ENT_QUOTES, 'UTF-8'); ?>
                </p>

                <a
                    class="confirm-button primary"
                    href="<?php echo htmlspecialchars($cartUrl, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($t['back'], ENT_QUOTES, 'UTF-8'); ?>
                </a>

            </section>

        <?php else: ?>

            <section class="confirm-panel">

                <div style="overflow-x:auto;">

                    <table class="confirm-table">

                        <thead>
                            <tr>
                                <th><?php echo htmlspecialchars($t['item'], ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($t['price'], ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($t['quantity'], ENT_QUOTES, 'UTF-8'); ?></th>
                                <th><?php echo htmlspecialchars($t['subtotal'], ENT_QUOTES, 'UTF-8'); ?></th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($items as $item): ?>

                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>

                                    <td class="confirm-price">
                                        ¥<?php echo number_format($item['price']); ?>
                                    </td>

                                    <td>
                                        <?php echo $item['quantity']; ?>
                                    </td>

                                    <td class="confirm-price">
                                        ¥<?php echo number_format($item['subtotal']); ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

                <div class="confirm-total">

                    <span>
                        <?php echo htmlspecialchars($t['total'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>

                    <strong>
                        ¥<?php echo number_format($total); ?>
                    </strong>

                </div>

                <p class="confirm-notice">
                    <?php echo htmlspecialchars($t['notice'], ENT_QUOTES, 'UTF-8'); ?>
                </p>

                <div class="confirm-actions">

                    <a
                        class="confirm-button"
                        href="<?php echo htmlspecialchars($cartUrl, ENT_QUOTES, 'UTF-8'); ?>">
                        ← <?php echo htmlspecialchars($t['back'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>

                    <form
                        method="POST"
                        action="<?php echo htmlspecialchars($placeOrderUrl, ENT_QUOTES, 'UTF-8'); ?>"
                        style="flex:1; min-width:180px;">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">

                        <button
                            type="submit"
                            class="confirm-button primary"
                            style="width:100%;">
                            <?php echo htmlspecialchars($t['submit'], ENT_QUOTES, 'UTF-8'); ?>
                            →
                        </button>

                    </form>

                </div>

            </section>

        <?php endif; ?>

    </main>

</body>

</html>