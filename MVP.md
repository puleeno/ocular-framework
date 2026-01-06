# Ocular Framework (v1.0-MVP)

**Ocular** là một Framework chuyển đổi WordPress từ cấu trúc EAV lỗi thời sang kiến trúc **Flat-Data & Persistent-Runtime**. Nó giữ lại giao diện Admin quen thuộc nhưng vận hành với tốc độ "bàn thờ" nhờ sự can thiệp của các Kernel chuyên biệt và lớp Transformer thông minh.

## 🚀 Triết lý cốt lõi

* **Context is Everything:** Taxonomy và Post Type được định nghĩa lại thành **Context**.
* **Embroidery Core:** Sử dụng bản fork Elementor 2.0 (Embroidery) làm **Context Builder** chính thức thay cho Gutenberg.
* **Zero-Impact Transformation:** Hàng nghìn Transformer chạy nhưng không ảnh hưởng tốc độ nhờ cơ chế Pre-compiled Mapping.
* **Hybrid Runtime:** Chạy linh hoạt trên **RoadRunner** (Production) hoặc **Nginx/Apache** (Standard).

---

## 🛠 Các thành phần chính của MVP

### 1. Dual-Runtime Bridge

Tự động nhận diện môi trường SAPI để quyết định cách thức vận hành:

* **Persistent Mode:** Chạy trên RoadRunner, giữ Database connection và Transformer trong RAM.
* **Request Mode:** Chạy trên FPM/Apache truyền thống, tối ưu hóa bằng Opcache.

### 2. SQL Transformer & Converter

Lớp đánh chặn mọi truy vấn SQL của WordPress để điều hướng dữ liệu:

* **WooCommerce Support:** Tự động phẳng hóa `Product` và `Order` (SKU, Price, Stock...) vào bảng `ocular_wc_flat`.
* **Yoast SEO Support:** Chuyển đổi toàn bộ Metadata SEO sang bảng phẳng để Frontend truy vấn O(1).

### 3. Context Builder (Embroidery)

Thay vì lưu HTML Comment rác, UI Builder (Frontend) giờ đây đóng vai trò là **Schema Designer**. Mỗi Widget là một cổng dữ liệu (Data Gateway) ánh xạ trực tiếp vào các cột trong Database phẳng.

### 4. Garbage Collector (GC)

Dọn dẹp "orphan data" (dữ liệu mồ côi) ngay lập tức khi một Context Entity (Post/Product) bị xóa, giữ cho Database luôn tinh gọn.

---

## 🏗 Cấu trúc thư mục

```text
ocular-core/
├── apps/
│   ├── Admin/           # Embroidery UI & Admin Scoped Hooks
│   ├── Frontend/        # Flat Data Rendering & Context Provider
├── src/
│   ├── Runtime/         # RoadRunner vs Standard Server Bridge
│   ├── Transformers/    # Logic phẳng hóa (WooCommerce, Yoast SEO)
│   ├── Context/         # Context Builder & Entity Manager
│   └── Database/        # SQL Interceptor & Garbage Collector
├── bootstrap/           # Global State & Hook Snapshot
└── entry_point.php      # Unified Entry Point

```

---

## 🚦 Cài đặt & Sử dụng

### Chạy với RoadRunner (Khuyến nghị)

```bash
# Khởi động Ocular với RoadRunner Worker
./rr serve

```

### Chạy với Nginx/Apache

Không cần cấu hình thêm, Ocular tự động nhận diện và chuyển sang **StandardRuntime**.

---

## 📋 Lộ trình MVP

* [x] Tích hợp nhân Embroidery (Elementor 2.0 fork).
* [x] Xây dựng bộ SQL Transformer cho Core WordPress.
* [x] Hỗ trợ phẳng hóa dữ liệu cho **WooCommerce** mặc định.
* [x] Hỗ trợ Metadata cho **Yoast SEO**.
* [x] Cơ chế Snapshot/Reset cho Action & Filter Hooks.

---

## 🤝 Đóng góp

Ocular cho phép người dùng đóng góp bằng cách đăng ký các **Converter** mới:

```php
$manager->registerConverter('my_custom_plugin', [
    'trigger' => 'wp_postmeta',
    'map' => ['meta_key_1' => 'flat_col_1'],
    'target_table' => 'ocular_custom_table'
]);

```


## ⚙️ Cấu hình Ocular Runtime

Ocular sử dụng tệp cấu hình trung tâm để điều phối hành vi của Transformer và Hook System dựa trên môi trường thực thi.

### 1. File cấu hình mẫu (`ocular.config.php`)

```php
return [
    // Tự động phát hiện Runtime (hoặc ép buộc bằng 'roadrunner' / 'standard')
    'runtime_mode' => env('OCULAR_RUNTIME', 'auto'),

    'performance' => [
        // Lưu trữ bản đồ Transformer vào RAM (Sử dụng APCu cho Standard Mode)
        'transformer_cache' => true,
        
        // Biên dịch chuỗi Hook (Action/Filter) thành mã thực thi tĩnh
        'compile_hooks' => true,
        
        // Tự động dọn dẹp biến toàn cục sau mỗi vòng lặp (Chỉ dành cho RoadRunner)
        'strict_state_reset' => true,
    ],

    'database' => [
        // Bật/tắt việc "phẳng hóa" dữ liệu ngay khi ghi (Dual-Write)
        'jit_migration' => true,
        
        // Danh sách các bảng được Transformer ưu tiên xử lý
        'hot_tables' => ['wp_posts', 'wp_postmeta', 'wp_options', 'wp_woocommerce_order_items'],
    ],
];

```

### 2. Tinh chỉnh cho từng môi trường

#### A. Môi trường Production (RoadRunner)

Để đạt tốc độ "bàn thờ", hãy đảm bảo các thông số sau trong tệp `.env`:

* `OCULAR_RUNTIME=roadrunner`
* `WORKER_COUNT=8` (Tùy theo số nhân CPU)
* `MAX_REQUESTS=500` (Để tránh rò rỉ bộ nhớ từ các Plugin bên thứ ba)

#### B. Môi trường Hosting (Nginx/Apache)

Ocular sẽ tự động hạ cấp xuống **Standard Mode**, nhưng bạn có thể tối ưu thêm:

* Cài đặt **APCu** để Transformer không phải đọc file mapping từ Disk.
* Bật **Zend OPcache** để lưu trữ các Class đã được Ocular biên dịch.

---

## 🛠 Hướng dẫn cho Developer: Tạo Ocular Converter cho WooCommerce

Khi bạn muốn mở rộng MVP để hỗ trợ các thuộc tính tùy chỉnh của WooCommerce (như Brand, Warehouse ID...), bạn chỉ cần tạo một Converter:

```php
use Ocular\Database\Converter;

class WooBrandConverter extends Converter {
    public function setup() {
        $this->bind('wp_postmeta')
             ->where('meta_key', '_product_brand')
             ->toTable('ocular_wc_product_flat', 'brand_name');
    }
}

```

### 3. Lệnh CLI hỗ trợ

Ocular đi kèm với các lệnh hỗ trợ quản lý State:

* `php ocular migrate`: Tạo các bảng phẳng dựa trên các Transformer đã đăng ký.
* `php ocular warmup`: Quét toàn bộ dữ liệu cũ và đẩy vào Flat Tables (Chạy 1 lần duy nhất).
* `php ocular gc:run`: Kích hoạt Garbage Collector thủ công để dọn rác DB.
