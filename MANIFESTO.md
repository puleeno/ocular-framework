# Ocular Framework Manifesto

> **"Giữ nguyên bộ não quản trị của WordPress, nhưng thay thế toàn bộ hệ thống thần kinh và cơ bắp bằng công nghệ dữ liệu phẳng và Runtime bền bỉ."**

---

## 5 Trụ Cột Định Nghĩa Ocular

### 1. Mục tiêu tối thượng: "Tốc độ bàn thờ" (Performance First)
Ocular sinh ra để giải quyết triệt để sự chậm chạp kinh niên của WordPress.

*   **Phẳng hóa dữ liệu:** Loại bỏ cấu trúc EAV (Entity-Attribute-Value) phức tạp của bảng `wp_postmeta`. Mọi dữ liệu (WooCommerce, Yoast SEO) được Transformer đưa vào các **Bảng phẳng (Flat Tables)** để truy vấn với tốc độ $O(1)$.
*   **Phá bỏ rào cản Request-Response:** Sử dụng **RoadRunner** để giữ PHP luôn chạy trong RAM, loại bỏ thời gian nạp lại hàng nghìn file Core mỗi khi có người truy cập.

### 2. Triết lý kiến trúc: "Giữ vỏ - Thay lõi"
Ocular không bắt người dùng phải học lại từ đầu.

*   **Admin quen thuộc:** Bạn vẫn dùng Admin của WordPress, vẫn dùng WooCommerce và Yoast SEO như bình thường.
*   **Embroidery thay thế Gutenberg:** Chọn **Embroidery** (Elementor 2.0 fork) làm nhân Core. Đây không chỉ là một Page Builder mà là một **Context Builder** – nơi UI phía frontend trực tiếp định nghĩa cấu trúc dữ liệu phía backend.

### 3. Dữ liệu là ngữ cảnh (Context-Driven)
Ocular xóa bỏ ranh giới giữa Post Type và Taxonomy.

*   Mọi thực thể được quy về một khái niệm duy nhất: **Context**.
*   Việc thiết kế giao diện bằng Context Builder chính là việc thiết kế Database Schema. Khi bạn kéo một Widget, bạn đang tạo ra một trường dữ liệu (Field) trong bảng phẳng.

### 4. Tính linh hoạt và Thích nghi (Hybrid Runtime)
Ocular không kén chọn môi trường nhưng luôn ưu tiên hiệu suất cao nhất.

*   **Switch Mode:** Tự động nhận diện để chạy siêu tốc trên **RoadRunner** hoặc chạy ổn định trên **Nginx/Apache** truyền thống.
*   **Transformer/Converter:** Đóng vai trò là "thông dịch viên" vĩnh viễn, đảm bảo dữ liệu từ các Plugin cũ luôn tương thích với kiến trúc phẳng mới.

### 5. Sự sạch sẽ tuyệt đối (State Management)
Giải quyết "nỗi ám ảnh" về Hooks và Filters.

*   **Snapshot & Reset:** Chụp ảnh hệ thống Hook trước request và khôi phục ngay sau khi kết thúc. Điều này đảm bảo không có sự rò rỉ bộ nhớ hay "nhiễm độc" trạng thái giữa các người dùng khác nhau trong môi trường RoadRunner.
*   **Garbage Collector (GC):** Tự động dọn dẹp các dữ liệu mồ côi, đảm bảo database không bao giờ phình to một cách vô nghĩa.

---

## Tóm gọn Ocular bằng 3 từ khóa:

1.  **Flat:** Dữ liệu phẳng, truy vấn tức thì.
2.  **Persistent:** Runtime bền bỉ, không nạp lại.
3.  **Familiar:** Giao diện cũ, sức mạnh mới.
