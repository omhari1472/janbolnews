<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Article;
use App\Models\Author;
use App\Models\Tag;

class SampleArticlesSeeder extends Seeder
{
    public function run(): void
    {
        $scratchpad = '/private/tmp/claude-501/-Users-hari-Downloads/23fd73ad-5bc7-4b64-bd1d-d0a35a7b2dd7/scratchpad';
        $disk = Storage::disk('public');

        $img1 = 'articles/' . \Illuminate\Support\Str::random(40) . '.jpg';
        $img2 = 'articles/' . \Illuminate\Support\Str::random(40) . '.jpg';
        $disk->put($img1, file_get_contents($scratchpad . '/politics.jpg'));
        $disk->put($img2, file_get_contents($scratchpad . '/cricket.jpg'));

        $author1 = Author::firstOrCreate(['name' => 'हरि ओम']);
        $author2 = Author::firstOrCreate(['name' => 'संजय शर्मा']);

        $a1 = Article::create([
            'title_hi'         => 'भारत बना दुनिया की चौथी सबसे बड़ी अर्थव्यवस्था, GDP ने पार किया ऐतिहासिक मील का पत्थर',
            'title_en'         => "India Becomes World's 4th Largest Economy, GDP Crosses Historic Milestone",
            'slug'             => 'india-fourth-largest-economy-gdp-milestone-2026',
            'category_id'      => 1,
            'author_id'        => $author1->id,
            'language'         => 'both',
            'status'           => 'published',
            'is_featured'      => true,
            'is_breaking'      => true,
            'featured_image'   => $img1,
            'published_at'     => now(),
            'excerpt_hi'       => 'IMF की रिपोर्ट के अनुसार भारत ने जापान को पीछे छोड़ते हुए दुनिया की चौथी सबसे बड़ी अर्थव्यवस्था का दर्जा हासिल किया, GDP 3.7 ट्रिलियन डॉलर को पार।',
            'excerpt_en'       => "According to IMF report, India has surpassed Japan to become the world's 4th largest economy with GDP crossing 3.7 trillion dollars.",
            'content_hi'       => '<p>नई दिल्ली: भारत ने एक ऐतिहासिक उपलब्धि हासिल की है। अंतर्राष्ट्रीय मुद्रा कोष (IMF) की ताज़ा रिपोर्ट के अनुसार, भारत ने जापान को पीछे छोड़ते हुए दुनिया की चौथी सबसे बड़ी अर्थव्यवस्था बनने का दर्जा हासिल कर लिया है।</p><p>इस महत्वपूर्ण उपलब्धि पर प्रधानमंत्री नरेंद्र मोदी ने देश को बधाई देते हुए कहा कि यह 140 करोड़ भारतीयों की मेहनत और लगन का परिणाम है। उन्होंने कहा, "यह सिर्फ एक संख्या नहीं है, यह हर भारतीय के सपने का साकार होना है।"</p><p>वित्त मंत्रालय के आंकड़ों के मुताबिक, भारत की GDP अब 3.7 ट्रिलियन अमेरिकी डॉलर को पार कर गई है। विशेषज्ञों का मानना है कि अगले तीन वर्षों में भारत जर्मनी को भी पीछे छोड़ते हुए तीसरी सबसे बड़ी अर्थव्यवस्था बन सकता है।</p><p>इस वृद्धि में मुख्य योगदान डिजिटल अर्थव्यवस्था, मैन्युफैक्चरिंग सेक्टर और सेवा उद्योग का रहा है। UPI के माध्यम से हो रहे डिजिटल लेनदेन ने भारत को वैश्विक स्तर पर एक अलग पहचान दिलाई है।</p><p>विश्व बैंक के अनुसार, भारत की विकास दर 2025-26 में 7.2 प्रतिशत रहने का अनुमान है, जो दुनिया की प्रमुख अर्थव्यवस्थाओं में सबसे अधिक है।</p>',
            'content_en'       => "<p>New Delhi: India has achieved a historic milestone. According to the latest report by the International Monetary Fund (IMF), India has surpassed Japan to become the world's fourth largest economy.</p><p>Prime Minister Narendra Modi congratulated the nation on this achievement, saying it is the result of the hard work and dedication of 1.4 billion Indians. He stated, 'This is not just a number; it is the realization of every Indian's dream.'</p><p>According to Finance Ministry data, India's GDP has now crossed 3.7 trillion US dollars. Experts believe that within the next three years, India could also surpass Germany to become the third largest economy.</p><p>The digital economy, manufacturing sector, and service industry have been the main contributors to this growth. Digital transactions through UPI have given India a distinct identity at the global level.</p><p>According to the World Bank, India's growth rate is estimated to be 7.2 percent in 2025-26, the highest among major world economies.</p>",
        ]);

        foreach (['अर्थव्यवस्था', 'GDP', 'IMF', 'भारत', 'मोदी'] as $t) {
            $tag = Tag::firstOrCreate(['name' => $t, 'slug' => \Illuminate\Support\Str::slug($t)]);
            $a1->tags()->syncWithoutDetaching([$tag->id]);
        }

        $a2 = Article::create([
            'title_hi'         => 'टीम इंडिया ने रचा इतिहास: T20 विश्व कप फाइनल में ऑस्ट्रेलिया को 47 रनों से हराया',
            'title_en'         => 'Team India Creates History: Beats Australia by 47 Runs in T20 World Cup Final',
            'slug'             => 'india-t20-world-cup-final-beats-australia-2026',
            'category_id'      => 2,
            'author_id'        => $author2->id,
            'language'         => 'both',
            'status'           => 'published',
            'is_featured'      => true,
            'is_breaking'      => false,
            'featured_image'   => $img2,
            'published_at'     => now()->subHours(2),
            'excerpt_hi'       => 'रोहित शर्मा की कप्तानी में टीम इंडिया ने T20 विश्व कप 2026 का खिताब जीतकर इतिहास रचा। विराट कोहली ने 78 रनों की शानदार पारी खेली।',
            'excerpt_en'       => "Under Rohit Sharma's captaincy, Team India created history by winning the T20 World Cup 2026 title. Virat Kohli played a brilliant innings of 78 runs.",
            'content_hi'       => '<p>बारबाडोस: टीम इंडिया ने T20 विश्व कप 2026 का खिताब अपने नाम कर लिया। कप्तान रोहित शर्मा की अगुआई में भारत ने रोमांचक फाइनल मुकाबले में ऑस्ट्रेलिया को 47 रनों से करारी शिकस्त दी।</p><p>पहले बल्लेबाज़ी करते हुए भारत ने 20 ओवरों में 4 विकेट के नुकसान पर 186 रनों का विशाल स्कोर खड़ा किया। विराट कोहली ने 52 गेंदों पर 78 रनों की तूफानी पारी खेली जिसमें 6 चौके और 4 छक्के शामिल थे।</p><p>रोहित शर्मा ने 38 और हार्दिक पांड्या ने 32 रनों की उपयोगी पारी खेली। ऑस्ट्रेलिया की टीम 20 ओवरों में 139 रन ही बना सकी और भारत ने 47 रनों से जीत दर्ज की।</p><p>गेंदबाज़ी में जसप्रीत बुमराह सबसे सफल रहे। उन्होंने 4 ओवर में 18 रन देकर 3 विकेट लिए। अर्शदीप सिंह ने 2 और रवींद्र जडेजा ने 1 विकेट लेकर जीत में अहम भूमिका निभाई।</p><p>मैच के बाद कप्तान रोहित शर्मा ने कहा, "यह पूरी टीम की जीत है। हर खिलाड़ी ने बेहतरीन प्रदर्शन किया। भारतीय फैंस को यह खिताब समर्पित है।" विराट कोहली को प्लेयर ऑफ द मैच का पुरस्कार मिला।</p>',
            'content_en'       => "<p>Barbados: Team India has won the T20 World Cup 2026 title. Under captain Rohit Sharma's leadership, India defeated Australia by 47 runs in an exciting final match.</p><p>Batting first, India posted a massive total of 186 runs for the loss of 4 wickets in 20 overs. Virat Kohli played a blistering innings of 78 runs off 52 balls, including 6 fours and 4 sixes.</p><p>Rohit Sharma scored 38 and Hardik Pandya contributed a useful 32 runs. The Australian team could only manage 139 runs in 20 overs and India won by 47 runs.</p><p>In bowling, Jasprit Bumrah was the most successful, taking 3 wickets for 18 runs in 4 overs. Arshdeep Singh took 2 and Ravindra Jadeja claimed 1 wicket in the victory.</p><p>After the match, captain Rohit Sharma said, 'This is a win for the entire team. Every player performed brilliantly. This title is dedicated to the Indian fans.' Virat Kohli received the Player of the Match award.</p>",
        ]);

        foreach (['क्रिकेट', 'T20 विश्व कप', 'रोहित शर्मा', 'विराट कोहली', 'टीम इंडिया'] as $t) {
            $tag = Tag::firstOrCreate(['name' => $t, 'slug' => \Illuminate\Support\Str::slug($t)]);
            $a2->tags()->syncWithoutDetaching([$tag->id]);
        }

        echo "Created: Article 1 id={$a1->id}, Article 2 id={$a2->id}\n";
    }
}
