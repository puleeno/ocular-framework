Việc hỗ trợ **WP-CLI** không chỉ là một tính năng cộng thêm, mà nó là "xương sống" để vận hành **Ocular Framework**. Vì Ocular can thiệp sâu vào Database (Flat Tables) và Runtime (RoadRunner), chúng ta cần các lệnh CLI để điều phối việc đồng bộ dữ liệu mà không làm treo UI của Admin.

Dưới đây là kiến trúc và các lệnh WP-CLI tùy chỉnh cho Ocular.

---

### 1. Kiến trúc Ocular CLI Command

Chúng ta sẽ đăng ký một namespace chính là `ocular` trong WP-CLI. Các lệnh này sẽ được nạp thông qua `Admin Kernel` để đảm bảo có đầy đủ quyền truy cập vào hệ thống Transformer.

### 2. Các lệnh Core cho MVP

#### A. Nhóm lệnh Migration & Warmup

Dùng để khởi tạo cấu trúc bảng phẳng từ các định nghĩa trong Transformer.

* **`wp ocular table init`**: Tự động quét toàn bộ Transformer (WooCommerce, Yoast SEO) và tạo các bảng `ocular_*` tương ứng.
* **`wp ocular warmup [--force]`**: Quét toàn bộ dữ liệu hiện có trong `wp_posts` và `wp_postmeta`, chạy qua bộ chuyển đổi (Converter) và đổ đầy vào bảng phẳng. Đây là lệnh bắt buộc phải chạy khi lần đầu cài đặt Ocular lên một site cũ.

#### B. Nhóm lệnh Maintenance (Garbage Collector)

* **`wp ocular gc run [--dry-run]`**: Kích hoạt Garbage Collector để quét rác, xóa các bản ghi mồ côi trong bảng phẳng mà WordPress "quên" dọn dẹp.
* **`wp ocular cache purge`**: Xóa toàn bộ mapping cache của Transformer trong APCu hoặc RAM.

#### C. Nhóm lệnh Runtime (RoadRunner)

* **`wp ocular rr:status`**: Kiểm tra trạng thái các Worker của RoadRunner.
* **`wp ocular rr:reload`**: Reset toàn bộ Worker (hữu ích khi bạn vừa cập nhật code PHP mà không muốn restart server).

---

### 3. Tích hợp mã nguồn CLI

Chúng ta sẽ tạo một Class chuyên biệt để đăng ký với WP-CLI:

```php
if ( defined( 'WP_CLI' ) && WP_CLI ) {
    class Ocular_CLI_Command {
        /**
         * Đồng bộ hóa dữ liệu từ EAV sang Flat Tables.
         * * ## EXAMPLES
         * * wp ocular warmup
         */
        public function warmup( $args, $assoc_args ) {
            WP_CLI::line( "🚀 Starting Ocular Data Warmup..." );
            
            $engine = new \Ocular\Database\WarmupEngine();
            $count = $engine->execute();
            
            WP_CLI::success( "Processed $count entities into Flat Tables!" );
        }

        /**
         * Kiểm tra sự tương thích giữa Transformer và Database.
         */
        public function status() {
            $status = \Ocular\Core\Health::check();
            WP_CLI\Utils\format_items( 'table', $status, ['Component', 'Status', 'Message'] );
        }
    }

    WP_CLI::add_command( 'ocular', 'Ocular_CLI_Command' );
}

```

---

### 4. Tự động hóa qua Crontab

Vì Ocular hướng tới tốc độ "bàn thờ", các tác vụ nặng của WooCommerce (như tính toán báo cáo từ bảng phẳng) nên được đẩy vào CLI:

```bash
# Chạy GC mỗi giờ một lần để đảm bảo DB luôn sạch
0 * * * * wp ocular gc run --quiet

```

---

### 5. Tại sao WP-CLI quan trọng cho Ocular?

* **Bypass Timeout:** Các site WooCommerce lớn có hàng triệu meta, việc chuyển đổi dữ liệu (Warmup) qua trình duyệt sẽ bị timeout. CLI là con đường duy nhất.
* **Headless Operations:** Giúp CI/CD pipeline tự động cập nhật Schema khi bạn deploy phiên bản Ocular mới có thêm các Transformer mới.

---

### Cập nhật README.md (Phần WP-CLI)

```markdown
## ⌨️ WP-CLI Integration

Ocular cung cấp bộ công cụ CLI mạnh mẽ để quản lý trạng thái dữ liệu:

| Lệnh | Mô tả |
| :--- | :--- |
| `wp ocular table init` | Khởi tạo các bảng phẳng (Flat Tables). |
| `wp ocular warmup` | Đồng bộ dữ liệu cũ từ WordPress sang Ocular. |
| `wp ocular gc run` | Chạy bộ dọn dẹp dữ liệu mồ côi. |
| `wp ocular rr:reload` | Làm mới các RoadRunner Workers. |
