<?php
require 'db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth.php");
    exit;
}

$alert = '';

// 1. Approve / Delete Member
if (isset($_GET['approve_user'])) {
    $id = (int)$_GET['approve_user'];
    $pdo->prepare("UPDATE users SET status = 'active' WHERE id = ?")->execute([$id]);
    $alert = "Mwanachama amepitishwa (Activated) rasmi!";
}
if (isset($_GET['delete_user'])) {
    $id = (int)$_GET['delete_user'];
    $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'")->execute([$id]);
    $alert = "Mwanachama ameondolewa kwenye mfumo!";
}

// 2. Upload Memory (Picha au Video)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_memory'])) {
    $title = htmlspecialchars($_POST['title']);
    $desc = htmlspecialchars($_POST['description']);
    $date = $_POST['event_date'];
    $type = $_POST['media_type'];

    $target_dir = "uploads/";
    $file_name = time() . "_" . basename($_FILES["media_file"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["media_file"]["tmp_name"], $target_file)) {
        $stmt = $pdo->prepare("INSERT INTO memories (title, description, media_type, file_path, event_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $desc, $type, $target_file, $date]);
        $alert = "Memory imepakiwa na kuhifadhiwa kikamilifu!";
    } else {
        $alert = "Imeshindwa kupakia faili. Hakikisha folder la 'uploads' lipo kwenye direktori yako.";
    }
}

// 3. Approve / Delete Review
if (isset($_GET['approve_review'])) {
    $id = (int)$_GET['approve_review'];
    $pdo->prepare("UPDATE reviews SET is_approved = 1 WHERE id = ?")->execute([$id]);
    $alert = "Review imepitishwa kuonekana kwenye ukurasa wa mbele!";
}
if (isset($_GET['delete_review'])) {
    $id = (int)$_GET['delete_review'];
    $pdo->prepare("DELETE FROM reviews WHERE id = ?")->execute([$id]);
    $alert = "Review imefutwa!";
}

$users = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$reviews = $pdo->query("SELECT * FROM reviews ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Babati Maits Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        body { background-color: #09090b; color: #f4f4f5; font-family: 'Segoe UI', sans-serif; }
        .glass { background: rgba(24, 24, 27, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="flex min-h-screen">

    <!-- Admin Sidebar -->
    <aside class="w-64 bg-zinc-900 border-r border-zinc-800 p-6 flex flex-col justify-between hidden md:flex">
        <div>
            <div class="mb-8">
                <h1 class="text-xl font-black text-rose-500 tracking-wider">BABATI MAITS</h1>
                <span class="text-xs bg-purple-600 px-2.5 py-0.5 rounded-full text-white font-semibold">ADMIN PANEL</span>
            </div>
            <nav class="space-y-2">
                <a href="#members" class="flex items-center gap-3 bg-zinc-800 text-white px-4 py-3 rounded-xl transition"><i class="fas fa-users-cog text-rose-500"></i> Manage Members</a>
                <a href="#upload" class="flex items-center gap-3 text-zinc-400 hover:bg-zinc-800 hover:text-white px-4 py-3 rounded-xl transition"><i class="fas fa-cloud-upload-alt text-purple-500"></i> Upload Memories</a>
                <a href="#reviews" class="flex items-center gap-3 text-zinc-400 hover:bg-zinc-800 hover:text-white px-4 py-3 rounded-xl transition"><i class="fas fa-star text-yellow-500"></i> Event Reviews</a>
                <a href="index.php" class="flex items-center gap-3 text-zinc-400 hover:bg-zinc-800 hover:text-white px-4 py-3 rounded-xl transition"><i class="fas fa-globe text-blue-500"></i> View Live Site</a>
            </nav>
        </div>
        <a href="logout.php" class="flex items-center gap-3 text-red-400 hover:bg-red-500/10 px-4 py-3 rounded-xl transition"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </aside>

    <!-- Admin Main Content -->
    <main class="flex-1 p-6 md:p-10 overflow-y-auto space-y-12">
        
        <!-- Top bar kwa simu -->
        <div class="flex justify-between items-center md:hidden border-b border-zinc-800 pb-4">
            <h1 class="text-xl font-black text-rose-500">ADMIN PANEL</h1>
            <a href="logout.php" class="bg-red-600 px-4 py-2 rounded-lg text-xs font-bold">Logout</a>
        </div>

        <?php if($alert): ?>
            <div class="bg-emerald-500/20 border border-emerald-500 text-emerald-300 p-4 rounded-xl text-sm flex items-center gap-3">
                <i class="fas fa-info-circle text-lg"></i> <span><?= $alert ?></span>
            </div>
        <?php endif; ?>

        <!-- 1. MANAGE MEMBERS -->
        <section id="members">
            <h2 class="text-2xl font-bold mb-4 flex items-center gap-2"><i class="fas fa-users text-rose-500 text-xl"></i> Simamia Wanachama (Members)</h2>
            <div class="glass rounded-2xl overflow-x-auto shadow-xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-zinc-800 text-zinc-400 text-xs uppercase tracking-wider">
                            <th class="p-4">Jina Kamili / Stage Name</th>
                            <th class="p-4">Dance Style</th>
                            <th class="p-4">Namba ya Simu</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Vitendo (Actions)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-800 text-sm">
                        <?php foreach($users as $u): ?>
                        <tr class="hover:bg-zinc-900/45 transition">
                            <td class="p-4 font-semibold"><?= htmlspecialchars($u['full_name']) ?> <br><span class="text-rose-400 text-xs font-normal">(<?= htmlspecialchars($u['stage_name']) ?>)</span></td>
                            <td class="p-4 text-zinc-300"><?= htmlspecialchars($u['dance_style']) ?></td>
                            <td class="p-4 text-zinc-300"><?= htmlspecialchars($u['phone']) ?></td>
                            <td class="p-4">
                                <?php if($u['status'] == 'active'): ?>
                                    <span class="bg-emerald-500/20 text-emerald-400 px-3 py-1 rounded-full text-xs font-medium">Active</span>
                                <?php else: ?>
                                    <span class="bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-full text-xs font-medium">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 space-x-2">
                                <?php if($u['status'] == 'pending'): ?>
                                    <a href="?approve_user=<?= $u['id'] ?>" class="bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">Approve</a>
                                <?php endif; ?>
                                <a href="?delete_user=<?= $u['id'] ?>" onclick="return confirm('Una uhakika unataka kumfuta mwanachama huyu?')" class="bg-red-600/80 hover:bg-red-700 px-3 py-1.5 rounded-lg text-xs font-semibold transition">Futa</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($users)): ?>
                        <tr><td colspan="5" class="p-4 text-center text-zinc-500">Hakuna wanachama waliosajiliwa bado.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- 2. UPLOAD MEMORIES -->
        <section id="upload" class="glass p-6 md:p-8 rounded-2xl max-w-2xl shadow-xl">
            <h2 class="text-2xl font-bold mb-2 flex items-center gap-2"><i class="fas fa-photo-video text-purple-500 text-xl"></i> Weka Memory Mpya</h2>
            <p class="text-zinc-400 text-sm mb-6">Pakia picha au video ya matukio au mazoezi ili yaonekane kwenye tovuti ya mbele.</p>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label class="text-xs text-zinc-400 mb-1 block">Kichwa cha Habari / Tukio</label>
                    <input type="text" name="title" placeholder="mf. Show Kali Babati Mjini" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:border-purple-500 focus:outline-none">
                </div>
                <div>
                    <label class="text-xs text-zinc-400 mb-1 block">Maelezo Mafupi</label>
                    <textarea name="description" placeholder="Andika maelezo mafupi kuhusu tukio hili..." rows="2" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:border-purple-500 focus:outline-none"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs text-zinc-400 mb-1 block">Aina ya Faili</label>
                        <select name="media_type" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:border-purple-500 focus:outline-none">
                            <option value="image">Picha (Image)</option>
                            <option value="video">Video (MP4)</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-zinc-400 mb-1 block">Tarehe ya Tukio</label>
                        <input type="date" name="event_date" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 focus:border-purple-500 focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="text-xs text-zinc-400 mb-1 block">Chagua Faili kutoka kwenye Kifaa chako</label>
                    <input type="file" name="media_file" accept="image/*,video/mp4" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-2.5 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                </div>
                <button type="submit" name="upload_memory" class="w-full bg-gradient-to-r from-rose-500 to-purple-600 py-3.5 rounded-xl font-bold shadow-lg shadow-purple-500/20 hover:opacity-90 transition">Pakia Kwenye Mfumo</button>
            </form>
        </section>

        <!-- 3. MANAGE REVIEWS -->
        <section id="reviews">
            <h2 class="text-2xl font-bold mb-4 flex items-center gap-2"><i class="fas fa-star text-yellow-400 text-xl"></i> Simamia Event Reviews</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach($reviews as $r): ?>
                <div class="glass p-5 rounded-2xl flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-base"><?= htmlspecialchars($r['reviewer_name']) ?> <span class="text-xs text-rose-400 font-normal">(Tukio: <?= htmlspecialchars($r['event_name']) ?>)</span></h4>
                        <div class="text-yellow-400 text-xs my-1"><?= str_repeat('⭐', $r['rating']) ?> (<?= $r['rating'] ?>/5)</div>
                        <p class="text-zinc-300 text-sm mt-2 italic">"<?= htmlspecialchars($r['comment']) ?>"</p>
                    </div>
                    <div class="flex flex-col gap-2 ml-4">
                        <?php if(!$r['is_approved']): ?>
                            <a href="?approve_review=<?= $r['id'] ?>" class="bg-emerald-600 hover:bg-emerald-700 px-3 py-1.5 rounded-lg text-xs font-semibold text-center transition">Approve</a>
                        <?php else: ?>
                            <span class="text-xs text-emerald-400 font-medium text-center bg-emerald-500/10 px-2 py-1 rounded">Approved</span>
                        <?php endif; ?>
                        <a href="?delete_review=<?= $r['id'] ?>" onclick="return confirm('Futa review hii?')" class="bg-red-600/80 hover:bg-red-700 px-3 py-1.5 rounded-lg text-xs font-semibold text-center transition">Futa</a>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if(empty($reviews)): ?>
                <p class="text-zinc-500 col-span-2">Hakuna reviews zilizowekwa bado.</p>
                <?php endif; ?>
            </div>
        </section>

    </main>

</body>
</html>