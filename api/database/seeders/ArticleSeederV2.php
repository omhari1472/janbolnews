<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleSeederV2 extends Seeder {
    public function run(): void {
        $cats    = DB::table('categories')->pluck('id', 'slug');
        $authors = DB::table('authors')->pluck('id')->toArray();
        $now     = now();

        $articles = [
            // ── POLITICS ──
            ['title_hi'=>'प्रधानमंत्री मोदी ने किया 10 नए AIIMS का शिलान्यास','title_en'=>'PM Modi Lays Foundation for 10 New AIIMS','slug'=>'pm-modi-10-new-aiims-foundation','excerpt_hi'=>'देशभर में स्वास्थ्य सेवाओं को बेहतर बनाने के लिए प्रधानमंत्री ने 10 नए AIIMS का शिलान्यास किया।','content_hi'=>'<p>नई दिल्ली: प्रधानमंत्री नरेंद्र मोदी ने आज देश के विभिन्न राज्यों में 10 नए अखिल भारतीय आयुर्विज्ञान संस्थान (AIIMS) की आधारशिला रखी। इन संस्थानों के निर्माण पर 25,000 करोड़ रुपये खर्च होंगे।</p><p>प्रत्येक नए AIIMS में 750 बिस्तरों की व्यवस्था होगी। इन संस्थानों में MBBS, MD और PhD पाठ्यक्रम चलाए जाएंगे।</p>','featured_image'=>'https://images.unsplash.com/photo-1587351021759-3e566b3db4f1?w=800&q=80','category_id'=>$cats['politics']??1,'is_featured'=>1,'is_breaking'=>0,'hours_ago'=>3],
            ['title_hi'=>'विपक्ष ने संसद में उठाया महंगाई का मुद्दा, हंगामा','title_en'=>'Opposition Raises Inflation Issue in Parliament','slug'=>'opposition-inflation-parliament-uproar','excerpt_hi'=>'संसद के मानसून सत्र में विपक्षी दलों ने बढ़ती महंगाई को लेकर जोरदार हंगामा किया।','content_hi'=>'<p>नई दिल्ली: संसद के मानसून सत्र में आज विपक्षी दलों ने बढ़ती महंगाई के मुद्दे पर जोरदार हंगामा किया। कांग्रेस और सपा के सांसद वेल में उतर आए।</p><p>विपक्ष का आरोप है कि पिछले 6 महीनों में खाद्य पदार्थों की कीमतें 30% तक बढ़ी हैं।</p>','featured_image'=>'https://images.unsplash.com/photo-1529107386315-e1a2ed48a620?w=800&q=80','category_id'=>$cats['politics']??1,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>6],
            ['title_hi'=>'नई शिक्षा नीति: सरकारी स्कूलों में अब AI की पढ़ाई','title_en'=>'New Education Policy: AI in Government Schools','slug'=>'new-education-policy-ai-schools','excerpt_hi'=>'केंद्र सरकार ने सरकारी स्कूलों में आर्टिफिशियल इंटेलिजेंस की पढ़ाई शुरू करने का ऐलान किया।','content_hi'=>'<p>नई दिल्ली: शिक्षा मंत्रालय ने घोषणा की कि अगले सत्र से कक्षा 6 से AI की पढ़ाई शुरू होगी। 50,000 शिक्षकों को प्रशिक्षित किया जाएगा।</p>','featured_image'=>'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=800&q=80','category_id'=>$cats['politics']??1,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>10],

            // ── SPORTS ──
            ['title_hi'=>'IPL 2026: मुंबई इंडियंस ने जीता खिताब, रोहित बने MVP','title_en'=>'IPL 2026: Mumbai Indians Win Title, Rohit Named MVP','slug'=>'ipl-2026-mumbai-indians-champions','excerpt_hi'=>'IPL 2026 के फाइनल में मुंबई इंडियंस ने चेन्नई सुपर किंग्स को 8 विकेट से हराया।','content_hi'=>'<p>अहमदाबाद: मुंबई इंडियंस ने रिकॉर्ड छठी बार IPL खिताब जीता। रोहित शर्मा ने 65 रन की नाबाद पारी खेली।</p>','featured_image'=>'https://images.unsplash.com/photo-1531415074968-036ba1b575da?w=800&q=80','category_id'=>$cats['sports']??2,'is_featured'=>1,'is_breaking'=>0,'hours_ago'=>2],
            ['title_hi'=>'फीफा वर्ल्ड कप 2026: भारत ने किया ऐतिहासिक क्वालिफाई','title_en'=>'FIFA World Cup 2026: India Makes Historic Qualification','slug'=>'fifa-worldcup-2026-india-qualifies','excerpt_hi'=>'भारतीय फुटबॉल टीम ने पहली बार FIFA वर्ल्ड कप के लिए क्वालिफाई किया।','content_hi'=>'<p>मुंबई: भारत ने पहली बार FIFA वर्ल्ड कप 2026 के लिए क्वालिफाई किया। एशियाई क्वालिफायर में 2-0 की जीत।</p>','featured_image'=>'https://images.unsplash.com/photo-1575361204480-aadea25e6e68?w=800&q=80','category_id'=>$cats['sports']??2,'is_featured'=>0,'is_breaking'=>1,'hours_ago'=>4],
            ['title_hi'=>'पीवी सिंधु ने जीता ऑल इंग्लैंड बैडमिंटन खिताब','title_en'=>'PV Sindhu Wins All England Badminton Title','slug'=>'pv-sindhu-all-england-2026','excerpt_hi'=>'पीवी सिंधु ने ऑल इंग्लैंड चैंपियनशिप का खिताब जीतकर देश का नाम रोशन किया।','content_hi'=>'<p>बर्मिंघम: पीवी सिंधु ने फाइनल में चीन की चेन यूफेई को 21-18, 21-15 से हराया।</p>','featured_image'=>'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=800&q=80','category_id'=>$cats['sports']??2,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>8],

            // ── ENTERTAINMENT ──
            ['title_hi'=>'अनुपम खेर को मिला दादा साहेब फाल्के पुरस्कार','title_en'=>'Anupam Kher Receives Dadasaheb Phalke Award','slug'=>'anupam-kher-dadasaheb-phalke','excerpt_hi'=>'अनुपम खेर को भारतीय सिनेमा के सर्वोच्च सम्मान से नवाजा गया।','content_hi'=>'<p>नई दिल्ली: 500 से अधिक फिल्मों में काम करने वाले अनुपम खेर को दादा साहेब फाल्के पुरस्कार।</p>','featured_image'=>'https://images.unsplash.com/photo-1524712245354-2c4e5e7121c0?w=800&q=80','category_id'=>$cats['entertainment']??3,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>5],
            ['title_hi'=>'सनी देओल की वापसी: गदर 3 का आधिकारिक ऐलान','title_en'=>'Sunny Deol Returns: Gadar 3 Officially Announced','slug'=>'sunny-deol-gadar-3-official','excerpt_hi'=>'सनी देओल ने गदर 3 की आधिकारिक घोषणा की। फिल्म 2027 में रिलीज़ होगी।','content_hi'=>'<p>मुंबई: गदर 3 स्वतंत्रता दिवस 2027 को रिलीज़ होगी। सनी देओल और अमीषा पटेल फिर साथ।</p>','featured_image'=>'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=800&q=80','category_id'=>$cats['entertainment']??3,'is_featured'=>1,'is_breaking'=>0,'hours_ago'=>7],
            ['title_hi'=>'OTT पर नई वेब सीरीज़ ने 24 घंटे में तोड़े सारे रिकॉर्ड','title_en'=>'New OTT Web Series Breaks All Records in 24 Hours','slug'=>'ott-web-series-record-breaking','excerpt_hi'=>'नेटफ्लिक्स पर हिंदी वेब सीरीज़ ने 24 घंटे में 50 लाख व्यूज़ का रिकॉर्ड बनाया।','content_hi'=>'<p>मुंबई: नेटफ्लिक्स पर नई हिंदी वेब सीरीज़ ने किसी भी भारतीय कंटेंट का सबसे तेज़ 50 लाख व्यूज़ का रिकॉर्ड बनाया।</p>','featured_image'=>'https://images.unsplash.com/photo-1485846234645-a62644f84728?w=800&q=80','category_id'=>$cats['entertainment']??3,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>9],

            // ── BUSINESS ──
            ['title_hi'=>'जियो ने लॉन्च किया 5G फोन, कीमत मात्र 2999 रुपये','title_en'=>'Jio Launches 5G Phone at Just Rs 2999','slug'=>'jio-5g-phone-launch-rs-2999','excerpt_hi'=>'रिलायंस जियो ने भारत का सबसे सस्ता 5G स्मार्टफोन लॉन्च किया।','content_hi'=>'<p>मुंबई: JioPhone 5G में 4000mAh बैटरी, 32GB स्टोरेज। पहले दिन 50 लाख प्री-ऑर्डर।</p>','featured_image'=>'https://images.unsplash.com/photo-1585060544812-6b45742d762f?w=800&q=80','category_id'=>$cats['business']??4,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>4],
            ['title_hi'=>'भारत बना दुनिया की तीसरी सबसे बड़ी अर्थव्यवस्था','title_en'=>'India Becomes World Third Largest Economy','slug'=>'india-third-largest-economy-gdp','excerpt_hi'=>'IMF की रिपोर्ट में भारत को दुनिया की तीसरी सबसे बड़ी अर्थव्यवस्था घोषित किया गया।','content_hi'=>'<p>वाशिंगटन: IMF ने भारत की GDP 5.5 ट्रिलियन डॉलर पर पहुँचने के साथ उसे जापान से आगे तीसरी सबसे बड़ी अर्थव्यवस्था घोषित किया।</p>','featured_image'=>'https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?w=800&q=80','category_id'=>$cats['business']??4,'is_featured'=>1,'is_breaking'=>1,'hours_ago'=>1],
            ['title_hi'=>'Amazon भारत में खोलेगा 50000 नए रोजगार','title_en'=>'Amazon to Create 50000 New Jobs in India','slug'=>'amazon-india-50000-jobs','excerpt_hi'=>'अमेजन ने भारत में कारोबार बढ़ाते हुए नए रोजगार देने की घोषणा की।','content_hi'=>'<p>बेंगलुरु: अमेजन इंडिया अगले 2 वर्षों में लॉजिस्टिक्स, टेक्नोलॉजी और कस्टमर सर्विस में 50,000 नए रोजगार देगा।</p>','featured_image'=>'https://images.unsplash.com/photo-1523474438810-b04a2480633c?w=800&q=80','category_id'=>$cats['business']??4,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>11],

            // ── TECHNOLOGY ──
            ['title_hi'=>'IIT दिल्ली ने बनाया भारत का पहला क्वांटम कंप्यूटर','title_en'=>'IIT Delhi Builds India First Quantum Computer','slug'=>'iit-delhi-quantum-computer-first','excerpt_hi'=>'IIT दिल्ली ने भारत का पहला स्वदेशी 100 क्यूबिट क्वांटम कंप्यूटर विकसित किया।','content_hi'=>'<p>नई दिल्ली: IIT दिल्ली का 100 क्यूबिट क्वांटम कंप्यूटर साइबर सुरक्षा और ड्रग डिस्कवरी में क्रांति लाएगा।</p>','featured_image'=>'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&q=80','category_id'=>$cats['technology']??5,'is_featured'=>1,'is_breaking'=>0,'hours_ago'=>3],
            ['title_hi'=>'नई बैटरी तकनीक से स्मार्टफोन अब 30 दिन चलेगा','title_en'=>'New Battery Technology Makes Smartphone Last 30 Days','slug'=>'new-battery-tech-30-days-smartphone','excerpt_hi'=>'वैज्ञानिकों ने सॉलिड-स्टेट बैटरी तकनीक विकसित की जिससे स्मार्टफोन 30 दिन चलेगा।','content_hi'=>'<p>बेंगलुरु: भारतीय वैज्ञानिकों की नई सॉलिड-स्टेट बैटरी अगले 2 वर्षों में व्यावसायिक उत्पादन के लिए तैयार होगी।</p>','featured_image'=>'https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?w=800&q=80','category_id'=>$cats['technology']??5,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>6],
            ['title_hi'=>'Google ने लॉन्च किया हिंदी AI Assistant','title_en'=>'Google Launches Hindi AI Assistant for India','slug'=>'google-hindi-ai-assistant-india','excerpt_hi'=>'गूगल ने हिंदी और 12 भारतीय भाषाओं में काम करने वाला AI असिस्टेंट लॉन्च किया।','content_hi'=>'<p>बेंगलुरु: 50 करोड़ हिंदी भाषियों के लिए गूगल का नया AI असिस्टेंट। 12 भारतीय भाषाओं में काम करेगा।</p>','featured_image'=>'https://images.unsplash.com/photo-1677442135703-1787eea5ce01?w=800&q=80','category_id'=>$cats['technology']??5,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>8],

            // ── STATE ──
            ['title_hi'=>'राजस्थान में मिला विशाल तेल भंडार, देश के लिए ऐतिहासिक खोज','title_en'=>'Massive Oil Reserve Found in Rajasthan','slug'=>'rajasthan-oil-reserve-discovery','excerpt_hi'=>'राजस्थान के बाड़मेर में 500 मिलियन बैरल तेल का भंडार मिला।','content_hi'=>'<p>जयपुर: ONGC को बाड़मेर में 500 मिलियन बैरल तेल का भंडार मिला। अगले 50 वर्षों तक भारत की जरूरतें पूरी हो सकती हैं।</p>','featured_image'=>'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=800&q=80','category_id'=>$cats['state']??6,'is_featured'=>1,'is_breaking'=>0,'hours_ago'=>5],
            ['title_hi'=>'मध्य प्रदेश की नई औद्योगिक नीति से 5 लाख रोजगार','title_en'=>'MP New Industrial Policy to Create 5 Lakh Jobs','slug'=>'mp-industrial-policy-jobs','excerpt_hi'=>'मध्य प्रदेश की नई नीति में IT, फार्मा और ऑटो सेक्टर पर ध्यान।','content_hi'=>'<p>भोपाल: मध्य प्रदेश की नई औद्योगिक नीति 2026 से अगले 5 वर्षों में 5 लाख रोजगार सृजित होंगे।</p>','featured_image'=>'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800&q=80','category_id'=>$cats['state']??6,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>9],
            ['title_hi'=>'पंजाब पुलिस का बड़ा ऑपरेशन, 500 नशा तस्कर गिरफ्तार','title_en'=>'Punjab Police Operation, 500 Drug Traffickers Arrested','slug'=>'punjab-police-drug-operation','excerpt_hi'=>'पंजाब पुलिस ने राज्यव्यापी अभियान में 500 तस्करों को पकड़ा।','content_hi'=>'<p>चंडीगढ़: पंजाब पुलिस के ऑपरेशन में 500 तस्कर गिरफ्तार, 200 किलो हेरोइन जब्त।</p>','featured_image'=>'https://images.unsplash.com/photo-1453873623425-04e3561289aa?w=800&q=80','category_id'=>$cats['state']??6,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>12],

            // ── WORLD ──
            ['title_hi'=>'चीन ने लॉन्च किया मून बेस मिशन','title_en'=>'China Launches Moon Base Mission','slug'=>'china-moon-base-mission','excerpt_hi'=>'चीन ने 2030 तक चंद्रमा पर मानव बस्ती बनाने के लिए मिशन लॉन्च किया।','content_hi'=>'<p>बीजिंग: चांग ई-7 मिशन लॉन्च। 2030 तक चंद्रमा पर स्थायी बेस का लक्ष्य।</p>','featured_image'=>'https://images.unsplash.com/photo-1446776811953-b23d57bd21aa?w=800&q=80','category_id'=>$cats['world']??7,'is_featured'=>1,'is_breaking'=>0,'hours_ago'=>4],
            ['title_hi'=>'G20 में भारत की पहल पर 100 अरब का जलवायु फंड','title_en'=>'G20 Approves 100 Billion Climate Fund on India Initiative','slug'=>'g20-climate-fund-india-initiative','excerpt_hi'=>'G20 शिखर सम्मेलन में भारत की अध्यक्षता में जलवायु परिवर्तन पर ऐतिहासिक फैसला।','content_hi'=>'<p>नई दिल्ली: G20 में 100 अरब डॉलर का जलवायु फंड स्वीकृत। भारत ने अहम भूमिका निभाई।</p>','featured_image'=>'https://images.unsplash.com/photo-1569144157591-c60f3f82f137?w=800&q=80','category_id'=>$cats['world']??7,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>7],
            ['title_hi'=>'अमेरिका में भारतीय मूल की नेत्री बन सकती हैं पहली राष्ट्रपति','title_en'=>'Indian-Origin Leader Could Become First US President','slug'=>'indian-origin-first-us-president','excerpt_hi'=>'अमेरिका चुनाव 2028 में भारतीय मूल की उम्मीदवार सबसे आगे।','content_hi'=>'<p>वाशिंगटन: ताजा सर्वे में भारतीय मूल की उम्मीदवार को 45% समर्थन। इतिहास बनने की संभावना।</p>','featured_image'=>'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?w=800&q=80','category_id'=>$cats['world']??7,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>10],

            // ── HEALTH ──
            ['title_hi'=>'योग से ठीक हुई असाध्य बीमारी, IIT का चौंकाने वाला शोध','title_en'=>'Yoga Cures Incurable Disease as per IIT Research','slug'=>'yoga-cures-disease-iit-research','excerpt_hi'=>'IIT मुंबई के शोध में 78% मरीज योग से दवाओं के बिना ठीक हुए।','content_hi'=>'<p>मुंबई: 500 मरीजों पर शोध में 78% ने नियमित योग से टाइप-2 डायबिटीज और उच्च रक्तचाप से राहत पाई।</p>','featured_image'=>'https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=800&q=80','category_id'=>$cats['health']??8,'is_featured'=>1,'is_breaking'=>0,'hours_ago'=>5],
            ['title_hi'=>'आयुष्मान भारत की सीमा बढ़ी, 10 लाख तक मुफ्त इलाज','title_en'=>'Ayushman Bharat Limit Raised to Rs 10 Lakh','slug'=>'ayushman-bharat-limit-10-lakh','excerpt_hi'=>'75 करोड़ भारतीयों को अब 10 लाख रुपये तक मुफ्त इलाज मिलेगा।','content_hi'=>'<p>नई दिल्ली: सरकार ने आयुष्मान भारत की सीमा 5 लाख से बढ़ाकर 10 लाख रुपये की।</p>','featured_image'=>'https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?w=800&q=80','category_id'=>$cats['health']??8,'is_featured'=>0,'is_breaking'=>0,'hours_ago'=>8],
            ['title_hi'=>'केरल में मंकीपॉक्स का पहला मामला, स्वास्थ्य विभाग अलर्ट पर','title_en'=>'First Monkeypox Case in Kerala, Health Alert Issued','slug'=>'kerala-monkeypox-first-case','excerpt_hi'=>'केरल में मंकीपॉक्स का पहला मामला। स्वास्थ्य मंत्रालय ने सभी राज्यों को अलर्ट किया।','content_hi'=>'<p>तिरुवनंतपुरम: विदेश से लौटे व्यक्ति में मंकीपॉक्स की पुष्टि। सभी राज्यों को अलर्ट जारी।</p>','featured_image'=>'https://images.unsplash.com/photo-1584820927498-cfe5211fd8bf?w=800&q=80','category_id'=>$cats['health']??8,'is_featured'=>0,'is_breaking'=>1,'hours_ago'=>2],
        ];

        foreach ($articles as $a) {
            $pub = $now->copy()->subHours($a['hours_ago']);
            DB::table('articles')->insertOrIgnore([
                'title_hi'       => $a['title_hi'],
                'title_en'       => $a['title_en'],
                'slug'           => $a['slug'],
                'excerpt_hi'     => $a['excerpt_hi'],
                'content_hi'     => $a['content_hi'],
                'featured_image' => $a['featured_image'],
                'category_id'    => $a['category_id'],
                'author_id'      => $authors[array_rand($authors)] ?? null,
                'status'         => 'published',
                'language'       => 'both',
                'is_featured'    => $a['is_featured'],
                'is_breaking'    => $a['is_breaking'],
                'views'          => rand(200, 25000),
                'published_at'   => $pub,
                'created_at'     => $pub,
                'updated_at'     => $pub,
            ]);
            echo '.';
        }
        echo "\n" . count($articles) . " articles seeded.\n";
    }
}
