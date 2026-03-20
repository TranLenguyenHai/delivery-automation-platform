<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng Đăng Nhập Hệ Thống Logistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .role-card { transition: transform 0.3s; border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .role-card:hover { transform: translateY(-10px); }
        .icon-large { font-size: 4rem; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container text-center">
        <h1 class="fw-bold mb-3" style="color: #2c3e50;">CHÀO MỪNG ĐẾN VỚI HỆ THỐNG GIAO HÀNG AI</h1>
        <p class="text-muted mb-5 fs-5">Vui lòng chọn cổng đăng nhập phù hợp với vai trò của bạn</p>

        <div class="row justify-content-center gap-4">
            <div class="col-md-4">
                <div class="card role-card p-5 text-center">
                    <div class="icon-large">🏪</div>
                    <h3 class="fw-bold text-success">ĐỐI TÁC / CỬA HÀNG</h3>
                    <p class="text-muted">Đăng nhập để tạo đơn hàng mới và gửi yêu cầu giao hàng.</p>
                    <a href="{{ route('login') }}" class="btn btn-success btn-lg mt-3 fw-bold w-100">VÀO CỔNG CỬA HÀNG</a>
                    <a href="{{ route('register') }}" class="mt-2 text-decoration-none text-success">Hoặc đăng ký tài khoản Shop mới</a>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card role-card p-5 text-center">
                    <div class="icon-large">👨‍💻</div>
                    <h3 class="fw-bold text-primary">ADMIN ĐIỀU PHỐI</h3>
                    <p class="text-muted">Đăng nhập để xét duyệt, phân tích AI và điều phối tài xế.</p>
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg mt-3 fw-bold w-100">VÀO CỔNG ADMIN</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
