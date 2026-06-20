<?php
namespace App\Http\Controllers\Public;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Setting;

class PublicSettingsController extends Controller {
    use ApiResponse;

    private array $public = [
        'site_name','site_tagline','site_email','site_phone',
        'facebook','twitter','instagram','youtube','telegram',
        'ads_enabled','adsense_id','adsense_header','adsense_sidebar','adsense_article',
        'ga_id',
    ];

    public function index() {
        return $this->successResponse(
            Setting::whereIn('key', $this->public)->pluck('value', 'key')
        );
    }
}
