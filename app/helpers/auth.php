<?php
if (session_id() === '') session_start();

// Normalize various role labels from DB to canonical keys
function normalizeRole($r) {
    // If role empty, allow special-case mapping based on session user (temporary)
    if (!$r) {
        if (session_id() === '') @session_start();
        if (isset($_SESSION['user'])) {
            $u = $_SESSION['user'];
            // Temporary: map known PKH account(s) to planner when vaiTro is blank
            if ((isset($u['tenDangNhap']) && $u['tenDangNhap'] === 'nvpkh') || (isset($u['maNguoiDung']) && $u['maNguoiDung'] === 'ND009')) {
                return 'planner';
            }
        }
        return '';
    }
    $r = trim($r);
    $map = array(
        'QuanLy' => 'manager', 'Quản lý' => 'manager',
        'XuongTruong' => 'leader', 'Xưởng trưởng' => 'leader', 'Xuong Truong' => 'leader',
        'CongNhan' => 'worker', 'Công nhân' => 'worker',
        'NhanVienKho' => 'warehouse', 'Nhân viên kho' => 'warehouse', 'NVKho' => 'warehouse',
        'PKH' => 'planner', 'PhongKeHoach' => 'planner', 'Nhân viên phòng kế hoạch' => 'planner',
        'QC' => 'qc', 'Bộ phận QC' => 'qc',
        'KyThuat' => 'technician', 'Nhân viên kỹ thuật' => 'technician'
    );
    // direct match first (covers DB values that exactly match keys above)
    if (isset($map[$r])) return $map[$r];

    // try normalized matching: remove accents/transliterate then strip non-alnum
    $norm = @iconv('UTF-8', 'ASCII//TRANSLIT', $r);
    if ($norm === false) $norm = $r;
    $norm = strtolower(preg_replace('/[^a-zA-Z0-9]/','', $norm));

    // build normalized map and try to match
    foreach ($map as $k => $v) {
        $k2 = @iconv('UTF-8', 'ASCII//TRANSLIT', $k);
        if ($k2 === false) $k2 = $k;
        $k2 = strtolower(preg_replace('/[^a-zA-Z0-9]/','', $k2));
        if ($k2 === $norm) return $v;
    }

    // fallback: return cleaned token (may be a canonical key already)
    return $norm;
}

function isLoggedIn() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']);
}

function getCurrentUser() {
    return isLoggedIn() ? $_SESSION['user'] : null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php?controller=auth&action=login');
        exit;
    }
}

// $allowedRoles: array of canonical keys or single string
function checkRole($allowedRoles) {
    if (!isLoggedIn()) return false;
    $user = getCurrentUser();
    $role = normalizeRole(isset($user['vaiTro']) ? $user['vaiTro'] : (isset($user['vai_tro']) ? $user['vai_tro'] : ''));
    if (is_string($allowedRoles)) $allowedRoles = array($allowedRoles);
    foreach ($allowedRoles as $ar) {
        if ($role === normalizeRole($ar) || $role === $ar) return true;
    }
    return false;
}

function requireRole($allowedRoles) {
    if (!checkRole($allowedRoles)) {
        header('HTTP/1.1 403 Forbidden');
        echo '<div style="padding:30px;"><h2>403 - Bạn không có quyền truy cập</h2><p>Bạn không đủ quyền để xem trang này.</p><p><a href="index.php">Quay lại</a></p></div>';
        exit;
    }
}

?>