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
ocular-app/
├── app/
│   ├── Http/
│   │   ├── Middlewares/   # Các bộ lọc PSR-15
│   │   └── Controllers/   # Logic xử lý nghiệp vụ
│   ├── Providers/         # Đăng ký dịch vụ (DB, Cache, Redis)
│   └── Models/            # Mapping dữ liệu phẳng (Ocular Mapping)
├── bin/                   # RoadRunner binary & CLI tools
├── core/                  # Nhân WordPress (WP-Core)
├── public/                # File tĩnh và entry point dự phòng
├── worker.php             # File thực thi chính cho RoadRunner
└── .rr.yaml               # Cấu hình RoadRunner

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

### 3. Cấu hình Worker (`worker.php`)

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