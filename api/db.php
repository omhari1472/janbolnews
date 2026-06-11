<?php
// ── Janbolnews — Database Setup ──────────────────────────────────────
require_once __DIR__ . '/config.php';

function getDb(): PDO {
    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,          PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA journal_mode=WAL; PRAGMA foreign_keys=ON;');

    // ── Schema ────────────────────────────────────────────────────────
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS categories (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            name_hi     TEXT NOT NULL,
            name_en     TEXT NOT NULL,
            slug        TEXT UNIQUE NOT NULL,
            parent_id   INTEGER DEFAULT NULL,
            icon        TEXT DEFAULT '',
            color       TEXT DEFAULT '#e53e3e',
            sort_order  INTEGER DEFAULT 0,
            is_active   INTEGER DEFAULT 1,
            FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS authors (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            name        TEXT NOT NULL,
            bio_hi      TEXT DEFAULT '',
            bio_en      TEXT DEFAULT '',
            avatar      TEXT DEFAULT '',
            email       TEXT DEFAULT '',
            is_active   INTEGER DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS articles (
            id              INTEGER PRIMARY KEY AUTOINCREMENT,
            title_hi        TEXT NOT NULL DEFAULT '',
            title_en        TEXT NOT NULL DEFAULT '',
            slug            TEXT UNIQUE NOT NULL,
            content_hi      TEXT DEFAULT '',
            content_en      TEXT DEFAULT '',
            excerpt_hi      TEXT DEFAULT '',
            excerpt_en      TEXT DEFAULT '',
            featured_image  TEXT DEFAULT '',
            category_id     INTEGER DEFAULT NULL,
            author_id       INTEGER DEFAULT NULL,
            status          TEXT DEFAULT 'draft' CHECK(status IN ('draft','published','scheduled')),
            is_featured     INTEGER DEFAULT 0,
            is_breaking     INTEGER DEFAULT 0,
            language        TEXT DEFAULT 'both' CHECK(language IN ('hi','en','both')),
            views           INTEGER DEFAULT 0,
            published_at    TEXT DEFAULT NULL,
            created_at      TEXT DEFAULT (datetime('now')),
            updated_at      TEXT DEFAULT (datetime('now')),
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
            FOREIGN KEY (author_id)   REFERENCES authors(id)   ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS tags (
            id      INTEGER PRIMARY KEY AUTOINCREMENT,
            name_hi TEXT NOT NULL DEFAULT '',
            name_en TEXT NOT NULL DEFAULT '',
            slug    TEXT UNIQUE NOT NULL
        );

        CREATE TABLE IF NOT EXISTS article_tags (
            article_id  INTEGER NOT NULL,
            tag_id      INTEGER NOT NULL,
            PRIMARY KEY (article_id, tag_id),
            FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
            FOREIGN KEY (tag_id)     REFERENCES tags(id)     ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS breaking_news (
            id          INTEGER PRIMARY KEY AUTOINCREMENT,
            text_hi     TEXT NOT NULL DEFAULT '',
            text_en     TEXT NOT NULL DEFAULT '',
            url         TEXT DEFAULT '',
            is_active   INTEGER DEFAULT 1,
            sort_order  INTEGER DEFAULT 0,
            created_at  TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS epapers (
            id              INTEGER PRIMARY KEY AUTOINCREMENT,
            title           TEXT NOT NULL,
            edition         TEXT DEFAULT '',
            city            TEXT DEFAULT '',
            date            TEXT NOT NULL,
            pdf_path        TEXT DEFAULT '',
            thumbnail_path  TEXT DEFAULT '',
            total_pages     INTEGER DEFAULT 0,
            is_active       INTEGER DEFAULT 1,
            created_at      TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS admin_users (
            id              INTEGER PRIMARY KEY AUTOINCREMENT,
            name            TEXT NOT NULL,
            email           TEXT UNIQUE NOT NULL,
            password_hash   TEXT NOT NULL,
            role            TEXT DEFAULT 'reporter' CHECK(role IN ('super','editor','reporter')),
            is_active       INTEGER DEFAULT 1,
            created_at      TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS media (
            id              INTEGER PRIMARY KEY AUTOINCREMENT,
            filename        TEXT NOT NULL,
            original_name   TEXT NOT NULL,
            path            TEXT NOT NULL,
            mime_type       TEXT DEFAULT '',
            size            INTEGER DEFAULT 0,
            alt_text        TEXT DEFAULT '',
            created_at      TEXT DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS site_settings (
            key     TEXT PRIMARY KEY,
            value   TEXT NOT NULL DEFAULT ''
        );

        CREATE TABLE IF NOT EXISTS article_views (
            article_id  INTEGER NOT NULL,
            ip          TEXT NOT NULL,
            created_at  TEXT DEFAULT (datetime('now')),
            FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
        );

        CREATE INDEX IF NOT EXISTS idx_articles_status      ON articles(status);
        CREATE INDEX IF NOT EXISTS idx_articles_category    ON articles(category_id);
        CREATE INDEX IF NOT EXISTS idx_articles_slug        ON articles(slug);
        CREATE INDEX IF NOT EXISTS idx_articles_published   ON articles(published_at);
        CREATE INDEX IF NOT EXISTS idx_articles_featured    ON articles(is_featured);
        CREATE INDEX IF NOT EXISTS idx_articles_breaking    ON articles(is_breaking);
        CREATE INDEX IF NOT EXISTS idx_article_views_art    ON article_views(article_id);
        CREATE INDEX IF NOT EXISTS idx_breaking_active      ON breaking_news(is_active, sort_order);
        CREATE INDEX IF NOT EXISTS idx_epapers_date         ON epapers(date);
        CREATE INDEX IF NOT EXISTS idx_categories_slug      ON categories(slug);
    ");

    // ── Seed data (only if empty) ──────────────────────────────────────
    seedData($pdo);

    return $pdo;
}

function seedData(PDO $pdo): void {
    // ── Categories ────────────────────────────────────────────────────
    $catCount = (int)$pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($catCount === 0) {
        $cats = [
            ['राजनीति', 'Politics',      'politics',      '#dc2626', '🏛️', 1],
            ['खेल',     'Sports',        'sports',         '#16a34a', '⚽', 2],
            ['मनोरंजन', 'Entertainment', 'entertainment',  '#9333ea', '🎬', 3],
            ['व्यापार',  'Business',      'business',       '#d97706', '💼', 4],
            ['तकनीक',   'Technology',    'technology',     '#0891b2', '💻', 5],
            ['राज्य',   'State',         'state',          '#65a30d', '🗺️', 6],
            ['विदेश',   'World',         'world',          '#7c3aed', '🌍', 7],
            ['स्वास्थ्य', 'Health',       'health',         '#0d9488', '❤️', 8],
        ];
        $ins = $pdo->prepare(
            "INSERT INTO categories (name_hi, name_en, slug, color, icon, sort_order, is_active) VALUES (?,?,?,?,?,?,1)"
        );
        foreach ($cats as $c) {
            $ins->execute($c);
        }
    }

    // ── Authors ───────────────────────────────────────────────────────
    $authCount = (int)$pdo->query("SELECT COUNT(*) FROM authors")->fetchColumn();
    if ($authCount === 0) {
        $pdo->exec("INSERT INTO authors (name, bio_hi, bio_en, email, is_active) VALUES
            ('संपादक मंडल', 'जनबोलन्यूज़ संपादकीय टीम', 'Janbolnews Editorial Team', 'editor@janbolnews.com', 1),
            ('अमित शर्मा',  'वरिष्ठ पत्रकार', 'Senior Journalist', 'amit@janbolnews.com', 1)
        ");
    }

    // ── Admin user ─────────────────────────────────────────────────────
    $adminCount = (int)$pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
    if ($adminCount === 0) {
        $hash = password_hash('janbolnews2026', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO admin_users (name, email, password_hash, role, is_active) VALUES (?,?,?,?,1)")
            ->execute(['Super Admin', 'admin@janbolnews.com', $hash, 'super']);
    }

    // ── Breaking news ─────────────────────────────────────────────────
    $bnCount = (int)$pdo->query("SELECT COUNT(*) FROM breaking_news")->fetchColumn();
    if ($bnCount === 0) {
        $pdo->exec("INSERT INTO breaking_news (text_hi, text_en, is_active, sort_order) VALUES
            ('ताज़ा खबर: देश में नए आर्थिक सुधारों की घोषणा', 'Breaking: Government announces new economic reforms', 1, 1),
            ('अपडेट: राज्य विधानसभा में बजट पारित', 'Update: State assembly passes budget', 1, 2)
        ");
    }

    // ── Sample articles ───────────────────────────────────────────────
    $artCount = (int)$pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    if ($artCount === 0) {
        $politicsId = (int)$pdo->query("SELECT id FROM categories WHERE slug='politics' LIMIT 1")->fetchColumn();
        $sportsId   = (int)$pdo->query("SELECT id FROM categories WHERE slug='sports'   LIMIT 1")->fetchColumn();
        $techId     = (int)$pdo->query("SELECT id FROM categories WHERE slug='technology' LIMIT 1")->fetchColumn();
        $authorId   = (int)$pdo->query("SELECT id FROM authors LIMIT 1")->fetchColumn();

        $ins = $pdo->prepare("
            INSERT INTO articles
                (title_hi, title_en, slug, content_hi, content_en, excerpt_hi, excerpt_en,
                 category_id, author_id, status, is_featured, language, views, published_at)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,datetime('now'))
        ");

        $ins->execute([
            'सरकार ने नई शिक्षा नीति की घोषणा की',
            'Government Announces New Education Policy',
            'government-announces-new-education-policy',
            '<p>केंद्र सरकार ने आज एक नई राष्ट्रीय शिक्षा नीति की घोषणा की जो देश भर के स्कूलों और कॉलेजों को प्रभावित करेगी। इस नीति के तहत पाठ्यक्रम में व्यापक बदलाव किए जाएंगे।</p><p>शिक्षा मंत्री ने बताया कि यह नीति बच्चों की समग्र विकास पर ध्यान केंद्रित करेगी और डिजिटल शिक्षा को बढ़ावा देगी।</p>',
            '<p>The central government today announced a new National Education Policy that will affect schools and colleges across the country. The curriculum will undergo comprehensive changes under this policy.</p><p>The Education Minister stated that this policy will focus on holistic development of children and promote digital education.</p>',
            'केंद्र सरकार ने नई राष्ट्रीय शिक्षा नीति की घोषणा की',
            'Central government announces a new National Education Policy',
            $politicsId, $authorId, 'published', 1, 'both', 145,
        ]);

        $ins->execute([
            'भारत ने क्रिकेट सीरीज़ में ऑस्ट्रेलिया को हराया',
            'India Beats Australia in Cricket Series',
            'india-beats-australia-cricket-series',
            '<p>भारतीय क्रिकेट टीम ने शानदार प्रदर्शन करते हुए ऑस्ट्रेलिया को तीन मैचों की सीरीज़ में 2-1 से हराया। कप्तान रोहित शर्मा ने इस जीत का नेतृत्व किया।</p><p>मैच के सर्वश्रेष्ठ खिलाड़ी का पुरस्कार विराट कोहली को मिला जिन्होंने 87 रन की शानदार पारी खेली।</p>',
            '<p>The Indian cricket team defeated Australia 2-1 in a three-match series with a brilliant performance. Captain Rohit Sharma led the victory.</p><p>The man of the match award went to Virat Kohli who played a brilliant innings of 87 runs.</p>',
            'भारत ने ऑस्ट्रेलिया को सीरीज़ में 2-1 से हराया',
            'India defeats Australia 2-1 in the series',
            $sportsId, $authorId, 'published', 0, 'both', 230,
        ]);

        $ins->execute([
            'भारत में 5G नेटवर्क का विस्तार तेज़',
            '5G Network Expansion Accelerates in India',
            '5g-network-expansion-accelerates-india',
            '<p>देश में 5G नेटवर्क का तेज़ी से विस्तार हो रहा है। अब तक 50 से अधिक शहरों में 5G सेवाएं शुरू हो चुकी हैं।</p><p>दूरसंचार कंपनियों ने अगले एक साल में 100 और शहरों में 5G नेटवर्क लाने का लक्ष्य रखा है। इससे इंटरनेट की गति में 10 गुना वृद्धि होगी।</p>',
            '<p>5G network is expanding rapidly across the country. 5G services have already started in more than 50 cities.</p><p>Telecom companies have set a target of bringing 5G network to 100 more cities in the next one year, which will increase internet speed 10 times.</p>',
            'देश के 50 से अधिक शहरों में 5G सेवाएं शुरू',
            '5G services started in more than 50 cities across the country',
            $techId, $authorId, 'published', 0, 'both', 89,
        ]);
    }

    // ── Default site settings ─────────────────────────────────────────
    $setCount = (int)$pdo->query("SELECT COUNT(*) FROM site_settings")->fetchColumn();
    if ($setCount === 0) {
        $defaults = [
            ['site_name',        'Janbolnews'],
            ['site_tagline_hi',  'हर खबर, सबसे पहले'],
            ['site_tagline_en',  'Every News, First'],
            ['site_email',       'info@janbolnews.com'],
            ['site_phone',       ''],
            ['site_logo',        ''],
            ['site_favicon',     ''],
            ['facebook_url',     ''],
            ['twitter_url',      ''],
            ['youtube_url',      ''],
            ['instagram_url',    ''],
            ['google_analytics', ''],
            ['default_language', 'hi'],
            ['articles_per_page','20'],
        ];
        $ins = $pdo->prepare("INSERT OR IGNORE INTO site_settings (key, value) VALUES (?,?)");
        foreach ($defaults as $s) $ins->execute($s);
    }
}
