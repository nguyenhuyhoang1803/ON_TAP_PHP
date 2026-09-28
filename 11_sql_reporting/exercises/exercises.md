# BÀI TẬP RÈN LUYỆN: SQL BÁO CÁO NÂNG CAO

## Đề bài: Hệ Thống Báo Cáo Phòng Khám Đa Khoa
- `bac_si` (`id`, `ho_ten`, `chuyen_khoa`)
- `benh_nhan` (`id`, `ho_ten`, `ngay_sinh`)
- `ca_kham` (`id`, `bac_si_id`, `benh_nhan_id`, `ngay_kham`, `chi_phi`)

### Yêu cầu viết câu lệnh SQL:
1. Thống kê tất cả bác sĩ: Họ tên, Chuyên khoa, Tổng số ca khám đã thực hiện, Tổng doanh thu khám bệnh mang lại (kể cả bác sĩ mới về chưa có ca khám nào).
2. Tìm bác sĩ có số lượng ca khám nhiều nhất trong năm 2026 (xử lý đồng hạng nếu có nhiều bác sĩ cùng số ca khám nhiều nhất).
3. Tìm những bệnh nhân đã từng khám ở ít nhất 2 chuyên khoa khác nhau.
