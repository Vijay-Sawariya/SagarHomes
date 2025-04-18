<?php
include('db.php');

$type = $_POST['type'] ?? '';
$area = $_POST['area'] ?? '';
$agent = $_POST['agent'] ?? '';
$followUpDate = $_POST['followUpDate'] ?? '';
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;

$limit = 10;
$offset = ($page - 1) * $limit;

// Build filter conditions
$conditions = [];
if ($type !== '') $conditions[] = "type = '" . mysqli_real_escape_string($conn, $type) . "'";
if ($area !== '') $conditions[] = "area = '" . mysqli_real_escape_string($conn, $area) . "'";
if ($agent !== '') $conditions[] = "agent = '" . mysqli_real_escape_string($conn, $agent) . "'";
if ($followUpDate !== '') $conditions[] = "DATE(next_follow_up) = '" . mysqli_real_escape_string($conn, $followUpDate) . "'";

$where = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

// Get total count for pagination
$totalQuery = "SELECT COUNT(*) as total FROM leads $where";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRows = mysqli_fetch_assoc($totalResult)['total'];
$totalPages = ceil($totalRows / $limit);

// Fetch leads
$query = "SELECT * FROM leads $where ORDER BY next_follow_up ASC LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0): ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Type</th>
                    <th>Area</th>
                    <th>Agent</th>
                    <th>Next Follow Up</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $today = new DateTime();
                while ($row = mysqli_fetch_assoc($result)):
                    $followUpDate = new DateTime($row['next_follow_up']);
                    $daysDiff = (int)$today->diff($followUpDate)->format('%r%a');
                    $highlight = ($daysDiff >= 0 && $daysDiff <= 3) ? 'highlight-followup' : '';
                ?>
                    <tr class="<?= $highlight ?>">
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['contact']) ?></td>
                        <td><?= htmlspecialchars($row['type']) ?></td>
                        <td><?= htmlspecialchars($row['area']) ?></td>
                        <td><?= htmlspecialchars($row['agent']) ?></td>
                        <td><?= htmlspecialchars($row['next_follow_up']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a href="#" class="page-link pagination-link" data-page="<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php else: ?>
    <div class="alert alert-warning text-center">No leads found.</div>
<?php endif; ?>
