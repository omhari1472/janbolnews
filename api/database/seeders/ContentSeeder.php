<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContentSeeder extends Seeder {
    public function run(): void {
        // Categories
        $cats = [
            ['name_hi'=>'राजनीति',    'name_en'=>'Politics',      'slug'=>'politics',      'color'=>'#C41E3A'],
            ['name_hi'=>'खेल',        'name_en'=>'Sports',        'slug'=>'sports',        'color'=>'#1d4ed8'],
            ['name_hi'=>'मनोरंजन',   'name_en'=>'Entertainment', 'slug'=>'entertainment', 'color'=>'#7c3aed'],
            ['name_hi'=>'व्यापार',    'name_en'=>'Business',      'slug'=>'business',      'color'=>'#16a34a'],
            ['name_hi'=>'तकनीक',      'name_en'=>'Technology',    'slug'=>'technology',    'color'=>'#0891b2'],
            ['name_hi'=>'राज्य',      'name_en'=>'State',         'slug'=>'state',         'color'=>'#d97706'],
            ['name_hi'=>'विदेश',      'name_en'=>'World',         'slug'=>'world',         'color'=>'#dc2626'],
            ['name_hi'=>'स्वास्थ्य', 'name_en'=>'Health',        'slug'=>'health',        'color'=>'#059669'],
        ];
        foreach ($cats as $i => $c) {
            DB::table('categories')->insertOrIgnore(array_merge($c, ['sort_order'=>$i+1,'is_active'=>1,'created_at'=>now(),'updated_at'=>now()]));
        }

        // Authors
        DB::table('authors')->insertOrIgnore([
            ['name'=>'राजेश शर्मा',  'email'=>'rajesh@janbolnews.com', 'is_active'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'प्रिया सिंह',  'email'=>'priya@janbolnews.com',  'is_active'=>1,'created_at'=>now(),'updated_at'=>now()],
            ['name'=>'अमित कुमार',   'email'=>'amit@janbolnews.com',   'is_active'=>1,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // Breaking News
        $breaking = [
            ['text_hi'=>'ISRO ने चंद्रयान-4 मिशन की घोषणा की, 2027 में होगा लॉन्च', 'text_en'=>'ISRO announces Chandrayaan-4 mission, launch planned for 2027', 'is_active'=>1, 'sort_order'=>1],
            ['text_hi'=>'सेंसेक्स 500 अंक उछला, निफ्टी 24,200 के पार', 'text_en'=>'Sensex surges 500 points, Nifty crosses 24,200', 'is_active'=>1, 'sort_order'=>2],
            ['text_hi'=>'विराट कोहली ने टेस्ट क्रिकेट से संन्यास लिया', 'text_en'=>'Virat Kohli retires from Test cricket', 'is_active'=>1, 'sort_order'=>3],
            ['text_hi'=>'बिहार में बाढ़ से 12 जिले प्रभावित, सेना तैनात', 'text_en'=>'Floods affect 12 districts in Bihar, Army deployed', 'is_active'=>1, 'sort_order'=>4],
        ];
        foreach ($breaking as $b) {
            DB::table('breaking_news')->insertOrIgnore(array_merge($b, ['created_at'=>now(),'updated_at'=>now()]));
        }

        // Site Settings
        $settings = [
            ['key'=>'site_name',   'value'=>'जनबोल न्यूज़'],
            ['key'=>'site_tagline','value'=>'आपकी आवाज़, आपकी खबर'],
            ['key'=>'site_email',  'value'=>'news@janbolnews.com'],
            ['key'=>'site_phone',  'value'=>'+91 98765 43210'],
            ['key'=>'facebook',    'value'=>'https://facebook.com/janbolnews'],
            ['key'=>'twitter',     'value'=>'https://twitter.com/janbolnews'],
            ['key'=>'instagram',   'value'=>'https://instagram.com/janbolnews'],
            ['key'=>'youtube',     'value'=>'https://youtube.com/janbolnews'],
            ['key'=>'telegram',    'value'=>'https://t.me/janbolnews'],
        ];
        foreach ($settings as $s) {
            DB::table('settings')->insertOrIgnore($s);
        }
    }
}
