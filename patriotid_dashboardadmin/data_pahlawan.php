<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../patriotid_login/login.php');
    exit;
}
require_once '../includes/koneksi.php';

// ── Tab aktif ──
$tab = $_GET['tab'] ?? 'pahlawan'; // pahlawan | tempat | tragedi

// ── Parameter ──
$search   = trim($_GET['search'] ?? '');
$filter   = trim($_GET['filter'] ?? '');
$sort_col = $_GET['sort'] ?? 'id';
$sort_dir = $_GET['dir']  ?? 'asc';
$page     = max(1, (int)($_GET['page'] ?? 1));
$per_page = 5;

$sort_dir = strtolower($sort_dir) === 'desc' ? 'desc' : 'asc';
$next_dir = $sort_dir === 'asc' ? 'desc' : 'asc';

// ── Konfigurasi per tab ──
$tabs = [
    'pahlawan' => [
        'label'        => 'Pahlawan',
        'table'        => 'pahlawan',
        'cols'         => ['nama' => 'Nama', 'asal' => 'Asal', 'era' => 'Era'],
        'allowed'      => ['id', 'nama', 'asal', 'era'],
        'filter_col'   => 'era',
        'filter_label' => 'Semua Era',
        'tambah'       => 'tambah_konten.php',
        'edit'         => 'edit_konten.php',
        'hapus'        => 'hapus_konten.php',
    ],
    'tempat' => [
        'label'        => 'Tempat Bersejarah',
        'table'        => 'tempat_bersejarah',
        'cols'         => ['nama' => 'Nama', 'lokasi' => 'Lokasi'],
        'allowed'      => ['id', 'nama', 'lokasi', 'created_at'],
        'filter_col'   => 'lokasi',
        'filter_label' => 'Semua Lokasi',
        'tambah'       => 'tambah_tempat.php',
        'edit'         => 'edit_tempat.php',
        'hapus'        => 'hapus_tempat.php',
    ],
    'tragedi' => [
        'label'        => 'Tragedi Bersejarah',
        'table'        => 'tragedi_bersejarah',
        'cols'         => ['judul' => 'Judul', 'tahun' => 'Tahun', 'lokasi' => 'Lokasi'],
        'allowed'      => ['id', 'judul', 'tahun', 'lokasi', 'created_at'],
        'filter_col'   => 'tahun',
        'filter_label' => 'Semua Tahun',
        'tambah'       => 'tambah_tragedi.php',
        'edit'         => 'edit_tragedi.php',
        'hapus'        => 'hapus_tragedi.php',
    ],
];

if (!array_key_exists($tab, $tabs)) $tab = 'pahlawan';
$cfg = $tabs[$tab];

if (!in_array($sort_col, $cfg['allowed'])) $sort_col = 'id';

// ── Cek apakah tabel ada di database ──
$tabelAda = false;
try {
    $pdo->query("SELECT 1 FROM `{$cfg['table']}` LIMIT 1");
    $tabelAda = true;
} catch (Exception $e) {
    $tabelAda = false;
}

$pahlawans  = [];
$total      = 0;
$total_page = 1;
$filters    = [];
$offset     = 0;

if ($tabelAda) {
    // Filter options
    $fcol    = $cfg['filter_col'];
    $filters = $pdo->query("SELECT DISTINCT `$fcol` FROM `{$cfg['table']}` WHERE `$fcol` IS NOT NULL AND `$fcol` != '' ORDER BY `$fcol` ASC")->fetchAll(PDO::FETCH_COLUMN);

    // WHERE
    $where  = [];
    $params = [];
    $searchCols = array_keys($cfg['cols']);

    if ($search !== '') {
        $parts = [];
        foreach ($searchCols as $i => $sc) {
            $parts[] = "`$sc` LIKE :s$i";
            $params[":s$i"] = "%$search%";
        }
        $where[] = '(' . implode(' OR ', $parts) . ')';
    }
    if ($filter !== '') {
        $where[] = "`$fcol` = :fcol";
        $params[':fcol'] = $filter;
    }

    $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    // Count
    $cs = $pdo->prepare("SELECT COUNT(*) FROM `{$cfg['table']}` $where_sql");
    $cs->execute($params);
    $total      = (int)$cs->fetchColumn();
    $total_page = max(1, (int)ceil($total / $per_page));
    $page       = min($page, $total_page);
    $offset     = ($page - 1) * $per_page;

    // Data
    $colSql = 'id, ' . implode(', ', array_map(fn($c) => "`$c`", array_keys($cfg['cols'])));
    $ds = $pdo->prepare("SELECT $colSql FROM `{$cfg['table']}` $where_sql ORDER BY $sort_col $sort_dir LIMIT :lim OFFSET :off");
    foreach ($params as $k => $v) $ds->bindValue($k, $v);
    $ds->bindValue(':lim', $per_page, PDO::PARAM_INT);
    $ds->bindValue(':off', $offset,   PDO::PARAM_INT);
    $ds->execute();
    $pahlawans = $ds->fetchAll();
}

function build_url(array $ov = []): string {
    $base = array_merge([
        'tab'    => $_GET['tab']    ?? 'pahlawan',
        'search' => $_GET['search'] ?? '',
        'filter' => $_GET['filter'] ?? '',
        'sort'   => $_GET['sort']   ?? 'id',
        'dir'    => $_GET['dir']    ?? 'asc',
        'page'   => $_GET['page']   ?? 1,
    ], $ov);
    return '?' . http_build_query(array_filter($base, fn($v) => $v !== ''));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PatriotID - Data Pahlawan</title>
  <link rel="stylesheet" href="data_pahlawan.css" />
</head>
<body>

<?php
  $halaman_aktif = 'data_pahlawan';
require_once '../patriotid_navbar/navbar.php';
?>

  <div class="layout">
    <aside class="sidebar">
      <nav>
        <a href="../patriotid_dashboardadmin/dashboard.php"     class="nav-item">Dashboard utama</a>
        <a href="../patriotid_dashboardadmin/data_pahlawan.php" class="nav-item active">Data Pahlawan</a>
        <a href="../patriotid_dashboardadmin/profil.php"        class="nav-item">Profil</a>
        <a href="../patriotid_logout/logout.php"                class="nav-item">Keluar</a>
      </nav>
    </aside>

    <main class="main">
      <h1 class="page-title">Data Pahlawan</h1>

      <!-- ── TABS ── -->
      <div class="tab-nav">
        <?php foreach ($tabs as $key => $t): ?>
          <a href="?tab=<?= $key ?>"
             class="tab-item <?= $tab === $key ? 'active' : '' ?>">
            <?= $t['label'] ?>
          </a>
        <?php endforeach; ?>
      </div>

      <!-- ── CARD ── -->
      <div class="table-card">
        <div class="table-header">
          <span class="table-header-text">
            Data <?= htmlspecialchars($cfg['label']) ?>
          </span>
          <a href="<?= $cfg['tambah'] ?>" class="btn-tambah">+ Tambah Data</a>
        </div>

        <?php if (!$tabelAda): ?>
          <div style="padding:24px; color:#888; text-align:center;">
            ⚠️ Tabel <b><?= $cfg['table'] ?></b> belum ada di database.<br>
            Silakan buat tabel terlebih dahulu di phpMyAdmin.
          </div>
        <?php else: ?>

        <!-- Notifikasi -->
        <?php if (isset($_GET['status'])): ?>
          <?php if ($_GET['status'] === 'tambah_ok'): ?>
            <div class="alert-success" style="margin:12px 18px 0">✅ Data berhasil ditambahkan.</div>
          <?php elseif ($_GET['status'] === 'edit_ok'): ?>
            <div class="alert-success" style="margin:12px 18px 0">✅ Data berhasil diperbarui.</div>
          <?php elseif ($_GET['status'] === 'hapus_ok'): ?>
            <div class="alert-success" style="margin:12px 18px 0">✅ Data berhasil dihapus.</div>
          <?php endif; ?>
        <?php endif; ?>

        <!-- Search + Filter -->
        <form method="GET" class="toolbar">
          <input type="hidden" name="tab" value="<?= $tab ?>">
          <input type="text" name="search" class="search-input"
            placeholder="Cari data..."
            value="<?= htmlspecialchars($search) ?>">
          <select name="filter" class="filter-select">
            <option value=""><?= $cfg['filter_label'] ?></option>
            <?php foreach ($filters as $f): ?>
              <option value="<?= htmlspecialchars($f) ?>" <?= $filter === $f ? 'selected' : '' ?>>
                <?= htmlspecialchars($f) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <input type="hidden" name="sort" value="<?= htmlspecialchars($sort_col) ?>">
          <input type="hidden" name="dir"  value="<?= htmlspecialchars($sort_dir) ?>">
          <button type="submit" class="btn-cari">Cari</button>
          <?php if ($search !== '' || $filter !== ''): ?>
            <a href="?tab=<?= $tab ?>" class="btn-reset">Reset</a>
          <?php endif; ?>
        </form>

        <!-- Tabel -->
        <table class="data-table">
          <thead>
            <tr>
              <th>No.</th>
              <?php foreach ($cfg['cols'] as $col_key => $col_label):
                $is_active = ($sort_col === $col_key);
                $arrow = $is_active ? ($sort_dir === 'asc' ? ' ↑' : ' ↓') : ' ↕';
                $href  = build_url(['sort' => $col_key, 'dir' => $is_active ? $next_dir : 'asc', 'page' => 1]);
              ?>
              <th>
                <a href="<?= $href ?>" class="sort-link <?= $is_active ? 'sort-active' : '' ?>">
                  <?= $col_label ?><?= $arrow ?>
                </a>
              </th>
              <?php endforeach; ?>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($pahlawans) > 0): ?>
              <?php $no = $offset + 1; foreach ($pahlawans as $p): ?>
              <tr>
                <td><?= $no++ ?></td>
                <?php foreach (array_keys($cfg['cols']) as $col_key): ?>
                  <td><?= htmlspecialchars($p[$col_key] ?? '-') ?></td>
                <?php endforeach; ?>
                <td class="aksi-col">
                  <a href="<?= $cfg['edit'] ?>?id=<?= $p['id'] ?>" class="btn-edit">Edit</a>
                  <button class="btn-hapus"
                    onclick="hapusData(<?= $p['id'] ?>, '<?= htmlspecialchars($p[array_key_first($cfg['cols'])], ENT_QUOTES) ?>', '<?= $cfg['hapus'] ?>')">
                    Hapus
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="<?= count($cfg['cols']) + 2 ?>" style="text-align:center; color:#888; padding:24px;">
                  Tidak ada data yang ditemukan.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- Pagination -->
        <div class="pagination">
          <span class="page-info">
            <?php if ($total > 0): ?>
              Menampilkan <?= $offset+1 ?>–<?= min($offset+$per_page, $total) ?> dari <?= $total ?> data
            <?php else: ?>
              0 data
            <?php endif; ?>
          </span>
          <div class="page-btns">
            <?php if ($page > 1): ?>
              <a href="<?= build_url(['page' => $page-1]) ?>" class="page-btn">‹</a>
            <?php else: ?>
              <span class="page-btn disabled">‹</span>
            <?php endif; ?>

            <?php
            $sp = max(1, $page-2); $ep = min($total_page, $page+2);
            if ($sp > 1): ?><a href="<?= build_url(['page'=>1]) ?>" class="page-btn">1</a><?php
              if ($sp > 2): ?><span class="page-ellipsis">…</span><?php endif;
            endif;
            for ($i=$sp; $i<=$ep; $i++): ?>
              <a href="<?= build_url(['page'=>$i]) ?>"
                 class="page-btn <?= $i===$page?'active':'' ?>"><?= $i ?></a>
            <?php endfor;
            if ($ep < $total_page):
              if ($ep < $total_page-1): ?><span class="page-ellipsis">…</span><?php endif; ?>
              <a href="<?= build_url(['page'=>$total_page]) ?>" class="page-btn"><?= $total_page ?></a>
            <?php endif; ?>

            <?php if ($page < $total_page): ?>
              <a href="<?= build_url(['page' => $page+1]) ?>" class="page-btn">›</a>
            <?php else: ?>
              <span class="page-btn disabled">›</span>
            <?php endif; ?>
          </div>
        </div>

        <?php endif; // tabelAda ?>
      </div>
    </main>
  </div>

  <script>
    function hapusData(id, nama, url) {
      if (confirm('Hapus konten "' + nama + '" ini?')) {
        window.location.href = url + '?id=' + id;
      }
    }
  </script>

</body>
</html>