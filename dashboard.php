<?php
include_once 'config.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: index.php");
    exit();
}

// Ambil semua data tiket
$query_tiket = mysqli_query($koneksi, "SELECT * FROM tickets ORDER BY id_ticket DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Maintenance Tiket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">PT EPN Maintenance</a>
            <div class="d-flex align-items-center text-white">
                <span class="me-3">Halo, <strong><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></strong></span>
                <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fw-bold text-secondary mb-0">Daftar Tiket Kerusakan</h3>
            <a href="buat-tiket.php" class="btn btn-primary fw-bold">+ Buat Tiket Baru</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="width: 50px;">No</th>
                                <th>Aset / Perangkat</th>
                                <th>Deskripsi</th>
                                <th class="text-center">Prioritas</th>
                                <th>Pelapor</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($query_tiket && mysqli_num_rows($query_tiket) > 0): 
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($query_tiket)): 
                                    
                                    // Fetch Asset Name
                                    $nama_asset = "Pos Timbangan Abu 1";
                                    if (!empty($row['id_asset'])) {
                                        $id_a = $row['id_asset'];
                                        $q_a = mysqli_query($koneksi, "SELECT * FROM assets WHERE id_asset = '$id_a' OR id = '$id_a'");
                                        if ($q_a && $d_a = mysqli_fetch_assoc($q_a)) {
                                            $nama_asset = $d_a['nama_asset'] ?? ($d_a['nama_perangkat'] ?? ($d_a['nama'] ?? $nama_asset));
                                        }
                                    }

                                    // Fetch Reporter Name
                                    $nama_pelapor = "Operator";
                                    if (!empty($row['id_reporter'])) {
                                        $id_u = $row['id_reporter'];
                                        $q_u = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = '$id_u' OR id = '$id_u'");
                                        if ($q_u && $d_u = mysqli_fetch_assoc($q_u)) {
                                            $nama_pelapor = $d_u['nama_lengkap'] ?? ($d_u['username'] ?? $nama_pelapor);
                                        }
                                    }

                                    // Format Status & Badge
                                    $st = strtolower(trim($row['status'] ?? 'open'));
                                    if (in_array($st, ['progress', 'in_progress', 'diproses'])) {
                                        $badge_status = '<span class="badge bg-warning text-dark px-3 py-2">Progress</span>';
                                    } elseif (in_array($st, ['selesai', 'closed'])) {
                                        $badge_status = '<span class="badge bg-success px-3 py-2">Selesai</span>';
                                    } else {
                                        $badge_status = '<span class="badge bg-danger px-3 py-2">Open</span>';
                                    }

                                    // Format Prioritas
                                    $prio = strtolower(trim($row['prioritas'] ?? 'medium'));
                                    if ($prio == 'high' || $prio == 'tinggi') {
                                        $badge_prio = '<span class="badge bg-danger">Tinggi</span>';
                                    } elseif ($prio == 'medium' || $prio == 'sedang') {
                                        $badge_prio = '<span class="badge bg-warning text-dark">Sedang</span>';
                                    } else {
                                        $badge_prio = '<span class="badge bg-secondary">Rendah</span>';
                                    }
                            ?>
                                <tr>
                                    <td class="text-center fw-bold"><?= $no++ ?></td>
                                    <td class="fw-semibold text-primary"><?= htmlspecialchars($nama_asset) ?></td>
                                    <td><?= htmlspecialchars($row['deskripsi'] ?? ($row['deskripsi_kerusakan'] ?? '-')) ?></td>
                                    <td class="text-center"><?= $badge_prio ?></td>
                                    <td><?= htmlspecialchars($nama_pelapor) ?></td>
                                    <td class="text-center"><?= $badge_status ?></td>
                                    <td class="text-center">
                                        <a href="tiket.php?id=<?= $row['id_ticket'] ?>" class="btn btn-sm btn-primary">Detail</a>
                                    </td>
                                </tr>
                            <?php 
                                endwhile; 
                            else: 
                            ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data tiket.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>