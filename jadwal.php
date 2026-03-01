<?php
 $pageTitle = "Jadwal Dokter";
require_once 'config/database.php';
 $db = new Database();

// Join tabel doctors dan schedules
 $db->query("SELECT d.name, d.specialization, s.day, s.time 
            FROM doctors d 
            JOIN schedules s ON d.id = s.doctor_id 
            ORDER BY FIELD(s.day, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), s.time");
 $schedules = $db->resultSet();

require_once 'inc/header.php';
?>

    <!-- Header -->
    <div class="container-fluid page-header py-5 wow fadeIn" data-wow-delay="0.1s">
        <div class="container text-center py-5">
            <h1 class="display-2 text-white mb-4">Jadwal Praktek</h1>
        </div>
    </div>

    <!-- Jadwal Table -->
    <div class="container-fluid py-5">
        <div class="container">
            <div class="table-responsive">
                <table class="table table-bordered table-hover shadow-sm">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Nama Dokter</th>
                            <th>Spesialisasi</th>
                            <th>Hari</th>
                            <th>Jam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($schedules as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['specialization']); ?></td>
                            <td><?php echo htmlspecialchars($row['day']); ?></td>
                            <td><?php echo htmlspecialchars($row['time']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php require_once 'inc/footer.php'; ?>