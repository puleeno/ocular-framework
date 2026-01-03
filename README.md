# 👁️ Ocular Framework

**Beyond the Monolith: Witness the Speed of Light.**

Ocular là một PHP Framework hiệu năng cao, được thiết kế để tái cấu trúc lớp dữ liệu của WordPress thành một kiến trúc hiện đại dựa trên Middleware. Bằng cách tuân thủ các tiêu chuẩn PSR-7 và PSR-15, Ocular biến WordPress từ một CMS cồng kềnh thành một Microservice mạnh mẽ, có khả năng phản hồi trong tích tắc (sub-millisecond).

---

## 🚀 Mục tiêu chiến lược

* **Phẳng hóa dữ liệu:** Loại bỏ nút thắt cổ chai của mô hình EAV (`wp_postmeta`) thông qua cơ chế Mapping vào các bảng phẳng (Flat Tables).
* **Bypass Bootstrapping:** Chỉ khởi tạo những thành phần thiết yếu của nhân WordPress thông qua `SHORTINIT`.
* **Stateful Execution:** Tối ưu hóa cho **RoadRunner**, giúp giữ ứng dụng thường trực trong RAM và loại bỏ chi phí khởi động I/O cho mỗi request.
* **Hiện đại hóa:** Thay thế tư duy lập trình thủ tục (procedural) bằng PSR-7 (HTTP Messages) và PSR-15 (Middleware).

---

## 🏗️ Kiến trúc hệ thống

Ocular hoạt động như một lớp Proxy thông minh nằm trước WordPress:

1. **Entry Point (RoadRunner):** Nhận Request và đẩy vào Worker PHP.
2. **Kernel (Ocular):** Tiếp nhận PSR-7 ServerRequest.
3. **Middleware Stack (PSR-15):** * `AuthMiddleware`: Xác thực nhanh qua JWT/Cache.
* `BridgeMiddleware`: Đồng bộ dữ liệu PSR-7 vào các siêu biến toàn cục (`$_GET`, `$_POST`).
* `WpLauncher`: Kích hoạt nhân WordPress có chọn lọc.
* `ResetterMiddleware`: Dọn dẹp bộ nhớ và biến toàn cục sau mỗi vòng lặp Worker.


4. **Data Layer:** Truy vấn dữ liệu từ các bảng phẳng đã được tối ưu thay vì `wp_postmeta`.

---

## 📁 Cấu trúc thư mục

```text
ocular-project/
├── app/                        # Nơi chứa logic ứng dụng của bạn
│   ├── Http/
│   │   ├── Controllers/        # Xử lý logic API hoặc Web custom
│   │   └── Middlewares/        # Các bộ lọc PSR-15 (Auth, Cache, v.v.)
│   ├── Models/                 # Định nghĩa Schema cho Flat-tables (Ocular Mapping)
│   └── Providers/              # Đăng ký Service (Database, Redis, Mailer)
│
├── bin/                        # Các tệp thực thi hệ thống
│   ├── rr                      # RoadRunner binary
│   └── console                 # CLI tool cho Ocular (Migration, Cache clear)
│
├── config/                     # Cấu hình Framework
│   ├── app.php                 # Cấu hình chung
│   ├── database.php            # Cấu hình DB cho Flat-tables
│   └── routes.php              # Định nghĩa Route cho FastRoute
│
├── core/                       # NHÂN WORDPRESS (WP-CORE)
│   ├── wp-admin/               # Giữ nguyên để quản trị
│   ├── wp-includes/            # Giữ nguyên để tận dụng thư viện
│   └── index.php               # Fallback cho các request truyền thống
│
├── content/                    # Tương đương với wp-content
│   ├── plugins/                # Nơi chứa các plugin bên thứ 3 (WooCommerce, v.v.)
│   ├── themes/                 # Nơi chứa các theme truyền thống
│   └── mu-plugins/             # Các plugin bắt buộc để "lừa" WP core
│
├── public/                     # Thư mục công khai (Web root)
│   ├── index.php               # ENTRY POINT DỰ PHÒNG (Cho Apache/Nginx truyền thống)
│   └── wp-config.php           # File cấu hình WordPress trung tâm
│
├── storage/                    # Logs, Cache, Uploads
├── vendor/                     # Composer dependencies
│
├── .rr.yaml                    # Cấu hình server RoadRunner
├── worker.php                  # ENTRY POINT CHO ROADRUNNER (PSR-7/15 Worker)
└── composer.json               # Quản lý thư viện

```

---

## ⚡ Cài đặt nhanh

### 1. Yêu cầu hệ thống

* PHP 8.1+ (Thread Safe khuyến khích cho RoadRunner).
* Composer.
* RoadRunner Server binary.

### 2. Khởi tạo dự án

```bash
composer create-project ocular/framework my-app
cd my-app
```

### 3. Cài đặt WordPress Core (WP-CLI)

Ocular tách biệt nhân WordPress (Core) vào một thư mục riêng để dễ dàng quản lý và tối ưu hóa. Sử dụng lệnh sau để tải WordPress Core vào thư mục `core/` mà không kèm theo các theme và plugin mặc định:

```bash
wp core download --skip-content --path=core
```

### 4. Cách thức hoạt động

Khi đã có thư mục `core/`, Ocular sẽ:
1.  **Isolated Core:** Giữ nhân WordPress sạch sẽ, không bị lẫn lộn dữ liệu framework.
2.  **Explicit Bootstrapping:** Thông qua `Kernel->bootstrap()`, Ocular nạp WordPress chỉ khi cần thiết.
3.  **Modern Content Path:** Các plugin và theme tùy chỉnh của bạn sẽ nằm trong thư mục `content/` (tương đương `wp-content`), giúp cấu trúc dự án chuẩn PSR-4 hơn.

### 5. Cấu hình Worker (`worker.php`)

```php
use Ocular\Kernel;
use Spiral\RoadRunner\Http\PSR7Worker;

$kernel = new Kernel();
$kernel->bootstrap(); // Boot WordPress một lần duy nhất

$psr7Worker = new PSR7Worker(/* factories */);

while ($request = $psr7Worker->waitRequest()) {
    $response = $kernel->handle($request);
    $psr7Worker->respond($response);
}

```

---

## 🛠️ Triết lý xử lý dữ liệu (The Eyewink Logic)

Ocular tích hợp sẵn tư duy của `wp-eyewink`. Thay vì:

```sql
SELECT * FROM wp_postmeta WHERE post_id = 123; -- Chậm

```

Ocular sẽ hướng truy vấn vào bảng phẳng:

```sql
SELECT price, sku, color FROM ocular_products WHERE post_id = 123; -- Nháy mắt!

```

---

## ⚖️ So sánh hiệu năng

| Chỉ số | WordPress Gốc | Ocular + RoadRunner |
| --- | --- | --- |
| **Vòng đời PHP** | Chết sau mỗi request | Thường trực trong RAM |
| **Boot time** | 100ms - 300ms | **~0ms** (Chỉ boot 1 lần) |
| **Truy vấn Meta** | EAV (Nhiều JOIN) | Flat Table (O(1)) |
| **TTFB** | > 200ms | **5ms - 20ms** |

---

## 📄 Bản quyền & Đóng góp

Ocular Framework được phát triển bởi cộng đồng yêu thích hiệu năng cao. Mọi đóng góp vui lòng gửi Pull Request về kho lưu trữ GitHub.

**Ocular: See the Speed. Feel the Power.**