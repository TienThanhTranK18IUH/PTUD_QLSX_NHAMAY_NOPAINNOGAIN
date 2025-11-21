<?php
// Dev tool: preview which sidebar menu items are visible for a given role
// Usage: open in browser and choose a role from the dropdown
if (session_id() === '') session_start();

$sampleRoles = array(
    'Quản lý',
    'Xưởng trưởng',
    'Công nhân',
    'Nhân viên kho',
    'PKH',
    'QC',
    'Nhân viên kỹ thuật',
    'manager', 'leader', 'worker', 'warehouse', 'planner', 'qc', 'technician'
);

$selected = isset($_GET['role']) ? $_GET['role'] : '';

if ($selected !== '') {
    // set a minimal user session so checkRole() in helpers works
    $_SESSION['user'] = array('vaiTro' => $selected, 'ma_nv' => 'DEV');
}

ob_start();
// include the app sidebar (it will read session and helpers)
include __DIR__ . '/../app/views/layouts/sidebar.php';
$sidebarHtml = ob_get_clean();

// Extract anchor texts and hrefs for a quick summary
$links = array();
if (preg_match_all('/<a[^>]*href=["\']?([^"\' >]+)[^>]*>(.*?)<\/a>/si', $sidebarHtml, $m)) {
    for ($i = 0; $i < count($m[0]); $i++) {
        $text = strip_tags($m[2][$i]);
        $href = $m[1][$i];
        $links[] = array('text' => trim(preg_replace('/\s+/', ' ', $text)), 'href' => $href);
    }
}

?><!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Role Menu Preview</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;padding:18px}
    .panel{display:flex;gap:24px;align-items:flex-start}
    .sidebar-preview{width:340px}
    .links{flex:1}
    select,button{margin-left:8px}
    ul{padding-left:18px}
    .note{color:#666;font-size:0.9em}
  </style>
</head>
<body>
  <h2>Role Menu Preview (dev)</h2>
  <form method="get">
    <label>Role:
      <select name="role">
        <option value="">-- (no role) --</option>
        <?php foreach ($sampleRoles as $r): $sel = ($r === $selected) ? ' selected' : ''; ?>
          <option value="<?php echo htmlspecialchars($r); ?>"<?php echo $sel; ?>><?php echo htmlspecialchars($r); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <button type="submit">Apply</button>
    <a href="?">Reset</a>
  </form>

  <div class="panel">
    <div class="sidebar-preview">
      <h3>Rendered sidebar</h3>
      <?php echo $sidebarHtml; ?>
    </div>
    <div class="links">
      <h3>Extracted links</h3>
      <ul>
        <?php if (count($links) === 0): ?>
          <li><em>No links found in sidebar output.</em></li>
        <?php else: foreach ($links as $ln): ?>
          <li><a href="<?php echo htmlspecialchars($ln['href']); ?>"><?php echo htmlspecialchars($ln['text']); ?></a></li>
        <?php endforeach; endif; ?>
      </ul>
    </div>
  </div>

  <p class="note">Note: This is a developer preview tool. Do not deploy to production.</p>
</body>
</html>
