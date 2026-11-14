<?php
require 'db.php';

// Kupokea Review mpya kutoka kwa mgeni au shabiki
$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    $name = htmlspecialchars($_POST['reviewer_name']);
    $event = htmlspecialchars($_POST['event_name']);
    $rating = (int)$_POST['rating'];
    $comment = htmlspecialchars($_POST['comment']);

    $stmt = $pdo->prepare("INSERT INTO reviews (reviewer_name, event_name, rating, comment) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $event, $rating, $comment])) {
        $msg = "Asante! Review yako imetumwa na inasubiri kupitishwa na Admin.";
    }
}

// Kuvuta Memories (Picha na Video)
$memories = $pdo->query("SELECT * FROM memories ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Kuvuta Reviews zilizopitishwa (approved) pekee
$reviews = $pdo->query("SELECT * FROM reviews WHERE is_approved = 1 ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="sw" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Babati Maits Group | Official Dance Crew</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        body { background-color: #09090b; color: #f4f4f5; font-family: 'Segoe UI', sans-serif; }
        .glass { background: rgba(24, 24, 27, 0.7); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .gradient-text { background: linear-gradient(90deg, #f43f5e, #a855f7, #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass px-6 py-4 flex justify-between items-center border-b border-zinc-800/50">
        <a href="index.php" class="text-2xl font-black tracking-wider gradient-text">BABATI MAITS</a>
        <div class="space-x-8 hidden md:flex text-sm font-medium">
            <a href="#memories" class="hover:text-rose-500 transition">Memories</a>
            <a href="#reviews" class="hover:text-rose-500 transition">Event Reviews</a>
        </div>
        <div class="space-x-3">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="<?= $_SESSION['role'] == 'admin' ? 'admin.php' : 'dashboard.php' ?>" class="bg-purple-600 hover:bg-purple-700 px-5 py-2 rounded-full font-semibold text-sm transition shadow-lg shadow-purple-600/30">Dashboard</a>
            <?php else: ?>
                <a href="auth.php" class="bg-gradient-to-r from-rose-500 to-purple-600 hover:opacity-90 px-5 py-2 rounded-full font-semibold text-sm transition shadow-lg shadow-rose-500/30">Login / Join Crew</a>
            <?php endif; ?>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="min-h-screen flex flex-col justify-center items-center text-center px-4 pt-20 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-purple-900/30 via-zinc-950 to-zinc-950">
        <span class="px-4 py-1.5 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/20 text-xs font-semibold mb-6 animate__animated animate__fadeInDown">🔥 Best Dance Crew in Babati</span>
        <h1 class="text-5xl md:text-7xl font-black mb-6 tracking-tight animate__animated animate__zoomIn">
            FEEL THE BEAT WITH <br><span class="gradient-text">BABATI MAITS GROUP</span>
        </h1>
        <p class="max-w-2xl text-zinc-400 text-base md:text-lg mb-8 animate__animated animate__fadeInUp leading-relaxed">
            Tunafanya choreography za kisasa, maonyesho ya harusi, matamasha, na mashindano mbalimbali. Tazama kumbukumbu zetu na maoni kutoka kwa mashabiki wetu.
        </p>
        <div class="flex gap-4 animate__animated animate__fadeInUp">
            <a href="#memories" class="bg-white text-black px-8 py-3.5 rounded-full font-bold text-sm hover:bg-zinc-200 transition">Tazama Memories</a>
            <a href="auth.php" class="glass px-8 py-3.5 rounded-full font-bold text-sm hover:border-rose-500 transition">Jiunge Nasi</a>
        </div>
    </header>

    <!-- Memories Section (Picha & Video) -->
    <section id="memories" class="py-20 px-6 max-w-7xl mx-auto">
        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-bold mb-2">Group <span class="gradient-text">Memories</span></h2>
            <p class="text-zinc-400 text-sm">Matukio, mazoezi, na show zetu zilizopita</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach($memories as $m): ?>
            <div class="glass rounded-2xl overflow-hidden group hover:border-rose-500/50 transition duration-300 shadow-xl">
                <div class="h-64 overflow-hidden bg-black relative">
                    <?php if($m['media_type'] == 'image'): ?>
                        <img src="<?= htmlspecialchars($m['file_path']) ?>" alt="Memory Image" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <?php else: ?>
                        <video controls class="w-full h-full object-cover">
                            <source src="<?= htmlspecialchars($m['file_path']) ?>" type="video/mp4">
                        </video>
                    <?php endif; ?>
                    <span class="absolute top-3 right-3 bg-black/70 backdrop-blur px-3 py-1 rounded-full text-xs text-rose-400 font-medium">
                        <?= htmlspecialchars($m['event_date']) ?>
                    </span>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-2"><?= htmlspecialchars($m['title']) ?></h3>
                    <p class="text-zinc-400 text-xs leading-relaxed"><?= htmlspecialchars($m['description']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if(empty($memories)): ?>
                <p class="text-zinc-500 col-span-3 text-center py-10">Hakuna memories zilizowekwa bado.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Reviews Section -->
    <section id="reviews" class="py-20 px-6 bg-zinc-900/40 border-t border-zinc-900">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-12">
            
            <!-- Fomu ya kuandika Review -->
            <div class="glass p-6 md:p-8 rounded-2xl h-fit border border-zinc-800 shadow-xl">
                <h3 class="text-xl font-bold mb-2">Toa Maoni (Event Review)</h3>
                <p class="text-zinc-400 text-xs mb-6">Umeona show yetu? Tuambie ilikuwaje.</p>
                
                <?php if($msg): ?>
                    <div class="bg-emerald-500/20 border border-emerald-500 text-emerald-300 p-3 rounded-xl mb-4 text-xs flex items-center gap-2">
                        <i class="fas fa-check-circle text-base"></i> <span><?= $msg ?></span>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <input type="text" name="reviewer_name" placeholder="Jina lako" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-rose-500 transition">
                    </div>
                    <div>
                        <input type="text" name="event_name" placeholder="Jina la Tukio (mf. Babati Festival)" required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-rose-500 transition">
                    </div>
                    <div>
                        <select name="rating" class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-rose-500 transition text-zinc-300">
                            <option value="5">⭐⭐⭐⭐⭐ (5/5 - Kali Sana)</option>
                            <option value="4">⭐⭐⭐⭐ (4/5 - Nzuri Sana)</option>
                            <option value="3">⭐⭐⭐ (3/5 - Wastani)</option>
                        </select>
                    </div>
                    <div>
                        <textarea name="comment" rows="3" placeholder="Andika maoni yako hapa..." required class="w-full bg-zinc-950 border border-zinc-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-rose-500 transition"></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="w-full bg-gradient-to-r from-rose-500 to-purple-600 py-3.5 rounded-xl font-bold text-sm hover:opacity-90 transition shadow-lg shadow-rose-500/20">Tuma Review</button>
                </form>
            </div>

            <!-- Orodha ya Reviews zilizopitishwa -->
            <div class="lg:col-span-2 space-y-6">
                <h3 class="text-2xl md:text-3xl font-bold mb-6">Watu Wanasemaje Kuhusu <span class="gradient-text">Babati Maits?</span></h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach($reviews as $r): ?>
                    <div class="glass p-6 rounded-2xl border border-zinc-800/80 shadow-lg flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="font-bold text-base"><?= htmlspecialchars($r['reviewer_name']) ?></span>
                                <span class="text-yellow-400 text-xs"><?= str_repeat('⭐', $r['rating']) ?></span>
                            </div>
                            <p class="text-xs text-rose-400 mb-3 font-medium"><i class="fas fa-calendar-check mr-1.5"></i> <?= htmlspecialchars($r['event_name']) ?></p>
                            <p class="text-zinc-300 text-sm italic leading-relaxed">"<?= htmlspecialchars($r['comment']) ?>"</p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if(empty($reviews)): ?>
                        <p class="text-zinc-500 col-span-2">Bado hakuna reviews zilizoidhinishwa kwa sasa.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center py-8 border-t border-zinc-900 text-zinc-500 text-xs">
        &copy; <?= date('Y') ?> Babati Maits Group. Haki zote zimehifadhiwa.
    </footer>

</body>
</html>