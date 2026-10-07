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

$lang = $_GET['lang'] ?? $_POST['lang'] ?? 'ja';

if (
    !is_string($lang) ||
    !in_array($lang, $supportedLanguages, true)
) {
    $lang = 'ja';
}

// =============================================
// 2. LẤY SỐ BÀN
// =============================================

$table = filter_input(INPUT_GET, 'table', FILTER_VALIDATE_INT);

if ($table === null && isset($_POST['table'])) {
    $postedTable = filter_var(
        $_POST['table'],
        FILTER_VALIDATE_INT
    );

    $table = $postedTable === false ? null : $postedTable;
}

if ($table === false || ($table !== null && $table < 1)) {
    $table = null;
}

if ($table !== null) {
    $_SESSION['table_id'] = $table;
}

$tableId = $_SESSION['table_id'] ?? null;

// =============================================
// 3. KHỞI TẠO GIỎ HÀNG
// =============================================

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = &$_SESSION['cart'];

// =============================================
// 4. DANH SÁCH MÓN VÀ GIÁ
// Tạm thời dùng dữ liệu mẫu.
// Sau này lấy từ database MySQL.
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
        'image' => 'set-sushi.jpg',
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
        'image' => 'ebi.jpg',
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
        'image' => 'tai.jpg',
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
        'image' => 'ippai-set.jpg',
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
        'image' => 'maguro.jpg',
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
        'image' => 'aka-ebi.jpg',
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
        'image' => 'anago.jpg',
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
        'image' => 'sashimi-5.jpg',
    ],
];

// =============================================
// 5. BẢN DỊCH
// =============================================

$texts = [
    'ja' => [
        'title' => 'ショッピングカート',
        'empty' => 'カートは空です。',
        'back' => 'メニューに戻る',
        'update' => '数量を更新',
        'delete' => '削除',
        'subtotal' => '小計',
        'total' => '合計金額',
        'checkout' => '注文内容を確認',
        'price' => '単価',
        'quantity' => '数量',
        'amount' => '金額',
        'tax' => '税込価格',
    ],
    'en' => [
        'title' => 'Your Cart',
        'empty' => 'Your cart is empty.',
        'back' => 'Back to Menu',
        'update' => 'Update Quantity',
        'delete' => 'Remove',
        'subtotal' => 'Subtotal',
        'total' => 'Total',
        'checkout' => 'Review Order',
        'price' => 'Unit Price',
        'quantity' => 'Quantity',
        'amount' => 'Amount',
        'tax' => 'Tax included',
    ],
    'zh-CN' => [
        'title' => '购物车',
        'empty' => '购物车为空。',
        'back' => '返回菜单',
        'update' => '更新数量',
        'delete' => '删除',
        'subtotal' => '小计',
        'total' => '总金额',
        'checkout' => '确认订单内容',
        'price' => '单价',
        'quantity' => '数量',
        'amount' => '金额',
        'tax' => '含税价格',
    ],
    'zh-TW' => [
        'title' => '購物車',
        'empty' => '購物車是空的。',
        'back' => '返回菜單',
        'update' => '更新數量',
        'delete' => '刪除',
        'subtotal' => '小計',
        'total' => '總金額',
        'checkout' => '確認訂單內容',
        'price' => '單價',
        'quantity' => '數量',
        'amount' => '金額',
        'tax' => '含稅價格',
    ],
    'ko' => [
        'title' => '장바구니',
        'empty' => '장바구니가 비어 있습니다.',
        'back' => '메뉴로 돌아가기',
        'update' => '수량 변경',
        'delete' => '삭제',
        'subtotal' => '소계',
        'total' => '총금액',
        'checkout' => '주문 내용 확인',
        'price' => '단가',
        'quantity' => '수량',
        'amount' => '금액',
        'tax' => '세금 포함',
    ],
    'vi' => [
        'title' => 'Giỏ hàng',
        'empty' => 'Giỏ hàng của bạn đang trống.',
        'back' => 'Quay lại menu',
        'update' => 'Cập nhật số lượng',
        'delete' => 'Xóa',
        'subtotal' => 'Thành tiền',
        'total' => 'Tổng cộng',
        'checkout' => 'Xác nhận đơn hàng',
        'price' => 'Đơn giá',
        'quantity' => 'Số lượng',
        'amount' => 'Thành tiền',
        'tax' => 'Đã gồm thuế',
    ],
];

$t = $texts[$lang];

// =============================================
// 6. XỬ LÝ CẬP NHẬT HOẶC XÓA MÓN
// =============================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    $foodId = filter_input(
        INPUT_POST,
        'food_id',
        FILTER_VALIDATE_INT
    );

    if (
        is_string($action) &&
        $foodId !== false &&
        $foodId !== null &&
        isset($foods[$foodId])
    ) {
        if ($action === 'delete') {
            unset($cart[$foodId]);
        } elseif ($action === 'update') {
            $quantity = filter_input(
                INPUT_POST,
                'quantity',
                FILTER_VALIDATE_INT
            );

            if (
                $quantity !== false &&
                $quantity !== null &&
                $quantity >= 1 &&
                $quantity <= 99
            ) {
                $cart[$foodId] = $quantity;
            }
        }
    }

    // Chuyển hướng sau khi cập nhật giỏ hàng.
    $params = ['lang' => $lang];

    if ($tableId !== null) {
        $params['table'] = (string) $tableId;
    }

    header('Location: cart.php?' . http_build_query($params));
    exit;
}

// =============================================
// 7. TÍNH TỔNG TIỀN Ở SERVER
// =============================================

$total = 0;
$cartItems = [];

foreach ($cart as $foodId => $quantity) {
    $foodId = filter_var($foodId, FILTER_VALIDATE_INT);
    $quantity = filter_var($quantity, FILTER_VALIDATE_INT);

    // Bỏ qua dữ liệu giỏ hàng không hợp lệ.
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

    $cartItems[] = [
        'id' => $foodId,
        'food' => $food,
        'quantity' => $quantity,
        'subtotal' => $subtotal,
    ];

    $total += $subtotal;
}

// Tạo các URL có giữ ngôn ngữ và số bàn.
$params = ['lang' => $lang];

if ($tableId !== null) {
    $params['table'] = (string) $tableId;
}

$menuUrl = 'menu.php?' . http_build_query($params);
$confirmUrl = 'confirm.php?' . http_build_query($params);
?>

<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8'); ?>
        | 寿司 博多魚がし
    </title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .cart-main {
            width: min(100% - 32px, 900px);
            margin: 0 auto;
            padding: 35px 0 60px;
        }

        .cart-title {
            font-family: serif;
            font-size: clamp(26px, 4vw, 36px);
        }

        .cart-panel {
            overflow: hidden;
            border: 1px solid #e6d9c7;
            border-radius: 12px;
            background: #fffdf8;
        }

        .cart-item {
            display: grid;
            grid-template-columns: 90px minmax(0, 1fr);
            gap: 15px;
            padding: 18px;
            border-bottom: 1px solid #eee5d9;
        }

        .cart-item-image {
            width: 90px;
            height: 90px;
            object-fit: cover;
            border-radius: 8px;
            background: #f0e9de;
        }

        .cart-item-name {
            margin: 0 0 5px;
            font-size: 17px;
        }

        .cart-item-price {
            color: #756b60;
            font-size: 13px;
        }

        .cart-item-subtotal {
            color: #b71920;
            font-weight: 700;
        }

        .cart-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .cart-actions input {
            width: 70px;
            padding: 7px;
            border: 1px solid #e6d9c7;
            border-radius: 6px;
        }

        .cart-actions button {
            padding: 7px 12px;
            border: 1px solid #b71920;
            border-radius: 6px;
            color: #b71920;
            background: white;
            cursor: pointer;
        }

        .cart-actions .delete-button {
            border-color: #ddd;
            color: #756b60;
        }

        .cart-summary {
            padding: 22px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            font-size: 18px;
            font-weight: 700;
        }

        .total-price {
            color: #b71920;
            font-size: 25px;
        }

        .cart-footer-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
        }

        .cart-link-button {
            flex: 1;
            min-width: 180px;
            padding: 13px 18px;
            border: 1px solid #e6d9c7;
            border-radius: 8px;
            text-align: center;
            background: white;
        }

        .cart-link-button.primary {
            border-color: #b71920;
            color: white;
            background: #b71920;
        }

        .cart-empty {
            padding: 45px 20px;
            text-align: center;
        }

        @media (max-width: 480px) {
            .cart-item {
                grid-template-columns: 68px minmax(0, 1fr);
                gap: 10px;
                padding: 12px;
            }

            .cart-item-image {
                width: 68px;
                height: 68px;
            }

            .cart-item-name {
                font-size: 14px;
            }

            .cart-summary {
                padding: 15px;
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

        <div class="table-badge">
            <?php if ($tableId !== null): ?>
                テーブル
                <?php echo htmlspecialchars((string) $tableId, ENT_QUOTES, 'UTF-8'); ?>
            <?php endif; ?>
        </div>
    </header>

    <main class="cart-main">

        <h1 class="cart-title">
            <?php echo htmlspecialchars($t['title'], ENT_QUOTES, 'UTF-8'); ?>
        </h1>

        <?php if (count($cartItems) === 0): ?>

            <section class="cart-panel cart-empty">

                <p>
                    <?php echo htmlspecialchars($t['empty'], ENT_QUOTES, 'UTF-8'); ?>
                </p>

                <a class="cart-link-button primary"
                    href="<?php echo htmlspecialchars($menuUrl, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php echo htmlspecialchars($t['back'], ENT_QUOTES, 'UTF-8'); ?>
                </a>

            </section>

        <?php else: ?>

            <section class="cart-panel">

                <?php foreach ($cartItems as $item): ?>

                    <article class="cart-item">

                        <img
                            class="cart-item-image"
                            src="../assets/images/<?php echo rawurlencode($item['food']['image']); ?>"
                            alt="">

                        <div>

                            <h2 class="cart-item-name">
                                <?php
                                echo htmlspecialchars(
                                    $item['food']['name'][$lang],
                                    ENT_QUOTES,
                                    'UTF-8'
                                );
                                ?>
                            </h2>

                            <div class="cart-item-price">
                                <?php echo htmlspecialchars($t['price'], ENT_QUOTES, 'UTF-8'); ?>:
                                ¥<?php echo number_format($item['food']['price']); ?>
                            </div>

                            <div class="cart-item-subtotal">
                                <?php echo htmlspecialchars($t['amount'], ENT_QUOTES, 'UTF-8'); ?>:
                                ¥<?php echo number_format($item['subtotal']); ?>
                            </div>

                            <div class="cart-actions">

                                <form method="POST" class="cart-actions">

                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="food_id"
                                        value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="lang"
                                        value="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">

                                    <?php if ($tableId !== null): ?>
                                        <input type="hidden" name="table"
                                            value="<?php echo $tableId; ?>">
                                    <?php endif; ?>

                                    <input
                                        type="number"
                                        name="quantity"
                                        min="1"
                                        max="99"
                                        required
                                        value="<?php echo $item['quantity']; ?>"
                                        aria-label="<?php echo htmlspecialchars($t['quantity'], ENT_QUOTES, 'UTF-8'); ?>">

                                    <button type="submit">
                                        <?php echo htmlspecialchars($t['update'], ENT_QUOTES, 'UTF-8'); ?>
                                    </button>

                                </form>

                                <form method="POST">

                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="food_id"
                                        value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="lang"
                                        value="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>">

                                    <?php if ($tableId !== null): ?>
                                        <input type="hidden" name="table"
                                            value="<?php echo $tableId; ?>">
                                    <?php endif; ?>

                                    <button type="submit" class="delete-button">
                                        <?php echo htmlspecialchars($t['delete'], ENT_QUOTES, 'UTF-8'); ?>
                                    </button>

                                </form>

                            </div>

                        </div>

                    </article>

                <?php endforeach; ?>

                <div class="cart-summary">

                    <div class="total-row">
                        <span>
                            <?php echo htmlspecialchars($t['total'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>

                        <span class="total-price">
                            ¥<?php echo number_format($total); ?>
                        </span>
                    </div>

                    <p class="cart-item-price">
                        <?php echo htmlspecialchars($t['tax'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>

                    <div class="cart-footer-actions">

                        <a
                            class="cart-link-button"
                            href="<?php echo htmlspecialchars($menuUrl, ENT_QUOTES, 'UTF-8'); ?>">
                            ← <?php echo htmlspecialchars($t['back'], ENT_QUOTES, 'UTF-8'); ?>
                        </a>

                        <a
                            class="cart-link-button primary"
                            href="<?php echo htmlspecialchars($confirmUrl, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($t['checkout'], ENT_QUOTES, 'UTF-8'); ?>
                            →
                        </a>

                    </div>

                </div>

            </section>

        <?php endif; ?>

    </main>

</body>

</html>