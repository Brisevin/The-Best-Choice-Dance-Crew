<?php
require 'db.php';
// Hakikisha mtumiaji amelog-in na ni user wa kawaida
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: auth.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$me = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$me->execute([$user_id]);
$my_data = $me->fetch(PDO::FETCH_ASSOC);

// Kuvuta orodha ya wanachama wengine walio active
$members = $pdo->query("SELECT full_name, stage_name, dance_style, phone FROM users WHERE status = 'active' AND role = 'user'")->fetchAll(PDO::FETCH_ASSOC);

// Kuvuta memories za hivi karibuni
$memories = $pdo->query("SELECT * FROM memories ORDER BY created_at DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard | Babati Maits Group</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        body { background-color: #09090b; color: #f4f4f5; font-family: 'Segoe UI', sans-serif; }
        .glass { background: rgba(24, 24, 27, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .gradient-text { background: linear-gradient(90deg, #f43f5e, #a855f7, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body class="flex min-h-screen">

    <!-- Modern Sidebar (Desktop) -->
    <aside class="w-64 bg-zinc-900 border-r border-zinc-800 p-6 flex flex-col justify-between hidden md:flex">
        <div>
            <div class="mb-8">
                <h1 class="text-xl font-black gradient-text tracking-wider">BABATI MAITS</h1>
                <span class="text-xs bg-rose-500/10 text-rose-400 border border-rose-500/20 px-2.5 py-0.5 rounded-full font-semibold">DANCER PORTAL</span>
            </div>
            <nav class="space-y-2">
                <a href="#profile" class="flex items-center gap-3 bg-rose-500/10 text-rose-400 px-4 py-3 rounded-xl font-semibold transition"><i class="fas fa-user-circle"></i> Wasifu Wangu</a>
                <a href="#crew" class="flex items-center gap-3 text-zinc-400 hover:bg-zinc-800 hover:text-white px-4 py-3 rounded-xl transition"><i class="fas fa-users"></i> Crew Members</a>
                <a href="#memories" class="flex items-center gap-3 text-zinc-400 hover:bg-zinc-800 hover:text-white px-4 py-3 rounded-xl transition"><i class="fas fa-photo-video"></i> Group Memories</a>
                <a href="index.php" class="flex items-center gap-3 text-zinc-400 hover:bg-zinc-800 hover:text-white px-4 py-3 rounded-xl transition"><i class="fas fa-globe"></i> Public Website</a>
            </nav>
        </div>
        <a href="logout.php" class="flex items-center gap-3 text-red-400 hover:bg-red-500/10 px-4 py-3 rounded-xl transition font-medium"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 p-6 md:p-10 overflow-y-auto space-y-10">
        
        <!-- Mobile Top Bar -->
        <div class="flex justify-between items-center md:hidden border-b border-zinc-800 pb-4">
            <h1 class="text-xl font-black gradient-text">BABATI MAITS</h1>
            <a href="logout.php" class="bg-red-600 px-4 py-2 rounded-lg text-xs font-bold">Logout</a>
        </div>

        <!-- Welcome Banner & Profile Card -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold">Karibu tena, <span class="gradient-text"><?= htmlspecialchars($my_data['stage_name']) ?></span>! 🔥</h2>
                <p class="text-zinc-400 text-sm mt-1">Hapa ndipo kitovu cha huduma na mawasiliano ya wanachama wa Babati Maits.</p>
            </div>
        </div>

        <div id="profile" class="glass p-6 md:p-8 rounded-2xl shadow-xl flex flex-wrap gap-6 items-center justify-between border border-zinc-800">
            <div>
                <span class="text-xs bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-3 py-1 rounded-full font-medium">Active Member</span>
                <h3 class="text-2xl font-bold mt-3"><?= htmlspecialchars($my_data['full_name']) ?> <span class="text-rose-400 text-lg font-normal">(<?= htmlspecialchars($my_data['stage_name']) ?>)</span></h3>
                <div class="flex flex-wrap gap-4 mt-3 text-sm text-zinc-300">
                    <p><i class="fas fa-music text-rose-500 mr-1.5"></i> Style: <strong class="text-white"><?= htmlspecialchars($my_data['dance_style']) ?></strong></p>
                    <p><i class="fas fa-phone text-purple-500 mr-1.5"></i> Simu: <strong class="text-white"><?= htmlspecialchars($my_data['phone']) ?></strong></p>
                    <p><i class="fas fa-envelope text-blue-500 mr-1.5"></i> Email: <strong class="text-white"><?= htmlspecialchars($my_data['email']) ?></strong></p>
                </div>
            </div>
        </div>

        <!-- Crew Members Section -->
        <section id="crew">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold flex items-center gap-2"><i class="fas fa-users text-rose-500"></i> Wanachama Wenzako wa Babati Maits (<?= count($members) ?>)</h3>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach($members as $m): ?>
                <div class="glass p-5 rounded-xl border border-zinc-800/80 hover:border-rose-500/50 transition">
                    <h4 class="font-bold text-rose-400 text-lg"><?= htmlspecialchars($m['stage_name']) ?></h4>
                    <p class="text-sm text-zinc-200"><?= htmlspecialchars($m['full_name']) ?></p>
                    <div class="mt-3 pt-3 border-t border-zinc-800/60 flex justify-between items-center text-xs text-zinc-400">
                        <span><i class="fas fa-fire text-amber-500 mr-1"></i> <?= htmlspecialchars($m['dance_style']) ?></span>
                        <span><i class="fas fa-phone-alt text-emerald-500 mr-1"></i> <?= htmlspecialchars($m['phone']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if(empty($members)): ?>
                <p class="text-zinc-500 col-span-3">Hakuna wanachama wengine kwa sasa.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Recent Memories Section -->
        <section id="memories">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold flex items-center gap-2"><i class="fas fa-photo-video text-purple-500"></i> Memories za Hivi Karibuni</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($memories as $mem): ?>
                <div class="glass rounded-xl overflow-hidden border border-zinc-800 flex flex-col justify-between">
                    <div class="h-48 overflow-hidden bg-black relative">
                        <?php if($mem['media_type'] == 'image'): ?>
                            <img src="<?= htmlspecialchars($mem['file_path']) ?>" class="w-full h-full object-cover hover:scale-105 transition duration-500">
                        <?php else: ?>
                            <video controls class="w-full h-full object-cover">
                                <source src="<?= htmlspecialchars($mem['file_path']) ?>" type="video/mp4">
                            </video>
                        <?php endif; ?>
                        <span class="absolute top-2 right-2 bg-black/70 px-2.5 py-1 rounded-full text-[10px] text-rose-400 font-medium">
                            <?= $mem['event_date'] ?>
                        </span>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-base mb-1"><?= htmlspecialchars($mem['title']) ?></h4>
                        <p class="text-xs text-zinc-400 leading-relaxed"><?= htmlspecialchars($mem['description']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if(empty($memories)): ?>
                <p class="text-zinc-500 col-span-3">Hakuna memories zilizowekwa bado.</p>
                <?php endif; ?>
            </div>
        </section>

    </main>

</body>
</html>