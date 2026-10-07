<?php
session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Kiểm tra kết nối database
if (!isset($pdo) || !$pdo instanceof PDO) {
    http_response_code(500);
    exit('Database connection is not configured.');
}

// Escape dữ liệu trước khi đưa vào HTML
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

try {
    // Tổng số đơn hàng
    $totalOrders = (int) $pdo->query(
        'SELECT COUNT(*) FROM orders'
    )->fetchColumn();

    // Số đơn đang chờ xử lý
    $pendingOrders = (int) $pdo->query(
        "SELECT COUNT(*) FROM orders WHERE status = 'pending'"
    )->fetchColumn();

    // Số đơn đang chế biến
    $preparingOrders = (int) $pdo->query(
        "SELECT COUNT(*) FROM orders WHERE status = 'preparing'"
    )->fetchColumn();

    // Số đơn đã hoàn thành
    $completedOrders = (int) $pdo->query(
        "SELECT COUNT(*) FROM orders WHERE status = 'completed'"
    )->fetchColumn();

    // Doanh thu của các đơn đã hoàn thành trong ngày hôm nay
    $stmt = $pdo->query(
        "SELECT COALESCE(SUM(total_amount), 0)
         FROM orders
         WHERE status = 'completed'
           AND created_at >= CURDATE()
           AND created_at < CURDATE() + INTERVAL 1 DAY"
    );

    $todayRevenue = (int) $stmt->fetchColumn();

    // Số đơn được tạo hôm nay
    $stmt = $pdo->query(
        "SELECT COUNT(*)
         FROM orders
         WHERE created_at >= CURDATE()
           AND created_at < CURDATE() + INTERVAL 1 DAY"
    );

    $todayOrders = (int) $stmt->fetchColumn();

    // 10 đơn hàng mới nhất
    $stmt = $pdo->query(
        'SELECT id, table_number, total_amount, status, created_at
         FROM orders
         ORDER BY created_at DESC, id DESC
         LIMIT 10'
    );

    $recentOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Dashboard query error: ' . $e->getMessage());

    http_response_code(500);
    exit('Không thể tải dữ liệu dashboard. Vui lòng thử lại sau.');
}

// Tên trạng thái bằng tiếng Việt
$statusLabels = [
    'pending' => 'Đang chờ',
    'preparing' => 'Đang chế biến',
    'completed' => 'Hoàn thành',
    'cancelled' => 'Đã hủy',
];

// CSS class tương ứng với trạng thái
$statusClasses = [
    'pending' => 'status-pending',
    'preparing' => 'status-preparing',
    'completed' => 'status-completed',
    'cancelled' => 'status-cancelled',
];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | 寿司 博多魚がし</title>

    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        .dashboard {
            width: min(100% - 32px, 1200px);
            margin: 28px auto;
        }

        .dashboard-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
        }

        .dashboard-heading h1 {
            margin: 0 0 6px;
        }

        .dashboard-heading p {
            margin: 0;
            color: #777;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            padding: 22px;
            background: white;
            border: 1px solid #e5e1d9;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
        }

        .stat-label {
            color: #777;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 30px;
            font-weight: bold;
            line-height: 1.3;
        }

        .stat-note {
            margin-top: 8px;
            color: #777;
            font-size: 12px;
        }

        .dashboard-section {
            margin-bottom: 28px;
            padding: 22px;
            background: white;
            border: 1px solid #e5e1d9;
            border-radius: 12px;
        }

        .dashboard-section h2 {
            margin-top: 0;
            font-size: 21px;
        }

        .dashboard-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .dashboard-nav a {
            flex: 1 1 180px;
            padding: 18px;
            border: 1px solid #e5e1d9;
            border-radius: 10px;
            color: #292929;
            background: #fff;
        }

        .dashboard-nav a:hover {
            background: #fff0f0;
            border-color: #a51e22;
            text-decoration: none;
        }

        .dashboard-nav strong {
            display: block;
            margin-bottom: 5px;
        }

        .dashboard-nav span {
            font-size: 13px;
            color: #777;
        }

        .order-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 16px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending {
            color: #804b00;
            background: #fff0d9;
        }

        .status-preparing {
            color: #075985;
            background: #e0f2fe;
        }

        .status-completed {
            color: #166534;
            background: #dcfce7;
        }

        .status-cancelled {
            color: #991b1b;
            background: #fee2e2;
        }

        @media (max-width: 800px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .stat-card {
                padding: 18px;
            }

            .stat-value {
                font-size: 26px;
            }

            .dashboard-section {
                padding: 14px;
            }
        }
    </style>
</head>

<body>

    <header class="admin-header">
        <div class="container">
            <strong>寿司 博多魚がし — 管理画面</strong>

            <nav class="site-nav">
                <a href="dashboard.php">Dashboard</a>
                <a href="orders.php">Đơn hàng</a>
                <a href="foods.php">Món ăn</a>
                <a href="categories.php">Danh mục</a>
                <a href="login.php">Đăng nhập</a>
            </nav>
        </div>
    </header>

    <main class="dashboard">

        <section class="dashboard-heading">
            <div>
                <h1>Dashboard</h1>
                <p>Tổng quan hoạt động của nhà hàng.</p>
            </div>

            <div>
                <span class="text-muted">
                    Ngày:
                    <?= e(date('d/m/Y')) ?>
                </span>
            </div>
        </section>

        <!-- Các thẻ thống kê -->
        <section class="stats-grid">

            <div class="stat-card">
                <div class="stat-label">Tổng số đơn hàng</div>
                <div class="stat-value">
                    <?= number_format($totalOrders) ?>
                </div>
                <div class="stat-note">Tất cả đơn hàng trong hệ thống</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Đơn hàng hôm nay</div>
                <div class="stat-value">
                    <?= number_format($todayOrders) ?>
                </div>
                <div class="stat-note">Đơn được tạo trong ngày</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Đang chờ xử lý</div>
                <div class="stat-value">
                    <?= number_format($pendingOrders) ?>
                </div>
                <div class="stat-note">Cần nhân viên kiểm tra</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Đang chế biến</div>
                <div class="stat-value">
                    <?= number_format($preparingOrders) ?>
                </div>
                <div class="stat-note">Đơn đang được chuẩn bị</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Đã hoàn thành</div>
                <div class="stat-value">
                    <?= number_format($completedOrders) ?>
                </div>
                <div class="stat-note">Tổng số đơn hoàn thành</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Doanh thu hôm nay</div>
                <div class="stat-value">
                    <?= number_format($todayRevenue) ?>円
                </div>
                <div class="stat-note">
                    Chỉ tính đơn hoàn thành, theo ngày tạo đơn
                </div>
            </div>

        </section>

        <!-- Lối tắt đến các trang quản trị -->
        <section class="dashboard-section">
            <h2>Quản lý hệ thống</h2>

            <div class="dashboard-nav">
                <a href="orders.php">
                    <strong>Quản lý đơn hàng</strong>
                    <span>Xem đơn mới và cập nhật trạng thái.</span>
                </a>

                <a href="foods.php">
                    <strong>Quản lý món ăn</strong>
                    <span>Thêm món, sửa giá và hình ảnh.</span>
                </a>

                <a href="categories.php">
                    <strong>Quản lý danh mục</strong>
                    <span>Tạo và chỉnh sửa danh mục món.</span>
                </a>
            </div>
        </section>

        <!-- Đơn hàng mới nhất -->
        <section class="dashboard-section">
            <h2>10 đơn hàng mới nhất</h2>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Bàn</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($recentOrders)): ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    Chưa có đơn hàng nào.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentOrders as $order): ?>
                                <?php
                                $orderStatus = $order['status'];
                                $statusLabel = $statusLabels[$orderStatus]
                                    ?? 'Không xác định';
                                $statusClass = $statusClasses[$orderStatus] ?? '';
                                ?>

                                <tr>
                                    <td>
                                        #<?= (int) $order['id'] ?>
                                    </td>

                                    <td>
                                        <?= $order['table_number'] !== null
                                            ? e($order['table_number'])
                                            : '—' ?>
                                    </td>

                                    <td>
                                        <?= number_format((int) $order['total_amount']) ?>円
                                    </td>

                                    <td>
                                        <span class="order-status <?= e($statusClass) ?>">
                                            <?= e($statusLabel) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= e(date(
                                            'd/m/Y H:i',
                                            strtotime($order['created_at'])
                                        )) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="button-group">
                <a href="orders.php" class="btn btn-primary">
                    Xem tất cả đơn hàng
                </a>
            </div>
        </section>

    </main>

    <footer class="site-footer">
        © 寿司 博多魚がし — Hệ thống quản lý nhà hàng
    </footer>

</body>

</html>