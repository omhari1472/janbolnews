<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller {
    use ApiResponse;

    private array $allowed = [
        'site_name','site_tagline','site_email','site_phone',
        'facebook','twitter','instagram','youtube','telegram',
        'signatory_name','signatory_title',
        'ads_enabled','adsense_id','adsense_header','adsense_sidebar','adsense_article',
        'ga_id',
        'font_hi','font_en',
    ];

    public function index() {
        return $this->successResponse(Setting::all()->pluck('value','key'));
    }

    public function update(Request $request) {
        foreach ($request->only($this->allowed) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return $this->successResponse(Setting::all()->pluck('value','key'), 'Settings saved');
    }
}
